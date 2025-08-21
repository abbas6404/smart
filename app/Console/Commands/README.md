# AutoBoard Commands Documentation

## Overview

This folder contains Laravel Artisan commands for managing the AutoBoard system - an automated daily income distribution system for MLM businesses.

## Commands

### 1. ProcessAutoBoardDistribution

**Command:** `php artisan auto-board:process`

**Purpose:** Processes daily AutoBoard distributions at midnight (00:00)

**What it does:**
- Finds the previous day's board with status 'collotion'
- Distributes collected money among eligible accounts
- Updates board status to 'distributed'
- **Automatically creates new board for today** (status: 'collotion')
- Creates audit trail and transaction records
- Updates account balances

**Usage:**
```bash
# Manual execution
php artisan auto-board:process

# With verbose output
php artisan auto-board:process -v
```

## AutoBoard System Architecture

### Daily Flow

```
Day 1 (Today): 00:00 - 23:59
├── Status: 'collotion' (collecting contributions)
├── Package purchases → Update today_collotion_amount
└── Contributions accumulate throughout the day

Day 2 (Next day at 12:00 AM):
├── AutoBoard runs automatically at 00:00
├── Processes Day 1's collection (status: 'collotion' → 'distributed')
└── **Automatically creates new Day 2 board** (status: 'collotion')
```

### Eligibility Requirements

Accounts must meet ALL criteria:
1. **Referral Count**: `direct_referral_count >= system_setting(auto_income_eligibility)`
2. **Status**: `status = 'active'`
3. **Package**: `active_package_id IS NOT NULL` (has purchased package)

### Distribution Logic

- **Equal Distribution**: Total collection ÷ Number of eligible accounts
- **Priority Ordering**: Higher referral count gets priority in ordering
- **Complete Audit Trail**: Creates distributions + transactions + balance updates

## Cron Job Setup

### Option 1: Server Cron (Recommended)

**Edit crontab:**
```bash
crontab -e
```

**Add this line:**
```bash
# Run AutoBoard distribution every day at midnight (00:00)
0 0 * * * cd /path/to/your/laravel/project && php artisan auto-board:process >> storage/logs/cron.log 2>&1
```

**Alternative with detailed logging:**
```bash
# Run at midnight with comprehensive logging
0 0 * * * cd /path/to/your/laravel/project && php artisan auto-board:process >> storage/logs/autoboard.log 2>&1
```

### Option 2: Laravel Task Scheduling

**Add to your server cron (runs Laravel scheduler every minute):**
```bash
* * * * * cd /path/to/your/laravel/project && php artisan schedule:run >> /dev/null 2>&1
```

**The Schedule class is already configured in `app/Console/Schedule.php`**

## Monitoring & Logging

### Check Logs

**Laravel logs:**
```bash
tail -f storage/logs/laravel.log
```

**Cron logs (if using server cron):**
```bash
tail -f storage/logs/cron.log
```

**AutoBoard specific logs:**
```bash
tail -f storage/logs/autoboard.log
```

### Database Verification

**Check distributions:**
```bash
php artisan tinker
DB::table('auto_board_distributions')->count();
```

**Check board status:**
```bash
php artisan tinker
DB::table('auto_boards')->select('distribution_date', 'status', 'today_collotion_amount')->get();
```

## System Settings

### Required Settings

The system reads these settings from the `system_settings` table:

- **`auto_income_eligibility`**: Minimum referral count required (default: 30)
- **`auto_board_distribution_time`**: Daily distribution time (default: 00:00)

### Update Settings

```bash
php artisan tinker

# Update eligibility requirement
DB::table('system_settings')
    ->where('key', 'auto_income_eligibility')
    ->update(['value' => '25']); // Change to 25 referrals

# Update distribution time
DB::table('system_settings')
    ->where('key', 'auto_board_distribution_time')
    ->update(['value' => '01:00']); // Change to 1:00 AM
```

## Troubleshooting

### Common Issues

**1. No eligible accounts found:**
- Check if accounts have sufficient referrals
- Verify accounts are active
- Ensure accounts have purchased packages

**2. Board not in collection status:**
- Board may have already been processed
- Check board status in database

**3. No collection amount:**
- Board may not have received contributions
- Check package purchase records

### Debug Commands

**Test eligibility:**
```bash
php artisan tinker
use App\Helpers\AutoBoardHelper;
AutoBoardHelper::getEligibleAccounts()->count();
```

**Check previous day board:**
```bash
php artisan tinker
use App\Helpers\AutoBoardHelper;
AutoBoardHelper::getPreviousDayBoard();
```

**Verify system settings:**
```bash
php artisan tinker
DB::table('system_settings')->where('key', 'auto_income_eligibility')->first();
```

## File Structure

```
app/Console/Commands/
├── README.md                           # This documentation
├── ProcessAutoBoardDistribution.php    # Main cron job command
└── Schedule.php                        # Laravel task scheduling

app/Helpers/
└── AutoBoardHelper.php                 # Core AutoBoard logic

app/Models/
└── AutoBoard.php                       # AutoBoard model
```

## Dependencies

- **Laravel 11+**
- **MySQL/PostgreSQL** database
- **Carbon** for date handling
- **Eloquent ORM** for database operations

## Security Notes

- Commands should only be run via cron jobs or authorized users
- All financial operations are logged for audit purposes
- Database transactions ensure data integrity
- Error handling prevents partial distributions

## Support

For issues or questions:
1. Check the logs first
2. Verify database settings
3. Test eligibility manually
4. Review system requirements

---

**Last Updated:** August 2025  
**Version:** 1.0  
**Author:** AutoBoard System Team
