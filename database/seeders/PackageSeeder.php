<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic Package',
                'amount' => 100.00,
                'description' => 'Basic investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 500.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Starter Package',
                'amount' => 200.00,
                'description' => 'Starter investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 1000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard Package',
                'amount' => 500.00,
                'description' => 'Standard investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 2500.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium Package',
                'amount' => 1000.00,
                'description' => 'Premium investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 5000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gold Package',
                'amount' => 2000.00,
                'description' => 'Gold investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 10000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Platinum Package',
                'amount' => 5000.00,
                'description' => 'Platinum investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 25000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Diamond Package',
                'amount' => 10000.00,
                'description' => 'Diamond investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 50000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Executive Package',
                'amount' => 20000.00,
                'description' => 'Executive investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 100000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VIP Package',
                'amount' => 50000.00,
                'description' => 'VIP investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 250000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ultimate Package',
                'amount' => 100000.00,
                'description' => 'Ultimate investment package with 1:5 withdrawal ratio',
                'withdrawal_limit' => 500000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('packages')->insert($packages);
    }
}
