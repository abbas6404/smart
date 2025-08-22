<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\AutoBoardHelper;
use Illuminate\Support\Facades\Log;

class ProcessAutoBoardDistribution extends Command
{
    protected $signature = 'auto-board:process';
    protected $description = 'Process AutoBoard distribution for previous day (cron job)';

    public function handle()
    {
        $this->info('🕛 Processing AutoBoard distribution (cron job)...');
        
        try {
            // Get previous day's board for processing
            $previousDayBoard = AutoBoardHelper::getPreviousDayBoard();
            
            if (!$previousDayBoard) {
                $this->warn('⚠️ No previous day board found for processing');
                Log::info('AutoBoard: No previous day board found for processing');
                return;
            }
            
            if ($previousDayBoard->status !== 'collection') {
                $this->warn("⚠️ Previous day board (ID: {$previousDayBoard->id}) is not in collection status");
                Log::info("AutoBoard: Board ID {$previousDayBoard->id} not ready for processing");
                return;
            }
            
            if ($previousDayBoard->today_collection_amount <= 0) {
                $this->warn("⚠️ Previous day board (ID: {$previousDayBoard->id}) has no collection amount");
                Log::info("AutoBoard: Board ID {$previousDayBoard->id} has no collection amount");
                return;
            }
            
            $this->info("📊 Processing board:");
            $this->line("   Date: {$previousDayBoard->distribution_date}");
            $this->line("   Collection: {$previousDayBoard->today_collection_amount}");
            $this->line("   Contributors: {$previousDayBoard->today_contributors}");
            
            // Process distribution
            $result = AutoBoardHelper::distributeTodayCollection($previousDayBoard->id);
            
            $this->info("✅ Distribution completed successfully!");
            $this->line("   Total eligible accounts: {$result['total_eligible']}");
            $this->line("   Total distributed: {$result['total_distributed']}");
            $this->line("   Per account amount: {$result['per_account_amount']}");
            
            // Log success
            Log::info("AutoBoard: Distribution completed for board ID {$previousDayBoard->id}", [
                'board_id' => $previousDayBoard->id,
                'date' => $previousDayBoard->distribution_date,
                'total_distributed' => $result['total_distributed'],
                'eligible_accounts' => $result['total_eligible']
            ]);
            
        } catch (\Exception $e) {
            $this->error("❌ Distribution failed: " . $e->getMessage());
            
            // Log error
            Log::error("AutoBoard: Distribution failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}
