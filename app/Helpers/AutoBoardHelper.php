<?php

namespace App\Helpers;

use App\Models\AutoBoard;
use App\Models\SubAccount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * AutoBoard Helper - Manages daily auto income distribution system
 * 
 * DESIGN LOGIC:
 * =============
 * 
 * DAILY FLOW:
 * -----------
 * Day 1 (Today): 00:00 - 23:59
 * - Status: 'collection' (collecting contributions)
 * - Package purchases → Update today_collection_amount
 * - Contributions accumulate throughout the day
 * 
 * Day 2 (Next day at 12:00 AM):
 * - AutoBoard runs automatically at 00:00
 * - Processes Day 1's collection (status: 'collection' → 'distributed')
 * - Creates new Day 2 board (status: 'collection')
 * 
 * TIMING:
 * - Collection Period: Full day (00:00 - 23:59)
 * - Processing Time: Next day at 12:00 AM (midnight)
 * - Board Status Changes: 'collection' → 'distributed'
 * 
 * ELIGIBILITY REQUIREMENTS:
 * - purchase_referral_count >= system_setting(auto_income_eligibility)
 * - status = 'active'
 * - active_package_id IS NOT NULL (has purchased package)
 * 
 * DISTRIBUTION LOGIC:
 * - Equal distribution among all eligible accounts
 * - Higher package referral count gets priority in ordering
 * - Creates complete audit trail (distributions + transactions)
 */
