<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KYCVerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get user IDs (assuming users are seeded first)
        $userIds = DB::table('users')->pluck('id')->toArray();
        
        // Get admin ID (assuming admin is seeded first)
        $adminId = DB::table('admins')->pluck('id')->first();

        if (empty($userIds)) {
            return; // Don't seed if dependencies aren't available
        }

        DB::table('kyc_verifications')->insert([
            [
                'user_id' => $userIds[0], // John Doe
                'nid_number' => '19901234567890123',
                'nid_type' => 'smart_nid',
                'full_name_bangla' => 'জন ডো',
                'full_name_english' => 'John Doe',
                'father_name' => 'Robert Doe',
                'mother_name' => 'Mary Doe',
                'date_of_birth' => '1990-05-15',
                'gender' => 'male',
                'blood_group' => 'O+',
                'address' => 'House #123, Road #5, Dhanmondi, Dhaka-1205',
                'postal_code' => '1205',
                'nid_front_image' => 'kyc/nid_front_john_doe.jpg',
                'nid_back_image' => 'kyc/nid_back_john_doe.jpg',
                'status' => 'kyc_verified',
                'reviewed_by' => $adminId,
                'reviewed_at' => now()->subDays(3),
                'review_notes' => 'All documents verified successfully. NID details match with provided information.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(3),
            ],
            [
                'user_id' => $userIds[1], // Jane Smith
                'nid_number' => '19851234567890123',
                'nid_type' => 'smart_nid',
                'full_name_bangla' => 'জেন স্মিথ',
                'full_name_english' => 'Jane Smith',
                'father_name' => 'William Smith',
                'mother_name' => 'Elizabeth Smith',
                'date_of_birth' => '1985-08-22',
                'gender' => 'female',
                'blood_group' => 'A+',
                'address' => 'Apartment #45, Building #12, Agrabad, Chittagong-4100',
                'postal_code' => '4100',
                'nid_front_image' => 'kyc/nid_front_jane_smith.jpg',
                'nid_back_image' => 'kyc/nid_back_jane_smith.jpg',
                'status' => 'kyc_verified',
                'reviewed_by' => $adminId,
                'reviewed_at' => now()->subDays(2),
                'review_notes' => 'Verification completed. All documents are authentic and information is correct.',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $userIds[2], // Mike Johnson
                'nid_number' => '19921234567890123',
                'nid_type' => 'old_nid',
                'full_name_bangla' => 'মাইক জনসন',
                'full_name_english' => 'Mike Johnson',
                'father_name' => 'David Johnson',
                'mother_name' => 'Sarah Johnson',
                'date_of_birth' => '1992-03-10',
                'gender' => 'male',
                'blood_group' => 'B+',
                'address' => 'Village: Shibganj, Upazila: Sylhet Sadar, District: Sylhet',
                'postal_code' => '3100',
                'nid_front_image' => 'kyc/nid_front_mike_johnson.jpg',
                'nid_back_image' => 'kyc/nid_back_mike_johnson.jpg',
                'status' => 'kyc_pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $userIds[3], // Sarah Wilson
                'nid_number' => '19881234567890123',
                'nid_type' => 'smart_nid',
                'full_name_bangla' => 'সারা উইলসন',
                'full_name_english' => 'Sarah Wilson',
                'father_name' => 'James Wilson',
                'mother_name' => 'Patricia Wilson',
                'date_of_birth' => '1988-11-18',
                'gender' => 'female',
                'blood_group' => 'AB+',
                'address' => 'House #78, Road #3, Rajshahi University Area, Rajshahi-6205',
                'postal_code' => '6205',
                'nid_front_image' => 'kyc/nid_front_sarah_wilson.jpg',
                'nid_back_image' => 'kyc/nid_back_sarah_wilson.jpg',
                'status' => 'kyc_failed',
                'reviewed_by' => $adminId,
                'reviewed_at' => now()->subHours(6),
                'review_notes' => 'KYC verification failed. NID images are unclear and address information does not match current records. Please resubmit with clear documents.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subHours(6),
            ],
            [
                'user_id' => $userIds[4], // David Brown
                'nid_number' => '19951234567890123',
                'nid_type' => 'birth_certificate',
                'full_name_bangla' => 'ডেভিড ব্রাউন',
                'full_name_english' => 'David Brown',
                'father_name' => 'Michael Brown',
                'mother_name' => 'Jennifer Brown',
                'date_of_birth' => '1995-07-04',
                'gender' => 'male',
                'blood_group' => 'O-',
                'address' => 'House #56, Road #8, Khalishpur, Khulna-9000',
                'postal_code' => '9000',
                'nid_front_image' => 'kyc/birth_cert_david_brown.jpg',
                'nid_back_image' => null,
                'status' => 'kyc_pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
            [
                'user_id' => $userIds[0], // John Doe - Second KYC (renewal)
                'nid_number' => '19901234567890124',
                'nid_type' => 'smart_nid',
                'full_name_bangla' => 'জন ডো',
                'full_name_english' => 'John Doe',
                'father_name' => 'Robert Doe',
                'mother_name' => 'Mary Doe',
                'date_of_birth' => '1990-05-15',
                'gender' => 'male',
                'blood_group' => 'O+',
                'address' => 'House #123, Road #5, Dhanmondi, Dhaka-1205',
                'postal_code' => '1205',
                'nid_front_image' => 'kyc/nid_front_john_doe_renewal.jpg',
                'nid_back_image' => 'kyc/nid_back_john_doe_renewal.jpg',
                'status' => 'kyc_pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
        ]);
    }
}
