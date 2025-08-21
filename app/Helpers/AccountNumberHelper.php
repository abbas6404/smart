<?php

namespace App\Helpers;

class AccountNumberHelper
{
    /**
     * Generate a unique 11-digit account number for sub-accounts
     * Format: XXX-XXXX-XXXX (like ATM card numbers)
     * Uses Y + DDD + SSSSS + CCC format
     * Y=Year(1), DDD=Day(3), SSSSS=Seconds(5), CCC=Counter(3)
     * 
     * @return string
     */
    public static function generateAccountNumber(): string
    {
        do {
            // Get current date/time components
            $year = date('Y') % 10;      // Last digit only: 4, 5, 6... (1 digit)
            $dayOfYear = date('z');      // Day of year: 0-365 (3 digits)
            
            // Get seconds since midnight (0-86399 = 5 digits)
            $hour = date('G');           // Hour: 0-23
            $minute = date('i');         // Minute: 0-59
            $second = date('s');         // Second: 0-59
            $totalSeconds = $hour * 3600 + $minute * 60 + $second; // 0-86399
            
            // Get counter for this second with microsecond precision (1-999 = 3 digits)
            $microseconds = round(microtime(true) * 1000000) % 1000000; // 0-999999
            $counter = ($microseconds % 999) + 1; // 1-999
            
            // Combine: Y + DDD + SSSSS + CCC
            // Example: 4 + 365 + 12345 + 1 = 436512345001
            $fullNumber = $year . 
                         str_pad($dayOfYear, 3, '0', STR_PAD_LEFT) . 
                         str_pad($totalSeconds, 5, '0', STR_PAD_LEFT) . 
                         str_pad($counter, 3, '0', STR_PAD_LEFT);
            
            // Format as XXX-XXXX-XXXX (3-4-4 format)
            $accountNumber = substr($fullNumber, 0, 3) . '-' . 
                           substr($fullNumber, 3, 4) . '-' . 
                           substr($fullNumber, 7, 4);
            
            // Check if this account number already exists
            $exists = \DB::table('sub_accounts')
                ->where('account_number', $accountNumber)
                ->exists();
                
        } while ($exists);
        
        return $accountNumber;
    }

    /**
     * Generate a unique referral code for sub-accounts
     * Format: 8 characters (alphanumeric)
     * 
     * @return string
     */
    public static function generateReferralCode(): string
    {
        do {
            // Generate 8 random alphanumeric characters
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $referralCode = '';
            
            for ($i = 0; $i < 8; $i++) {
                $referralCode .= $characters[mt_rand(0, strlen($characters) - 1)];
            }
            
            // Check if this referral code already exists
            $exists = \DB::table('sub_accounts')
                ->where('referral_code', $referralCode)
                ->exists();
                
        } while ($exists);
        
        return $referralCode;
    }

    /**
     * Validate account number format
     * 
     * @param string $accountNumber
     * @return bool
     */
    public static function validateAccountNumber(string $accountNumber): bool
    {
        // Check format: XXX-XXXX-XXXX (3-4-4 format)
        $pattern = '/^\d{3}-\d{4}-\d{4}$/';
        return preg_match($pattern, $accountNumber) === 1;
    }

    /**
     * Validate referral code format
     * 
     * @param string $referralCode
     * @return bool
     */
    public static function validateReferralCode(string $referralCode): bool
    {
        // Check format: 8 alphanumeric characters
        $pattern = '/^[A-Z0-9]{8}$/';
        return preg_match($pattern, $referralCode) === 1;
    }

    /**
     * Format account number for display
     * 
     * @param string $accountNumber
     * @return string
     */
    public static function formatAccountNumber(string $accountNumber): string
    {
        // Remove any existing dashes and reformat
        $clean = str_replace('-', '', $accountNumber);
        
        if (strlen($clean) === 11) {
            return substr($clean, 0, 3) . '-' . 
                   substr($clean, 3, 4) . '-' . 
                   substr($clean, 7, 4);
        }
        
        return $accountNumber;
    }

    /**
     * Generate multiple unique account numbers
     * 
     * @param int $count
     * @return array
     */
    public static function generateMultipleAccountNumbers(int $count): array
    {
        $accountNumbers = [];
        
        for ($i = 0; $i < $count; $i++) {
            $accountNumbers[] = self::generateAccountNumber();
        }
        
        return $accountNumbers;
    }

    /**
     * Generate multiple unique referral codes
     * 
     * @param int $count
     * @return array
     */
    public static function generateMultipleReferralCodes(int $count): array
    {
        $referralCodes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $referralCodes[] = self::generateReferralCode();
        }
        
        return $referralCodes;
    }
}
