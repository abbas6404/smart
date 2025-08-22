<?php

namespace App\Http\Controllers;

use App\Helpers\PurchaseReferralHelper;
use App\Console\Commands\ProcessAutoBoardDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    /**
     * Update purchase referral counts via cron URL
     * This endpoint can be called by external cron services
     */
    public function updatePurchaseReferrals(Request $request)
    {
        try {
            Log::info('Cron: Purchase referral update triggered via URL from IP: ' . $request->ip());
            
            $result = PurchaseReferralHelper::updateAllAccounts();
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase referral counts updated successfully',
                    'timestamp' => now()->toISOString(),
                    'data' => [
                        'total_accounts' => $result['total_accounts'],
                        'updated_accounts' => $result['updated_accounts'],
                        'execution_time_ms' => $result['execution_time_ms'],
                        'errors_count' => count($result['errors'])
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update purchase referral counts',
                    'error' => $result['error'],
                    'timestamp' => now()->toISOString()
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Cron: Purchase referral update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
    
    /**
     * Process AutoBoard distribution via cron URL
     * This endpoint can be called by external cron services
     */
    public function processAutoBoard(Request $request)
    {
        try {
            Log::info('Cron: AutoBoard distribution triggered via URL from IP: ' . $request->ip());
            
            // Call the AutoBoardHelper directly instead of the command
            // This avoids console output issues when called from web
            $previousDayBoard = \App\Helpers\AutoBoardHelper::getPreviousDayBoard();
            
            if (!$previousDayBoard) {
                return response()->json([
                    'success' => false,
                    'message' => 'No previous day board found for processing',
                    'timestamp' => now()->toISOString(),
                    'data' => [
                        'status' => 'no_board',
                        'message' => 'No AutoBoard found for previous day'
                    ]
                ], 200);
            }
            
            if ($previousDayBoard->status !== 'collection') {
                return response()->json([
                    'success' => false,
                    'message' => 'Board not ready for processing',
                    'timestamp' => now()->toISOString(),
                    'data' => [
                        'status' => 'not_ready',
                        'board_id' => $previousDayBoard->id,
                        'current_status' => $previousDayBoard->status,
                        'message' => 'Board must be in collection status'
                    ]
                ], 200);
            }
            
            if ($previousDayBoard->today_collection_amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Board has no collection amount',
                    'timestamp' => now()->toISOString(),
                    'data' => [
                        'status' => 'no_collection',
                        'board_id' => $previousDayBoard->id,
                        'collection_amount' => $previousDayBoard->today_collection_amount,
                        'message' => 'Board has no collection amount to distribute'
                    ]
                ], 200);
            }
            
            // Process distribution using the helper directly
            $result = \App\Helpers\AutoBoardHelper::distributeTodayCollection($previousDayBoard->id);
            
            // Log success
            \Illuminate\Support\Facades\Log::info("AutoBoard: Distribution completed via cron URL for board ID {$previousDayBoard->id}", [
                'board_id' => $previousDayBoard->id,
                'date' => $previousDayBoard->distribution_date,
                'total_distributed' => $result['total_distributed'],
                'eligible_accounts' => $result['total_eligible']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'AutoBoard distribution processed successfully',
                'timestamp' => now()->toISOString(),
                'data' => [
                    'status' => 'completed',
                    'message' => 'AutoBoard distribution cycle completed',
                    'board_id' => $previousDayBoard->id,
                    'distribution_date' => $previousDayBoard->distribution_date,
                    'collection_amount' => $previousDayBoard->today_collection_amount,
                    'contributors' => $previousDayBoard->today_contributors,
                    'total_eligible_accounts' => $result['total_eligible'],
                    'total_distributed' => $result['total_distributed'],
                    'per_account_amount' => $result['per_account_amount']
                ]
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Cron: AutoBoard distribution error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'AutoBoard distribution failed',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
    
    /**
     * Health check endpoint for cron monitoring
     */
    public function health(Request $request)
    {
        try {
            $stats = PurchaseReferralHelper::getStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase Referral Service is healthy',
                'timestamp' => now()->toISOString(),
                'data' => [
                    'total_accounts' => $stats['total_accounts'],
                    'accounts_with_referrals' => $stats['accounts_with_referrals'],
                    'last_check' => now()->format('Y-m-d H:i:s')
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Referral Service is unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 503);
        }
    }
}