class AutoBoardHelper
{
    /**
     * Get eligible accounts for auto income distribution
     * Based on auto_income_eligibility system setting and purchase_referral_count
     * 
     * ELIGIBILITY RULES:
     * 1. Must have sufficient package referrals (from system settings)
     * 2. Account must be active
     * 3. Must have an active package purchased
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getEligibleAccounts(): \Illuminate\Database\Eloquent\Collection
    {
        // Get the minimum referral count required from system settings
        $minReferralsRequired = DB::table('system_settings')
            ->where('key', 'auto_income_eligibility')
            ->value('value') ?? 30; // Default to 30 if not found

        return SubAccount::where('purchase_referral_count', '>=', $minReferralsRequired)
            ->where('status', 'active')
            ->where('active_package_id', '!=', null)
            ->orderBy('purchase_referral_count', 'desc') // Highest package referrals first
            ->get();
    }

    /**
     * Distribute today's collection among eligible accounts
     * 
     * PROCESS:
     * 1. Validate board is in 'collection' status (ready for distribution)
     * 2. Get all eligible accounts
     * 3. Calculate equal distribution amount per account
     * 4. Create distribution records
     * 5. Update account balances
     * 6. Create transaction logs
     * 7. Update board status to 'distributed'
     * 
     * @param int $autoBoardId
     * @return array
     */
    public static function distributeTodayCollection(int $autoBoardId): array
    {
        $autoBoard = AutoBoard::findOrFail($autoBoardId);
        
        // Only process boards in collection status (previous day's board)
        if ($autoBoard->status !== 'collection') {
            throw new \Exception('AutoBoard is not in collection status');
        }

        $eligibleAccounts = self::getEligibleAccounts();
        
        if ($eligibleAccounts->isEmpty()) {
            throw new \Exception('No eligible accounts found for distribution');
        }

        // Equal distribution: total collection ÷ number of eligible accounts
        $todayCollection = $autoBoard->today_collection_amount;
        $distributionPerAccount = $todayCollection / $eligibleAccounts->count();
        
        $distributionLog = [];
        $totalDistributed = 0;

        foreach ($eligibleAccounts as $account) {
            // Create distribution record for audit trail
            DB::table('auto_board_distributions')->insert([
                'auto_board_id' => $autoBoardId,
                'sub_account_id' => $account->id,
                'amount' => $distributionPerAccount,
                'purchase_referral_count' => $account->purchase_referral_count,
                'notes' => 'Auto income distribution based on package referral eligibility',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update sub account balance and auto income
            $account->increment('total_auto_income', $distributionPerAccount);
            $account->increment('total_balance', $distributionPerAccount);
            $account->update(['last_balance_update_at' => now()]);

            // Get current balance before transaction
            $balanceBefore = $account->total_balance;
            $balanceAfter = $balanceBefore + $distributionPerAccount;
            
            // Create transaction record for financial tracking
            DB::table('transactions')->insert([
                'sub_account_id' => $account->id,
                'type' => 'auto_income',
                'amount' => $distributionPerAccount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => 'Auto income distribution from board #' . $autoBoardId,
                'status' => 'approved',
                'system_ip' => request()->ip(),
                'system_user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Build distribution log for board records
            $distributionLog[] = [
                'account_id' => $account->id,
                'account_name' => $account->name,
                'referral_count' => $account->purchase_referral_count,
                'amount' => $distributionPerAccount,
            ];

            $totalDistributed += $distributionPerAccount;
        }

        // Update auto board: mark as distributed and log results
        $autoBoard->update([
            'total_distributed' => DB::raw('total_distributed + ' . $eligibleAccounts->count()),
            'today_distributed' => $eligibleAccounts->count(),
            'today_per_account_distributed' => $distributionPerAccount,
            'distributed_date' => now(),
            'status' => 'distributed', // Change from 'collection' to 'distributed'
            'distribution_log' => json_encode($distributionLog),
        ]);

        // Create new board for today (ready to collect contributions)
        self::createTodayBoard();

        return [
            'success' => true,
            'total_eligible' => $eligibleAccounts->count(),
            'total_distributed' => $totalDistributed,
            'per_account_amount' => $distributionPerAccount,
            'distribution_log' => $distributionLog,
        ];
    }

    /**
     * Get today's auto board or create new one
     * 
      * BOARD CREATION LOGIC:
 * - Creates new board for current day if doesn't exist
 * - Status starts as 'collection' (ready to collect contributions)
 * - Will be processed tomorrow at 12:00 AM
     * 
     * @return AutoBoard
     */
    public static function getTodayBoard(): AutoBoard
    {
        $today = Carbon::today()->format('Y-m-d');
        
        return AutoBoard::firstOrCreate(
            ['distribution_date' => $today],
            [
                'status' => 'collection', // Ready to collect today's contributions
                'today_collection_amount' => 0,
                'today_contributors' => 0,
            ]
        );
    }

    /**
     * Create new board for today (called after processing previous day)
     * 
     * MIDNIGHT BOARD CREATION:
     * - Creates new board for current day
     * - Status: 'collection' (ready to collect contributions)
     * - Will be processed tomorrow at midnight
     * 
     * @return AutoBoard
     */
    public static function createTodayBoard(): AutoBoard
    {
        $today = Carbon::today()->format('Y-m-d');
        
        // Check if today's board already exists
        $existingBoard = AutoBoard::where('distribution_date', $today)->first();
        
        if ($existingBoard) {
            // Board already exists, return it
            return $existingBoard;
        }
        
        // Create new board for today
        return AutoBoard::create([
            'total_collection_amount' => 0,
            'total_contributors' => 0,
            'total_distributed' => 0,
            'today_collection_amount' => 0,
            'today_contributors' => 0,
            'today_distributed' => 0,
            'today_per_account_distributed' => 0,
            'distribution_date' => $today,
            'distributed_date' => null,
            'status' => 'collection', // Ready to collect today's contributions
            'distribution_log' => json_encode([
                'message' => 'New board created for today',
                'created_at' => now()->toDateTimeString(),
                'status' => 'ready_for_collection'
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get previous day's board for processing at midnight
     * 
     * MIDNIGHT PROCESSING LOGIC:
     * - At 12:00 AM, process the previous day's board
     * - Previous day board should have status 'collection' (ready for distribution)
     * - After processing, status changes to 'distributed'
     * 
     * @return AutoBoard|null
     */
    public static function getPreviousDayBoard(): ?AutoBoard
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        
        return AutoBoard::where('distribution_date', $yesterday)
            ->where('status', 'collection')
            ->first();
    }

    /**
     * Check if previous day's board is ready for distribution
     * 
     * READY CONDITIONS:
     * - Board status = 'collection' (has collected contributions)
     * - today_collection_amount > 0 (has money to distribute)
     * - Board date = yesterday (ready for midnight processing)
     * 
     * @return bool
     */
    public static function isPreviousDayReadyForDistribution(): bool
    {
        $previousDayBoard = self::getPreviousDayBoard();
        return $previousDayBoard && $previousDayBoard->today_collection_amount > 0;
    }

    /**
     * Check if today's board is ready for distribution
     * 
     * READY CONDITIONS:
     * - Board status = 'collection' (has collected contributions)
     * - today_collection_amount > 0 (has money to distribute)
     * 
     * NOTE: This method checks CURRENT day's board
     * For processing, use previous day's board (status = 'collection')
     * 
     * @return bool
     */
    public static function isReadyForDistribution(): bool
    {
        $todayBoard = self::getTodayBoard();
        return $todayBoard->status === 'collection' && $todayBoard->today_collection_amount > 0;
    }

    /**
     * Get distribution statistics for today
     * 
     * STATS INCLUDED:
     * - Board ID and collection amount
     * - Number of contributors
     * - Number of eligible accounts
     * - Current status and distribution readiness
     * 
     * @return array
     */
    public static function getTodayStats(): array
    {
        $todayBoard = self::getTodayBoard();
        $eligibleAccounts = self::getEligibleAccounts();
        
        return [
            'board_id' => $todayBoard->id,
            'collection_amount' => $todayBoard->today_collection_amount,
            'contributors' => $todayBoard->today_contributors,
            'eligible_accounts' => $eligibleAccounts->count(),
            'status' => $todayBoard->status,
            'ready_for_distribution' => self::isReadyForDistribution(),
        ];
    }
}
