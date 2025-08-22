<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\PurchaseReferralHelper;

class UpdatePurchaseReferralCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrals:update-purchase-counts {--account-id= : Update specific account ID} {--validate : Validate referral relationships} {--stats : Show statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update purchase referral counts for all accounts or specific account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Purchase Referral Count Update Tool');
        $this->line('=====================================');
        
        // Show statistics if requested
        if ($this->option('stats')) {
            $this->showStatistics();
            return;
        }
        
        // Validate relationships if requested
        if ($this->option('validate')) {
            $this->validateRelationships();
            return;
        }
        
        // Update specific account if ID provided
        if ($accountId = $this->option('account-id')) {
            $this->updateSingleAccount($accountId);
            return;
        }
        
        // Update all accounts
        $this->updateAllAccounts();
    }
    
    /**
     * Update all accounts
     */
    private function updateAllAccounts()
    {
        $this->info('📊 Updating purchase referral counts for ALL accounts...');
        $this->line('This may take a while depending on the number of accounts.');
        
        if (!$this->confirm('Do you want to continue?')) {
            $this->warn('Operation cancelled.');
            return;
        }
        
        $this->line('⏳ Processing...');
        
        $result = PurchaseReferralHelper::updateAllAccounts();
        
        if ($result['success']) {
            $this->info('✅ Update completed successfully!');
            $this->line('');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Accounts', $result['total_accounts']],
                    ['Updated Accounts', $result['updated_accounts']],
                    ['Execution Time', $result['execution_time_ms'] . ' ms'],
                    ['Errors', count($result['errors'])]
                ]
            );
            
            if (!empty($result['errors'])) {
                $this->warn('⚠️ Some accounts had errors:');
                foreach ($result['errors'] as $error) {
                    $this->line("   Account ID {$error['account_id']}: {$error['error']}");
                }
            }
        } else {
            $this->error('❌ Update failed: ' . $result['error']);
        }
    }
    
    /**
     * Update single account
     */
    private function updateSingleAccount($accountId)
    {
        $this->info("📊 Updating purchase referral count for account ID: {$accountId}");
        
        $result = PurchaseReferralHelper::updateSingleAccount($accountId);
        
        if ($result['success']) {
            if ($result['updated']) {
                $this->info("✅ Account updated successfully!");
                $this->line("   Old count: {$result['old_count']}");
                $this->line("   New count: {$result['new_count']}");
            } else {
                $this->info("ℹ️ Account count unchanged");
                $this->line("   Current count: {$result['new_count']}");
            }
        } else {
            $this->error("❌ Update failed: {$result['error']}");
        }
    }
    
    /**
     * Show statistics
     */
    private function showStatistics()
    {
        $this->info('📈 Purchase Referral Statistics');
        $this->line('==============================');
        
        $stats = PurchaseReferralHelper::getStatistics();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Accounts', $stats['total_accounts']],
                ['Accounts with Referrals', $stats['accounts_with_referrals']],
                ['Accounts without Referrals', $stats['accounts_without_referrals']],
                ['Total Referrals', $stats['total_referrals']],
                ['Average Referrals', $stats['average_referrals']]
            ]
        );
        
        if (!empty($stats['referral_distribution'])) {
            $this->line('');
            $this->info('Referral Distribution:');
            $this->table(
                ['Referral Range', 'Account Count'],
                $stats['referral_distribution']->map(function ($item) {
                    return [$item->referral_range, $item->account_count];
                })->toArray()
            );
        }
    }
    
    /**
     * Validate relationships
     */
    private function validateRelationships()
    {
        $this->info('🔍 Validating Referral Relationships');
        $this->line('===================================');
        
        $validation = PurchaseReferralHelper::validateReferralRelationships();
        
        if ($validation['valid']) {
            $this->info('✅ All referral relationships are valid!');
        } else {
            $this->error("❌ Found {$validation['issue_count']} issues:");
            
            foreach ($validation['issues'] as $issue) {
                $this->line('');
                $this->warn("⚠️ {$issue['message']}");
                $this->line("   Type: {$issue['type']}");
                $this->line("   Count: {$issue['count']}");
                $this->line("   Account IDs: " . implode(', ', array_slice($issue['account_ids'], 0, 10)));
                
                if (count($issue['account_ids']) > 10) {
                    $this->line("   ... and " . (count($issue['account_ids']) - 10) . " more");
                }
            }
        }
    }
}
