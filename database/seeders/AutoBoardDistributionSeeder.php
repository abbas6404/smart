<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AutoBoardDistributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get necessary data
        $todaysBoard = DB::table('auto_boards')
            ->where('distribution_date', now()->format('Y-m-d'))
            ->first();
        
        $subAccounts = DB::table('sub_accounts')
            ->where('direct_referral_count', '>=', 30) // Only accounts eligible for auto income
            ->get();

        if (!$todaysBoard || $subAccounts->isEmpty()) {
            // If no eligible accounts, create some sample distributions anyway for demo
            $allSubAccounts = DB::table('sub_accounts')->get();
            
            if ($todaysBoard && $allSubAccounts->isNotEmpty()) {
                $distributions = [];
                $totalDistributed = 0;
                
                // Distribute to first 3 accounts as example (assuming they have 30+ referrals)
                foreach ($allSubAccounts->take(3) as $index => $subAccount) {
                    $amount = ($index + 1) * 50; // 50, 100, 150 Taka distribution
                    
                    $distributions[] = [
                        'auto_board_id' => $todaysBoard->id,
                        'sub_account_id' => $subAccount->id,
                        'amount' => $amount,
                        'direct_referral_count' => 30 + ($index * 5), // Simulated referral count
                        'notes' => "Daily auto income distribution - {$amount} Taka for having 30+ direct referrals",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    $totalDistributed += $amount;
                }
                
                if (!empty($distributions)) {
                    DB::table('auto_board_distributions')->insert($distributions);
                    
                    // Update auto board status
                    DB::table('auto_boards')
                        ->where('id', $todaysBoard->id)
                        ->update([
                            'total_distributed' => $totalDistributed,
                            'today_distributed' => $totalDistributed,
                            'today_per_account_distributed' => $totalDistributed / count($distributions),
                            'distributed_date' => now()->format('Y-m-d'),
                            'status' => 'distributed',
                            'distribution_log' => json_encode([
                                'message' => 'Auto income distributed to eligible accounts',
                                'total_amount' => $totalDistributed,
                                'accounts_count' => count($distributions),
                                'distribution_date' => now()->toDateTimeString()
                            ]),
                            'updated_at' => now(),
                        ]);
                }
            }
            return;
        }

        // If there are actually eligible accounts, distribute to them
        $distributions = [];
        $totalDistributed = 0;
        
        foreach ($subAccounts as $subAccount) {
            // Calculate distribution amount based on referral count
            $baseAmount = 100; // Base 100 Taka
            $bonusAmount = ($subAccount->direct_referral_count - 30) * 2; // 2 Taka per extra referral
            $amount = $baseAmount + $bonusAmount;
            
            $distributions[] = [
                'auto_board_id' => $todaysBoard->id,
                'sub_account_id' => $subAccount->id,
                'amount' => $amount,
                'direct_referral_count' => $subAccount->direct_referral_count,
                'notes' => "Daily auto income - {$amount} Taka for {$subAccount->direct_referral_count} direct referrals",
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $totalDistributed += $amount;
        }

        if (!empty($distributions)) {
            DB::table('auto_board_distributions')->insert($distributions);
            
            // Update auto board with distribution data
            DB::table('auto_boards')
                ->where('id', $todaysBoard->id)
                ->update([
                    'total_distributed' => $totalDistributed,
                    'today_distributed' => $totalDistributed,
                    'today_per_account_distributed' => $totalDistributed / count($distributions),
                    'distributed_date' => now()->format('Y-m-d'),
                    'status' => 'distributed',
                    'distribution_log' => json_encode([
                        'message' => 'Auto income distributed successfully',
                        'total_amount' => $totalDistributed,
                        'accounts_count' => count($distributions),
                        'average_per_account' => $totalDistributed / count($distributions),
                        'distribution_date' => now()->toDateTimeString()
                    ]),
                    'updated_at' => now(),
                ]);
        }
    }
}
