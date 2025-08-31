<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ticket_categories')->insert([
            [
                'name' => 'Technical Support',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Billing & Payment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Account Issues',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'General Inquiry',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Feature Request',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bug Report',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Security Concern',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Partnership Inquiry',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
