<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackagePurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get necessary IDs
        $subAccountIds = DB::table('sub_accounts')->pluck('id')->toArray();
        $packageIds = DB::table('packages')->pluck('id')->toArray();
        $transactionIds = DB::table('transactions')
            ->where('type', 'package_purchase')
            ->pluck('id')
            ->toArray();
        
        if (empty($subAccountIds) || empty($packageIds) || empty($transactionIds)) {
            return; // Skip if required data is missing
        }

        $packagePurchases = [
            [
                'sub_account_id' => $subAccountIds[0] ?? 1, // John
                'package_id' => $packageIds[0] ?? 1, // Basic Package
                'transaction_id' => $transactionIds[0] ?? 1,
                'amount' => 100.00,
                'notes' => 'First package purchase by John - Basic Package',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_account_id' => $subAccountIds[1] ?? 2, // Jane
                'package_id' => $packageIds[1] ?? 2, // Starter Package
                'transaction_id' => $transactionIds[1] ?? 2,
                'amount' => 200.00,
                'notes' => 'First package purchase by Jane - Starter Package',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_account_id' => $subAccountIds[2] ?? 3, // Mike
                'package_id' => $packageIds[2] ?? 3, // Standard Package
                'transaction_id' => $transactionIds[2] ?? 3,
                'amount' => 500.00,
                'notes' => 'First package purchase by Mike - Standard Package',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_account_id' => $subAccountIds[3] ?? 4, // Sarah
                'package_id' => $packageIds[1] ?? 2, // Starter Package
                'transaction_id' => $transactionIds[3] ?? 4,
                'amount' => 200.00,
                'notes' => 'First package purchase by Sarah - Starter Package',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_account_id' => $subAccountIds[4] ?? 5, // David
                'package_id' => $packageIds[0] ?? 1, // Basic Package
                'transaction_id' => $transactionIds[4] ?? 5,
                'amount' => 100.00,
                'notes' => 'First package purchase by David - Basic Package',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('package_purchases')->insert($packagePurchases);

        // Update sub_accounts with package purchase totals
        foreach ($packagePurchases as $purchase) {
            DB::table('sub_accounts')
                ->where('id', $purchase['sub_account_id'])
                ->increment('total_package_purchase', $purchase['amount']);
        }
    }
}
