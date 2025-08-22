<?php

namespace App\Helpers;

use App\Models\SubAccount;
use App\Models\PackagePurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Purchase Referral Helper - Updates purchase referral counts for all accounts
 * 
 * PURPOSE:
 * - Scans all sub accounts
 * - Counts direct referrals that have active packages and active status
 * - Updates purchase_referral_count field for each account
 * 
 * ELIGIBILITY CRITERIA FOR REFERRALS:
 * - Must be a direct referral of the account
 * - Must have an active_package_id (has purchased a package)
 * - Must have status = 'active'
 */
class PurchaseReferralHelper
{
    /**
     * Update purchase referral count for all accounts
     * 
     * PROCESS:
     * 1. Get all sub accounts
     * 2. For each account, count eligible direct referrals
     * 3. Update purchase_referral_count field
     * 4. Log the results
     * 
     * @return array
     */
    public static function updateAllAccounts(): array
    {
        $startTime = microtime(true);
        $totalAccounts = 0;
        $updatedAccounts = 0;
        $errors = [];
        
        try {
            // Get all sub accounts
            $subAccounts = SubAccount::all();
            $totalAccounts = $subAccounts->count();
            
            Log::info("PurchaseReferralHelper: Starting update for {$totalAccounts} accounts");
            
            foreach ($subAccounts as $account) {
                try {
                    $oldCount = $account->purchase_referral_count ?? 0;
                    $newCount = self::calculatePurchaseReferralCount($account->id);
                    
                    // Update the account if count changed
                    if ($oldCount !== $newCount) {
                        $account->update(['purchase_referral_count' => $newCount]);
                        $updatedAccounts++;
                        
                        Log::info("PurchaseReferralHelper: Account ID {$account->id} updated from {$oldCount} to {$newCount} referrals");
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'account_id' => $account->id,
                        'error' => $e->getMessage()
                    ];
                    Log::error("PurchaseReferralHelper: Error updating account ID {$account->id}: " . $e->getMessage());
                }
            }
            
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $result = [
                'success' => true,
                'total_accounts' => $totalAccounts,
                'updated_accounts' => $updatedAccounts,
                'execution_time_ms' => $executionTime,
                'errors' => $errors
            ];
            
            Log::info("PurchaseReferralHelper: Update completed", $result);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("PurchaseReferralHelper: Fatal error: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'total_accounts' => $totalAccounts,
                'updated_accounts' => $updatedAccounts,
                'errors' => $errors
            ];
        }
    }
    
    /**
     * Calculate purchase referral count for a specific account
     * 
     * ELIGIBILITY RULES:
     * 1. Must be a direct referral (sponsored by this account)
     * 2. Must have active_package_id (has purchased a package)
     * 3. Must have status = 'active'
     * 
     * @param int $accountId
     * @return int
     */
    public static function calculatePurchaseReferralCount(int $accountId): int
    {
        return SubAccount::where('referral_by_id', $accountId)
            ->where('active_package_id', '!=', null)
            ->where('status', 'active')
            ->count();
    }
    
    /**
     * Update purchase referral count for a specific account
     * 
     * @param int $accountId
     * @return array
     */
    public static function updateSingleAccount(int $accountId): array
    {
        try {
            $account = SubAccount::findOrFail($accountId);
            $oldCount = $account->purchase_referral_count ?? 0;
            $newCount = self::calculatePurchaseReferralCount($accountId);
            
            if ($oldCount !== $newCount) {
                $account->update(['purchase_referral_count' => $newCount]);
                
                Log::info("PurchaseReferralHelper: Single account ID {$accountId} updated from {$oldCount} to {$newCount} referrals");
                
                return [
                    'success' => true,
                    'account_id' => $accountId,
                    'old_count' => $oldCount,
                    'new_count' => $newCount,
                    'updated' => true
                ];
            }
            
            return [
                'success' => true,
                'account_id' => $accountId,
                'old_count' => $oldCount,
                'new_count' => $newCount,
                'updated' => false
            ];
            
        } catch (\Exception $e) {
            Log::error("PurchaseReferralHelper: Error updating single account ID {$accountId}: " . $e->getMessage());
            
            return [
                'success' => false,
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get statistics about purchase referral counts
     * 
     * @return array
     */
    public static function getStatistics(): array
    {
        $totalAccounts = SubAccount::count();
        $accountsWithReferrals = SubAccount::where('purchase_referral_count', '>', 0)->count();
        $totalReferrals = SubAccount::sum('purchase_referral_count');
        
        $referralDistribution = SubAccount::selectRaw('
                CASE 
                    WHEN purchase_referral_count = 0 THEN "0 referrals"
                    WHEN purchase_referral_count BETWEEN 1 AND 5 THEN "1-5 referrals"
                    WHEN purchase_referral_count BETWEEN 6 AND 10 THEN "6-10 referrals"
                    WHEN purchase_referral_count BETWEEN 11 AND 20 THEN "11-20 referrals"
                    WHEN purchase_referral_count BETWEEN 21 AND 50 THEN "21-50 referrals"
                    ELSE "50+ referrals"
                END as referral_range,
                COUNT(*) as account_count
            ')
            ->groupBy('referral_range')
            ->orderBy('referral_range')
            ->get();
        
        return [
            'total_accounts' => $totalAccounts,
            'accounts_with_referrals' => $accountsWithReferrals,
            'accounts_without_referrals' => $totalAccounts - $accountsWithReferrals,
            'total_referrals' => $totalReferrals,
            'average_referrals' => $totalAccounts > 0 ? round($totalReferrals / $totalAccounts, 2) : 0,
            'referral_distribution' => $referralDistribution
        ];
    }
    
    /**
     * Validate referral relationships
     * 
     * CHECKS:
     * - All accounts have valid sponsor relationships
     * - All active packages exist
     * 
     * @return array
     */
    public static function validateReferralRelationships(): array
    {
        $issues = [];
        
        // Check for accounts with invalid referral_by IDs
        $allReferralByIds = SubAccount::whereNotNull('referral_by_id')->pluck('referral_by_id')->unique();
        $existingAccountIds = SubAccount::pluck('id')->toArray();
        $invalidReferralByIds = $allReferralByIds->diff($existingAccountIds);
        
        if ($invalidReferralByIds->isNotEmpty()) {
            $issues[] = [
                'type' => 'invalid_referral_by',
                'message' => 'Found accounts with invalid referral_by IDs',
                'count' => $invalidReferralByIds->count(),
                'account_ids' => $invalidReferralByIds->toArray()
            ];
        }
        
        // Check for accounts with invalid active package IDs
        $allPackageIds = SubAccount::whereNotNull('active_package_id')->pluck('active_package_id')->unique();
        $existingPackageIds = DB::table('packages')->pluck('id')->toArray();
        $invalidPackageIds = $allPackageIds->diff($existingPackageIds);
        
        if ($invalidPackageIds->isNotEmpty()) {
            $issues[] = [
                'type' => 'invalid_package',
                'message' => 'Found accounts with invalid active package IDs',
                'count' => $invalidPackageIds->count(),
                'account_ids' => $invalidPackageIds->toArray()
            ];
        }
        
        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'issue_count' => count($issues)
        ];
    }
}
