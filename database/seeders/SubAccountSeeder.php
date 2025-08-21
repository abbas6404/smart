<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\AccountNumberHelper;

class SubAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing sub accounts first
        DB::table('sub_accounts')->delete();
        
        // Get user IDs for reference
        $userIds = DB::table('users')->pluck('id')->toArray();
        $packageIds = DB::table('packages')->pluck('id')->toArray();

        // Level 1: Root account (John)
        $johnId = DB::table('sub_accounts')->insertGetId([
            'name' => 'John Business Account 1',
            'user_id' => $userIds[0], // John Doe
            'account_number' => AccountNumberHelper::generateAccountNumber(),
            'referral_code' => AccountNumberHelper::generateReferralCode(),
            'active_package_id' => $packageIds[0], // Basic Package (100 Taka)
            'active_package_purcased_at' => now(),
            'referral_by_id' => null, // Root account
            'direct_referral_count' => 0,
            'generation_count' => 0,
            'total_balance' => 0.00,
            'withdrawal_limit' => 500.00,
            'total_withdrawal' => 0.00,
            'total_package_purchase' => 100.00,
            'total_deposit' => 0.00,
            'total_sponsor_commission' => 0.00,
            'total_generation_commission' => 0.00,
            'total_auto_income' => 0.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ Created Level 1: John (ID: {$johnId})");

        // Create 12 levels of accounts in a chain
        $totalAccounts = 1;
        $currentLevelAccounts = [$johnId]; // Start with John's ID
        
        for ($level = 2; $level <= 12; $level++) {
            $accountsInLevel = min(count($currentLevelAccounts) * 2, 3); // Max 3 accounts per level
            $newLevelAccounts = [];
            
            for ($i = 1; $i <= $accountsInLevel; $i++) {
                $totalAccounts++;
                
                // Select a sponsor from the previous level
                $sponsorIndex = ($i - 1) % count($currentLevelAccounts);
                $sponsorId = $currentLevelAccounts[$sponsorIndex];
                
                $newAccountId = DB::table('sub_accounts')->insertGetId([
                    'name' => "Level {$level} Account {$i}",
                    'user_id' => $userIds[($totalAccounts - 1) % count($userIds)], // Cycle through users
                    'account_number' => AccountNumberHelper::generateAccountNumber(),
                    'referral_code' => AccountNumberHelper::generateReferralCode(),
                    'active_package_id' => $packageIds[($totalAccounts - 1) % count($packageIds)], // Cycle through packages
                    'active_package_purcased_at' => now(),
                    'referral_by_id' => $sponsorId,
                    'direct_referral_count' => 0,
                    'generation_count' => 0,
                    'total_balance' => 0.00,
                    'withdrawal_limit' => 500.00,
                    'total_withdrawal' => 0.00,
                    'total_package_purchase' => 100.00,
                    'total_deposit' => 0.00,
                    'total_sponsor_commission' => 0.00,
                    'total_generation_commission' => 0.00,
                    'total_auto_income' => 0.00,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $newLevelAccounts[] = $newAccountId;
            }
            
            $currentLevelAccounts = $newLevelAccounts;
            $this->command->info("✅ Created Level {$level}: {$accountsInLevel} accounts");
        }

        // Update referral counts after insertion
        $this->updateReferralCounts();
        
        $this->command->info("✅ Created {$totalAccounts} sub accounts in 12 levels");
    }

    private function updateReferralCounts()
    {
        // Get all accounts with referrals
        $accounts = DB::table('sub_accounts')->get();
        
        foreach ($accounts as $account) {
            if ($account->referral_by_id) {
                // Count direct referrals for this account
                $directCount = DB::table('sub_accounts')
                    ->where('referral_by_id', $account->id)
                    ->count();
                
                // Update direct referral count
                DB::table('sub_accounts')
                    ->where('id', $account->id)
                    ->update(['direct_referral_count' => $directCount]);
            }
        }
        
        $this->command->info("✅ Updated referral counts for all accounts");
    }
}
