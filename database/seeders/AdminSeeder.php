<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
           
        ]);
    }
}
