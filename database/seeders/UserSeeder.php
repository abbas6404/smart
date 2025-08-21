<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'John Doe',
                'phone' => '+8801712345678',
                'email' => 'john.doe@scc.com',
                'password' => Hash::make('12345678'),
                'address' => 'Dhaka, Bangladesh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'phone' => '+8801812345678',
                'email' => 'jane.smith@scc.com',
                'password' => Hash::make('12345678'),
                'address' => 'Chittagong, Bangladesh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mike Johnson',
                'phone' => '+8801912345678',
                'email' => 'mike.johnson@scc.com',
                'password' => Hash::make('12345678'),
                'address' => 'Sylhet, Bangladesh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sarah Wilson',
                'phone' => '+8801612345678',
                'email' => 'sarah.wilson@scc.com',
                'password' => Hash::make('12345678'),
                'address' => 'Rajshahi, Bangladesh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'David Brown',
                'phone' => '+8801512345678',
                'email' => 'david.brown@scc.com',
                'password' => Hash::make('12345678'),
                'address' => 'Khulna, Bangladesh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
