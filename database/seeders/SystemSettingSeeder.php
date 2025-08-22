<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // MLM System Settings
            // System Settings
            [
                'key' => 'site_url',
                'value' => 'http://smart.test',   //change this to your website url
                'type' => 'string',
                'group' => 'system',
                'display_name' => 'Site URL',
                'description' => 'The main URL of the website',
                'is_editable' => true,
                'is_public' => true,
                'options' => null,
                'validation_rules' => 'required|url',
                'created_at' => now(),
                'updated_at' => now(),
            ],
    


            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'system',
                'display_name' => 'Maintenance Mode',
                'description' => 'Enable maintenance mode to restrict user access',
                'is_editable' => true,
                'is_public' => false,
                'options' => json_encode(['true' => 'Enabled', 'false' => 'Disabled']),
                'validation_rules' => 'required|boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],

                      // Security Settings
                      [
                        'key' => 'session_timeout',
                        'value' => '120',
                        'type' => 'integer',
                        'group' => 'security',
                        'display_name' => 'Session Timeout (Minutes)',
                        'description' => 'Number of minutes before user session expires due to inactivity',
                        'is_editable' => true,
                        'is_public' => false,
                        'options' => json_encode(['min' => 15, 'max' => 480, 'step' => 15]),
                        'validation_rules' => 'required|integer|min:15|max:480',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'key' => 'max_login_attempts',
                        'value' => '5',
                        'type' => 'integer',
                        'group' => 'security',
                        'display_name' => 'Maximum Login Attempts',
                        'description' => 'Maximum failed login attempts before account lockout',
                        'is_editable' => true,
                        'is_public' => false,
                        'options' => json_encode(['min' => 3, 'max' => 10, 'step' => 1]),
                        'validation_rules' => 'required|integer|min:3|max:10',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],


                    [
                        'key' => 'registration_enabled',
                        'value' => 'true',
                        'type' => 'boolean',
                        'group' => 'system',
                        'display_name' => 'User Registration',
                        'description' => 'Allow new users to register accounts',
                        'is_editable' => true,
                        'is_public' => false,
                        'options' => json_encode(['true' => 'Enabled', 'false' => 'Disabled']),
                        'validation_rules' => 'required|boolean',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],

            [
                'key' => 'sponsor_commission_rate',
                'value' => '40',
                'type' => 'decimal',
                'group' => 'mlm',
                'display_name' => 'Sponsor Commission Rate',
                'description' => 'Percentage of package amount that goes to the sponsor (40%)',
                'is_editable' => true,
                'is_public' => true,
                'options' => json_encode(['min' => 0, 'max' => 100, 'step' => 1]),
                'validation_rules' => 'required|numeric|min:0|max:100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'auto_income_rate',
                'value' => '30',
                'type' => 'decimal',
                'group' => 'mlm',
                'display_name' => 'Auto Income Rate',
                'description' => 'Percentage of package amount that goes to auto board (30%)',
                'is_editable' => true,
                'is_public' => true,
                'options' => json_encode(['min' => 0, 'max' => 100, 'step' => 1]),
                'validation_rules' => 'required|numeric|min:0|max:100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'generation_commission_rate',
                'value' => '20',
                'type' => 'decimal',
                'group' => 'mlm',
                'display_name' => 'Generation Commission Rate',
                'description' => 'Percentage of package amount that goes to generation levels (20%)',
                'is_editable' => true,
                'is_public' => true,
                'options' => json_encode(['min' => 0, 'max' => 100, 'step' => 1]),
                'validation_rules' => 'required|numeric|min:0|max:100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'auto_income_eligibility',
                'value' => '30',
                'type' => 'integer',
                'group' => 'mlm',
                'display_name' => 'Auto Income Eligibility',
                'description' => 'Minimum number of direct referrals required to be eligible for auto income',
                'is_editable' => true,
                'is_public' => true,
                'options' => json_encode(['min' => 1, 'max' => 1000, 'step' => 1]),
                'validation_rules' => 'required|integer|min:1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_generation_levels',
                'value' => '10',
                'type' => 'integer',
                'group' => 'mlm',
                'display_name' => 'Maximum Generation Levels',
                'description' => 'Maximum number of generation levels for commission distribution',
                'is_editable' => true,
                'is_public' => true,
                'options' => json_encode(['min' => 1, 'max' => 20, 'step' => 1]),
                'validation_rules' => 'required|integer|min:1|max:20',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Financial Settings
          
            [
                'key' => 'minimum_withdrawal',
                'value' => '100',
                'type' => 'decimal',
                'group' => 'financial',
                'display_name' => 'Minimum Withdrawal Amount',
                'description' => 'Minimum amount that can be withdrawn in Taka',
                'is_editable' => true,
                'is_public' => true,
                'options' => json_encode(['min' => 50, 'max' => 1000, 'step' => 50]),
                'validation_rules' => 'required|numeric|min:50',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maximum_withdrawal',
                'value' => '10000',
                'type' => 'decimal',
                'group' => 'financial',
                'display_name' => 'Maximum Withdrawal Amount',
                'description' => 'Maximum amount that can be withdrawn per day in Taka',
                'is_editable' => true,
                'is_public' => true,
                'options' => json_encode(['min' => 1000, 'max' => 100000, 'step' => 1000]),
                'validation_rules' => 'required|numeric|min:1000',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Auto Board Settings
            [
                'key' => 'auto_board_distribution_time',
                'value' => '00:00',
                'type' => 'time',
                'group' => 'auto_board',
                'display_name' => 'Auto Board Distribution Time',
                'description' => 'Daily time when auto board distributions are processed (24-hour format)',
                'is_editable' => true,
                'is_public' => false,
                'options' => json_encode(['format' => '24h', 'step' => '00:15']),
                'validation_rules' => 'required|date_format:H:i',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'auto_board_retention_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'auto_board',
                'display_name' => 'Auto Board Retention Days',
                'description' => 'Number of days to keep auto board data before archiving',
                'is_editable' => true,
                'is_public' => false,
                'options' => json_encode(['min' => 7, 'max' => 365, 'step' => 1]),
                'validation_rules' => 'required|integer|min:7|max:365',
                'created_at' => now(),
                'updated_at' => now(),
            ],

    
            [
                'key' => 'max_sub_accounts_per_user',
                'value' => '5',
                'type' => 'integer',
                'group' => 'system',
                'display_name' => 'Maximum Sub-Accounts Per User',
                'description' => 'Maximum number of sub-accounts a user can create',
                'is_editable' => true,
                'is_public' => true,
                'options' => json_encode(['min' => 1, 'max' => 20, 'step' => 1]),
                'validation_rules' => 'required|integer|min:1|max:20',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            
            

  
        ];

        DB::table('system_settings')->insert($settings);
    }
}
