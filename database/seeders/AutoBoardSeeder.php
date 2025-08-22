<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AutoBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create auto boards for past 3 days and next 2 days
        $autoBoards = [];
        
        // Past 3 days with collections (ready for distribution)
        for ($i = -3; $i <= -1; $i++) {
            $date = now()->addDays($i);
            $collectionAmount = rand(500, 2000); // Random collection between 500-2000
            $contributors = rand(10, 50); // Random contributors between 10-50
            
            $autoBoards[] = [
                'total_collection_amount' => $collectionAmount,
                'total_contributors' => $contributors,
                'total_distributed' => 0,
                'today_collection_amount' => $collectionAmount,
                'today_contributors' => $contributors,
                'today_distributed' => 0,
                'today_per_account_distributed' => 0,
                'distribution_date' => $date->format('Y-m-d'),
                'distributed_date' => null,
                'status' => 'collection', // Ready for distribution
                'distribution_log' => json_encode([
                    'collection_date' => $date->format('Y-m-d'),
                    'contributors' => $contributors,
                    'total_amount' => $collectionAmount,
                    'status' => 'ready_for_distribution'
                ]),
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }
        
        // Today's board (currently collecting)
        $todayCollection = rand(100, 800); // Random ongoing collection
        $todayContributors = rand(5, 25);
        
        $autoBoards[] = [
            'total_collection_amount' => $todayCollection,
            'total_contributors' => $todayContributors,
            'total_distributed' => 0,
            'today_collection_amount' => $todayCollection,
            'today_contributors' => $todayContributors,
            'today_distributed' => 0,
            'today_per_account_distributed' => 0,
            'distribution_date' => now()->format('Y-m-d'),
            'distributed_date' => null,
            'status' => 'collection', // Currently collecting
            'distribution_log' => json_encode([
                'collection_date' => now()->format('Y-m-d'),
                'contributors' => $todayContributors,
                'total_amount' => $todayCollection,
                'status' => 'collecting'
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        // Future days (empty boards)
        for ($i = 1; $i <= 2; $i++) {
            $date = now()->addDays($i);
            
            $autoBoards[] = [
                'total_collection_amount' => 0,
                'total_contributors' => 0,
                'total_distributed' => 0,
                'today_collection_amount' => 0,
                'today_contributors' => 0,
                'today_distributed' => 0,
                'today_per_account_distributed' => 0,
                'distribution_date' => $date->format('Y-m-d'),
                'distributed_date' => null,
                'status' => 'collection', // Future boards ready to collect
                'distribution_log' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('auto_boards')->insert($autoBoards);
    }
}
