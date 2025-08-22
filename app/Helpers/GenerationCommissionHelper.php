<?php

namespace App\Helpers;

use App\Models\SubAccount;
use App\Models\SponsorRelationship;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Generation Commission Helper - Manages MLM generation-based commission system
 * 
 * DESIGN LOGIC:
 * =============
 * 
 * COMMISSION STRUCTURE:
 * --------------------
 * Sponsor Commission: 40% (from system_settings: sponsor_commission_rate)
 * - Goes directly to the immediate sponsor (referral_by_id)
 * 
 * Generation Commission: From level_commissions table
 * - Level 1: 1% (from level_commissions table)
 * - Level 2: 1% (from level_commissions table)
 * - Level 3: 1% (from level_commissions table)
 * - ... and so on
 * 
 * ELIGIBILITY REQUIREMENTS:
 * - Sponsor must have active package
 * - Sponsor must be in 'active' status
 * - Downline must have active package
 * - Downline must be in 'active' status
 * 
 * COMMISSION CALCULATION:
 * - Sponsor Commission: Package Amount × 40%
 * - Generation Commission: Package Amount × Level Rate (from table)
 * - Applied to eligible generations only
 * - Maximum generation depth configurable
 */
class GenerationCommissionHelper
{
    /**
     * Get commission rate for specific generation level from level_commissions table
     * 
     * @param int $generationLevel (1, 2, 3, 4, 5...)
     * @return float Commission rate as decimal (0.01 = 1%)
     */
    public static function getCommissionRate(int $generationLevel): float
    {
        $rate = DB::table('level_commissions')
            ->where('level', $generationLevel)
            ->where('is_active', true)
            ->value('commission_percentage');
            
        return $rate ? ($rate / 100) : 0.01; // Default to 1% if not found
    }

    /**
     * Get sponsor commission rate from system settings
     * 
     * @return float Sponsor commission rate as decimal (0.40 = 40%)
     */
    public static function getSponsorCommissionRate(): float
    {
        $rate = DB::table('system_settings')
            ->where('key', 'sponsor_commission_rate')
            ->value('value') ?? 40; // Default to 40% if not found
            
        return $rate / 100; // Convert percentage to decimal
    }

    /**
     * Get maximum generation depth for commission calculation
     * 
     * @return int Maximum generation level (default: 5)
     */
    public static function getMaxGenerationDepth(): int
    {
        return DB::table('system_settings')
            ->where('key', 'max_generation_levels')
            ->value('value') ?? 5; // Default to 5 generations
    }

    /**
     * Calculate commission for a specific generation level
     * 
     * @param float $packageAmount Package purchase amount
     * @param int $generationLevel Generation level (1, 2, 3...)
     * @return float Commission amount
     */
    public static function calculateCommission(float $packageAmount, int $generationLevel): float
    {
        $rate = self::getCommissionRate($generationLevel);
        return $packageAmount * $rate;
    }

    /**
     * Calculate sponsor commission
     * 
     * @param float $packageAmount Package purchase amount
     * @return float Sponsor commission amount
     */
    public static function calculateSponsorCommission(float $packageAmount): float
    {
        $rate = self::getSponsorCommissionRate();
        return $packageAmount * $rate;
    }

    /**
     * Get all eligible sponsors for generation commission
     * 
     * ELIGIBILITY CRITERIA:
     * 1. Must have active package (active_package_id IS NOT NULL)
     * 2. Must be in 'active' status
     * 3. Must have downline members
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getEligibleSponsors(): \Illuminate\Database\Eloquent\Collection
    {
        return SubAccount::where('active_package_id', '!=', null)
            ->where('status', 'active')
            ->whereHas('downline')
            ->orderBy('id')
            ->get();
    }

    /**
     * Get downline members for a specific sponsor up to max generation depth
     * 
     * @param int $sponsorId Sponsor's sub_account_id
     * @param int $maxDepth Maximum generation depth to check
     * @return array Multi-level downline structure
     */
    public static function getDownlineStructure(int $sponsorId, int $maxDepth = null): array
    {
        $maxDepth = $maxDepth ?? self::getMaxGenerationDepth();
        $downline = [];
        
        // Level 1: Direct referrals
        $level1 = SubAccount::where('referral_by_id', $sponsorId)
            ->where('status', 'active')
            ->where('active_package_id', '!=', null)
            ->get();
        
        $downline[1] = $level1;
        
        // Build subsequent levels
        for ($level = 2; $level <= $maxDepth; $level++) {
            $previousLevel = $downline[$level - 1] ?? collect();
            $currentLevel = collect();
            
            foreach ($previousLevel as $member) {
                $nextLevel = SubAccount::where('referral_by_id', $member->id)
                    ->where('status', 'active')
                    ->where('active_package_id', '!=', null)
                    ->get();
                
                $currentLevel = $currentLevel->merge($nextLevel);
            }
            
            if ($currentLevel->isEmpty()) {
                break; // No more levels
            }
            
            $downline[$level] = $currentLevel;
        }
        
        return $downline;
    }

