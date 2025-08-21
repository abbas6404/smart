<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levelCommissions = [
            [
                'level' => 1,
                'commission_percentage' => 1.00, // 1% for level 1
                'description' => 'Level 1 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 2,
                'commission_percentage' => 1.00, // 1% for level 2
                'description' => 'Level 2 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 3,
                'commission_percentage' => 1.00, // 1% for level 3
                'description' => 'Level 3 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 4,
                'commission_percentage' => 1.00, // 1% for level 4
                'description' => 'Level 4 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 5,
                'commission_percentage' => 1.00, // 1% for level 5
                'description' => 'Level 5 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 6,
                'commission_percentage' => 1.00, // 1% for level 6
                'description' => 'Level 6 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 7,
                'commission_percentage' => 1.00, // 1% for level 7
                'description' => 'Level 7 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 8,
                'commission_percentage' => 1.00, // 1% for level 8
                'description' => 'Level 8 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 9,
                'commission_percentage' => 1.00, // 1% for level 9
                'description' => 'Level 9 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 10,
                'commission_percentage' => 1.00, // 1% for level 10
                'description' => 'Level 10 generation commission (1%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('level_commissions')->insert($levelCommissions);
    }
}
