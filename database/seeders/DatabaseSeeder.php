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
            AgentSeeder::class,                   // 5. Create agents
            SubAccountSeeder::class,             // 6. Create sub-accounts (depends on users & packages)
            AutoBoardSeeder::class,              // 7. Create auto boards for daily income
            TransactionSeeder::class,            // 8. Create sample transactions
            PackagePurchaseSeeder::class,        // 9. Create package purchases (depends on transactions)
            AutoBoardContributionSeeder::class,  // 10. Create auto board contributions
            AutoBoardDistributionSeeder::class,  // 11. Create auto board distributions
            SystemSettingSeeder::class,          // 12. Create system settings
            TicketCategorySeeder::class,         // 13. Create ticket categories
            TicketSeeder::class,                 // 14. Create sample tickets
            KYCVerificationSeeder::class,        // 15. Create KYC verification records
        ]);

    
    }
}
