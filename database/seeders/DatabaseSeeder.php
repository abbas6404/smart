<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in order (dependencies first)
        $this->call([
            AdminSeeder::class,                  // 1. Create admin accounts first
            PackageSeeder::class,                // 2. Create investment packages
            LevelCommissionSeeder::class,        // 3. Create level commission structure
            UserSeeder::class,                   // 4. Create sample users
            SubAccountSeeder::class,             // 5. Create sub-accounts (depends on users & packages)
            AutoBoardSeeder::class,              // 6. Create auto boards for daily income
            TransactionSeeder::class,            // 7. Create sample transactions
            PackagePurchaseSeeder::class,        // 8. Create package purchases (depends on transactions)
            AutoBoardContributionSeeder::class,  // 9. Create auto board contributions
            AutoBoardDistributionSeeder::class,  // 10. Create auto board distributions
            SystemSettingSeeder::class,          // 11. Create system settings
        ]);

    
    }
}
