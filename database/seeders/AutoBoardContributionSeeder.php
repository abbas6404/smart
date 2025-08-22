<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AutoBoardContributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get necessary IDs
        $todaysBoard = DB::table('auto_boards')
            ->where('distribution_date', now()->format('Y-m-d'))
            ->first();
        
        $subAccountIds = DB::table('sub_accounts')->pluck('id')->toArray();
        $packagePurchaseIds = DB::table('package_purchases')->pluck('id')->toArray();
        $transactionIds = DB::table('transactions')->pluck('id')->toArray();

        if (!$todaysBoard || empty($subAccountIds)) {
            return; // Skip if no data available
        }

        $contributions = [
            [
                'auto_board_id' => $todaysBoard->id,
                'sub_account_id' => $subAccountIds[0] ?? 1, // John's account
                'package_purchase_id' => $packagePurchaseIds[0] ?? 1,
                'transaction_id' => $transactionIds[0] ?? 1,
                'amount' => 30.00, // 30% of 100 Taka package
                'notes' => 'Auto income contribution from Basic Package purchase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'auto_board_id' => $todaysBoard->id,
                'sub_account_id' => $subAccountIds[1] ?? 2, // Jane's account
                'package_purchase_id' => $packagePurchaseIds[1] ?? 2,
                'transaction_id' => $transactionIds[1] ?? 2,
                'amount' => 60.00, // 30% of 200 Taka package
                'notes' => 'Auto income contribution from Starter Package purchase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'auto_board_id' => $todaysBoard->id,
                'sub_account_id' => $subAccountIds[2] ?? 3, // Mike's account
                'package_purchase_id' => $packagePurchaseIds[2] ?? 3,
                'transaction_id' => $transactionIds[2] ?? 3,
                'amount' => 150.00, // 30% of 500 Taka package
                'notes' => 'Auto income contribution from Standard Package purchase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'auto_board_id' => $todaysBoard->id,
                'sub_account_id' => $subAccountIds[3] ?? 4, // Sarah's account
                'package_purchase_id' => $packagePurchaseIds[3] ?? 4,
                'transaction_id' => $transactionIds[3] ?? 4,
                'amount' => 60.00, // 30% of 200 Taka package
                'notes' => 'Auto income contribution from Starter Package purchase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'auto_board_id' => $todaysBoard->id,
                'sub_account_id' => $subAccountIds[4] ?? 5, // David's account
                'package_purchase_id' => $packagePurchaseIds[4] ?? 5,
                'transaction_id' => $transactionIds[4] ?? 5,
                'amount' => 30.00, // 30% of 100 Taka package
                'notes' => 'Auto income contribution from Basic Package purchase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('auto_board_contributions')->insert($contributions);

        // Update today's auto board with contribution totals
        $totalAmount = array_sum(array_column($contributions, 'amount'));
        DB::table('auto_boards')
            ->where('id', $todaysBoard->id)
            ->update([
                'total_collection_amount' => $totalAmount,
                'today_collection_amount' => $totalAmount,
                'total_contributors' => count($contributions),
                'today_contributors' => count($contributions),
                'updated_at' => now(),
            ]);
    }
}
