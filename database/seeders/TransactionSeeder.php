<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sub account IDs
        $subAccountIds = DB::table('sub_accounts')->pluck('id')->toArray();
        
        if (empty($subAccountIds)) {
            return; // Skip if no sub accounts
        }

        $transactions = [
            // Package purchase transactions
            [
                'sub_account_id' => $subAccountIds[0] ?? 1, // John
                'type' => 'package_purchase',
                'amount' => 100.00,
                'balance_before' => 0.00,
                'balance_after' => -100.00,
                'description' => 'Basic Package purchase - 100 Taka',
                'status' => 'approved',
                'system_ip' => '192.168.1.100',
                'system_user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode(['package' => 'Basic Package', 'package_id' => 1]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_account_id' => $subAccountIds[1] ?? 2, // Jane
                'type' => 'package_purchase',
                'amount' => 200.00,
                'balance_before' => 0.00,
                'balance_after' => -200.00,
                'description' => 'Starter Package purchase - 200 Taka',
                'status' => 'approved',
                'system_ip' => '192.168.1.101',
                'system_user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode(['package' => 'Starter Package', 'package_id' => 2]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_account_id' => $subAccountIds[2] ?? 3, // Mike
                'type' => 'package_purchase',
                'amount' => 500.00,
                'balance_before' => 0.00,
                'balance_after' => -500.00,
                'description' => 'Standard Package purchase - 500 Taka',
                'status' => 'approved',
                'system_ip' => '192.168.1.102',
                'system_user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode(['package' => 'Standard Package', 'package_id' => 3]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_account_id' => $subAccountIds[3] ?? 4, // Sarah
                'type' => 'package_purchase',
                'amount' => 200.00,
                'balance_before' => 0.00,
                'balance_after' => -200.00,
                'description' => 'Starter Package purchase - 200 Taka',
                'status' => 'approved',
                'system_ip' => '192.168.1.103',
                'system_user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode(['package' => 'Starter Package', 'package_id' => 2]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_account_id' => $subAccountIds[4] ?? 5, // David
                'type' => 'package_purchase',
                'amount' => 100.00,
                'balance_before' => 0.00,
                'balance_after' => -100.00,
                'description' => 'Basic Package purchase - 100 Taka',
                'status' => 'approved',
                'system_ip' => '192.168.1.104',
                'system_user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode(['package' => 'Basic Package', 'package_id' => 1]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Some sponsor commission transactions
            [
                'sub_account_id' => $subAccountIds[0] ?? 1, // John gets sponsor commission from Jane
                'type' => 'sponsor_commission',
                'amount' => 80.00, // 40% of Jane's 200 Taka package
                'balance_before' => -100.00,
                'balance_after' => -20.00,
                'description' => 'Sponsor commission from Jane Smith - 40% of 200 Taka',
                'status' => 'approved',
                'system_ip' => '192.168.1.100',
                'system_user_agent' => 'System Generated',
                'metadata' => json_encode(['referred_account' => 'Jane Smith', 'commission_rate' => '40%']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_account_id' => $subAccountIds[0] ?? 1, // John gets sponsor commission from Mike
                'type' => 'sponsor_commission',
                'amount' => 200.00, // 40% of Mike's 500 Taka package
                'balance_before' => -20.00,
                'balance_after' => 180.00,
                'description' => 'Sponsor commission from Mike Johnson - 40% of 500 Taka',
                'status' => 'approved',
                'system_ip' => '192.168.1.100',
                'system_user_agent' => 'System Generated',
                'metadata' => json_encode(['referred_account' => 'Mike Johnson', 'commission_rate' => '40%']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Auto income transactions
            [
                'sub_account_id' => $subAccountIds[0] ?? 1, // John gets auto income
                'type' => 'auto_income',
                'amount' => 100.00,
                'balance_before' => 180.00,
                'balance_after' => 280.00,
                'description' => 'Daily auto income distribution - 100 Taka',
                'status' => 'approved',
                'system_ip' => '192.168.1.100',
                'system_user_agent' => 'System Generated',
                'metadata' => json_encode(['distribution_date' => now()->format('Y-m-d'), 'board_id' => 1]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('transactions')->insert($transactions);
    }
}
