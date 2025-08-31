<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get user IDs (assuming users are seeded first)
        $userIds = DB::table('users')->pluck('id')->toArray();
        
        // Get category IDs (assuming categories are seeded first)
        $categoryIds = DB::table('ticket_categories')->pluck('id')->toArray();
        
        // Get admin ID (assuming admin is seeded first)
        $adminId = DB::table('admins')->pluck('id')->first();

        if (empty($userIds) || empty($categoryIds)) {
            return; // Don't seed if dependencies aren't available
        }

        DB::table('tickets')->insert([
            [
                'user_id' => $userIds[0], // John Doe
                'category_id' => $categoryIds[0], // Technical Support
                'message' => 'I am having trouble logging into my account. It keeps saying "Invalid credentials" even though I am sure my password is correct.',
                'attachments' => null,
                'reply' => 'Hello John, I have reset your password. Please check your email for the new password. If you still have issues, please let me know.',
                'status' => 'closed',
                'reviewed_by' => $adminId,
                'reviewed_at' => now()->subDays(2),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $userIds[1], // Jane Smith
                'category_id' => $categoryIds[1], // Billing & Payment
                'message' => 'I was charged twice for my monthly subscription. Can you please refund the duplicate charge?',
                'attachments' => 'payment_receipts/screenshot_2024_01_15.png',
                'reply' => 'Hello Jane, I can see the duplicate charge. I have processed a refund for $29.99. It should appear in your account within 3-5 business days.',
                'status' => 'closed',
                'reviewed_by' => $adminId,
                'reviewed_at' => now()->subDays(1),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $userIds[2], // Mike Johnson
                'category_id' => $categoryIds[2], // Account Issues
                'message' => 'My account shows as suspended but I haven\'t received any notification about this. Can you help me understand why?',
                'attachments' => null,
                'reply' => null,
                'status' => 'open',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $userIds[3], // Sarah Wilson
                'category_id' => $categoryIds[3], // General Inquiry
                'message' => 'I would like to know more about your premium features. What additional benefits do I get with the premium plan?',
                'attachments' => null,
                'reply' => 'Hello Sarah, our premium plan includes priority support, advanced analytics, unlimited storage, and exclusive features. Would you like me to schedule a demo call?',
                'status' => 'closed',
                'reviewed_by' => $adminId,
                'reviewed_at' => now()->subHours(6),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subHours(6),
            ],
            [
                'user_id' => $userIds[4], // David Brown
                'category_id' => $categoryIds[4], // Feature Request
                'message' => 'It would be great if you could add a dark mode option to the mobile app. Many users prefer dark themes for better battery life and eye comfort.',
                'attachments' => null,
                'reply' => 'Thank you for the suggestion David! Dark mode is actually in our development roadmap and should be available in the next major update (Q2 2024).',
                'status' => 'closed',
                'reviewed_by' => $adminId,
                'reviewed_at' => now()->subHours(12),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subHours(12),
            ],
            [
                'user_id' => $userIds[0], // John Doe
                'category_id' => $categoryIds[5], // Bug Report
                'message' => 'When I try to upload files larger than 10MB, the app crashes. This happens consistently on both iOS and Android. Steps to reproduce: 1. Select file >10MB 2. Tap upload 3. App crashes immediately.',
                'attachments' => 'bug_reports/crash_log_2024_01_16.txt',
                'reply' => 'Thank you for the detailed bug report John. Our development team has identified the issue and a fix will be deployed in the next app update (version 2.1.3).',
                'status' => 'closed',
                'reviewed_by' => $adminId,
                'reviewed_at' => now()->subHours(3),
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subHours(3),
            ],
            [
                'user_id' => $userIds[1], // Jane Smith
                'category_id' => $categoryIds[6], // Security Concern
                'message' => 'I received an email that looks like it\'s from your company asking for my password. I\'m concerned this might be a phishing attempt. The email address was support@yourcompany-verify.com',
                'attachments' => 'security/phishing_email_screenshot.png',
                'reply' => 'Thank you for reporting this Jane! That email is indeed a phishing attempt and is NOT from our company. We never ask for passwords via email. Please delete it and mark as spam.',
                'status' => 'closed',
                'reviewed_by' => $adminId,
                'reviewed_at' => now()->subHours(1),
                'created_at' => now()->subHours(4),
                'updated_at' => now()->subHours(1),
            ],
            [
                'user_id' => $userIds[2], // Mike Johnson
                'category_id' => $categoryIds[7], // Partnership Inquiry
                'message' => 'I represent a company that would like to explore partnership opportunities with your platform. We offer complementary services that could benefit both our user bases.',
                'attachments' => 'partnership/company_presentation.pdf',
                'reply' => null,
                'status' => 'open',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
        ]);
    }
}