    /**
     * Process package purchase commission (sponsor + generation)
     * 
     * COMMISSION FLOW:
     * 1. Calculate and distribute sponsor commission (40%)
     * 2. Calculate and distribute generation commission (from level_commissions table)
     * 3. Update account balances
     * 4. Create transaction records
     * 5. Update commission totals
     * 
     * @param int $purchaserId Sub account ID who purchased package
     * @param float $packageAmount Package purchase amount
     * @param int $packageId Package ID purchased
     * @return array Commission distribution results
     */
    public static function processPackagePurchaseCommission(
        int $purchaserId, 
        float $packageAmount, 
        int $packageId
    ): array {
        $purchaser = SubAccount::findOrFail($purchaserId);
        
        if (!$purchaser->referral_by_id) {
            return [
                'success' => false,
                'message' => 'No sponsor found for this account',
                'total_commission' => 0,
                'distributions' => []
            ];
        }
        
        $distributions = [];
        $totalCommission = 0;
        
        // 1. PROCESS SPONSOR COMMISSION (40%)
        $sponsor = SubAccount::find($purchaser->referral_by_id);
        if ($sponsor && $sponsor->status === 'active' && $sponsor->active_package_id) {
            $sponsorCommission = self::calculateSponsorCommission($packageAmount);
            
            // Update sponsor's balance and commission totals
            $sponsor->increment('total_sponsor_commission', $sponsorCommission);
            $sponsor->increment('total_balance', $sponsorCommission);
            $sponsor->update(['last_balance_update_at' => now()]);
            
            // Get current balance before transaction
            $balanceBefore = $sponsor->total_balance;
            $balanceAfter = $balanceBefore + $sponsorCommission;
            
            // Create transaction record for sponsor commission
            DB::table('transactions')->insert([
                'sub_account_id' => $sponsor->id,
                'type' => 'sponsor_commission',
                'amount' => $sponsorCommission,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => "Sponsor commission from package purchase #{$packageId}",
                'status' => 'approved',
                'system_ip' => request()->ip(),
                'system_user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Record sponsor commission distribution
            $distributions[] = [
                'sponsor_id' => $sponsor->id,
                'sponsor_name' => $sponsor->name,
                'commission_type' => 'sponsor',
                'generation_level' => 0, // 0 for sponsor commission
                'commission_rate' => self::getSponsorCommissionRate() * 100, // Convert to percentage
                'commission_amount' => $sponsorCommission,
                'package_amount' => $packageAmount,
                'package_id' => $packageId,
            ];
            
            $totalCommission += $sponsorCommission;
        }
        
        // 2. PROCESS GENERATION COMMISSION (from level_commissions table)
        $maxDepth = self::getMaxGenerationDepth();
        $downline = self::getDownlineStructure($purchaser->referral_by_id, $maxDepth);
        
        foreach ($downline as $level => $members) {
            if ($members->isEmpty()) {
                continue;
            }
            
            // Find the sponsor at this level
            $sponsor = self::findSponsorAtLevel($purchaserId, $level);
            
            if (!$sponsor) {
                continue;
            }
            
            // Calculate generation commission for this level
            $commissionRate = self::getCommissionRate($level);
            $commissionAmount = $packageAmount * $commissionRate;
            
            // Update sponsor's balance and commission totals
            $sponsor->increment('total_generation_commission', $commissionAmount);
            $sponsor->increment('total_balance', $commissionAmount);
            $sponsor->update(['last_balance_update_at' => now()]);
            
            // Get current balance before transaction
            $balanceBefore = $sponsor->total_balance;
            $balanceAfter = $balanceBefore + $commissionAmount;
            
            // Create transaction record for generation commission
            DB::table('transactions')->insert([
                'sub_account_id' => $sponsor->id,
                'type' => 'generation_commission',
                'amount' => $commissionAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => "Generation {$level} commission from package purchase #{$packageId}",
                'status' => 'approved',
                'system_ip' => request()->ip(),
                'system_user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Record generation commission distribution
            $distributions[] = [
                'sponsor_id' => $sponsor->id,
                'sponsor_name' => $sponsor->name,
                'commission_type' => 'generation',
                'generation_level' => $level,
                'commission_rate' => $commissionRate * 100, // Convert to percentage
                'commission_amount' => $commissionAmount,
                'package_amount' => $packageAmount,
                'package_id' => $packageId,
            ];
            
            $totalCommission += $commissionAmount;
        }
        
        return [
            'success' => true,
            'message' => "Commission processed: Sponsor + {$maxDepth} generation levels",
            'total_commission' => $totalCommission,
            'distributions' => $distributions,
            'max_depth' => $maxDepth,
            'sponsor_commission' => $distributions[0]['commission_amount'] ?? 0,
            'generation_commission' => $totalCommission - ($distributions[0]['commission_amount'] ?? 0),
        ];
    }

    /**
     * Find sponsor at specific generation level for a given account
     * 
     * @param int $accountId Account to find sponsor for
     * @param int $level Generation level (1 = direct, 2 = 2nd level, etc.)
     * @return SubAccount|null
     */
    public static function findSponsorAtLevel(int $accountId, int $level): ?SubAccount
    {
        if ($level < 1) {
            return null;
        }
        
        $currentAccount = SubAccount::find($accountId);
        if (!$currentAccount) {
            return null;
        }
        
        // Navigate up the referral chain
        for ($i = 1; $i <= $level; $i++) {
            if (!$currentAccount->referral_by_id) {
                return null; // No more sponsors up the chain
            }
            
            $currentAccount = SubAccount::find($currentAccount->referral_by_id);
            if (!$currentAccount || $currentAccount->status !== 'active' || !$currentAccount->active_package_id) {
                return null; // Sponsor not eligible
            }
        }
        
        return $currentAccount;
    }

    /**
     * Get commission statistics for a sponsor
     * 
     * @param int $sponsorId Sponsor's sub account ID
     * @return array Commission statistics
     */
    public static function getSponsorCommissionStats(int $sponsorId): array
    {
        $sponsor = SubAccount::findOrFail($sponsorId);
        $maxDepth = self::getMaxGenerationDepth();
        $downline = self::getDownlineStructure($sponsorId, $maxDepth);
        
        $stats = [
            'sponsor_id' => $sponsorId,
            'sponsor_name' => $sponsor->name,
            'total_sponsor_commission' => $sponsor->total_sponsor_commission,
            'total_generation_commission' => $sponsor->total_generation_commission,
            'max_generation_depth' => $maxDepth,
            'levels' => [],
        ];
        
        foreach ($downline as $level => $members) {
            $stats['levels'][$level] = [
                'member_count' => $members->count(),
                'commission_rate' => self::getCommissionRate($level) * 100,
                'eligible_members' => $members->where('active_package_id', '!=', null)
                    ->where('status', 'active')
                    ->count(),
            ];
        }
        
        return $stats;
    }

    /**
     * Get all commission transactions for an account
     * 
     * @param int $accountId Sub account ID
     * @param int $limit Number of transactions to return
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCommissionTransactions(int $accountId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return DB::table('transactions')
            ->where('sub_account_id', $accountId)
            ->whereIn('type', ['sponsor_commission', 'generation_commission'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all active commission rates from level_commissions table
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveCommissionRates(): \Illuminate\Database\Eloquent\Collection
    {
        return DB::table('level_commissions')
            ->where('is_active', true)
            ->orderBy('level')
            ->get();
    }
}
