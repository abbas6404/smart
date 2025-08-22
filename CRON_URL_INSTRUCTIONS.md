# Cron URL Endpoints for Purchase Referral Updates

## 🎯 Overview

This document explains how to use the web URL endpoints to trigger purchase referral count updates via external cron services.

## 🌐 Available Endpoints

### 1. **Update Purchase Referral Counts**
- **URL**: `https://yourdomain.com/cron/update-purchase-referrals`
- **Method**: `GET`
- **Purpose**: Updates purchase referral counts for all sub-accounts
- **Security**: No authentication required

### 2. **Process AutoBoard Distribution**
- **URL**: `https://yourdomain.com/cron/process-auto-board`
- **Method**: `GET`
- **Purpose**: Processes AutoBoard collections and distributes to eligible accounts
- **Security**: No authentication required

### 3. **Health Check**
- **URL**: `https://yourdomain.com/cron/health`
- **Method**: `GET`
- **Purpose**: Check if the service is running and healthy
- **Security**: No authentication required

## 🔐 Security

The endpoints are publicly accessible for easy cron job integration:

```
https://yourdomain.com/cron/update-purchase-referrals
https://yourdomain.com/cron/process-auto-board
https://yourdomain.com/cron/health
```

## ⚙️ Configuration

No configuration required! The endpoints are ready to use immediately.

## 📋 Cron Job Setup Examples

### **Linux/Ubuntu Server (crontab)**

```bash
# Edit crontab
crontab -e

# Update purchase referral counts every hour
0 * * * * curl -s "https://yourdomain.com/cron/update-purchase-referrals" > /dev/null 2>&1

# Process AutoBoard distribution daily at midnight
0 0 * * * curl -s "https://yourdomain.com/cron/process-auto-board" > /dev/null 2>&1

# Or run AutoBoard every 6 hours
0 */6 * * * curl -s "https://yourdomain.com/cron/process-auto-board" > /dev/null 2>&1
```

### **Windows Task Scheduler**

1. Create a batch file `update-referrals.bat`:
```batch
@echo off
curl -s "https://yourdomain.com/cron/update-purchase-referrals"
```

2. Create a batch file `process-auto-board.bat`:
```batch
@echo off
curl -s "https://yourdomain.com/cron/process-auto-board"
```

2. Schedule it to run at your desired frequency

### **Shared Hosting (cPanel)**

In cPanel Cron Jobs section:
```bash
# Update purchase referral counts every hour
0 * * * * curl -s "https://yourdomain.com/cron/update-purchase-referrals"

# Process AutoBoard distribution daily at midnight
0 0 * * * curl -s "https://yourdomain.com/cron/process-auto-board"
```

### **External Cron Services**

#### **EasyCron.com**
- **Purchase Referrals**: `https://yourdomain.com/cron/update-purchase-referrals` (hourly)
- **AutoBoard**: `https://yourdomain.com/cron/process-auto-board` (daily at midnight)

#### **Cron-job.org**
- **Purchase Referrals**: `https://yourdomain.com/cron/update-purchase-referrals` (hourly)
- **AutoBoard**: `https://yourdomain.com/cron/process-auto-board` (daily)

#### **SetCronJob.com**
- **Purchase Referrals**: `https://yourdomain.com/cron/update-purchase-referrals` (hourly)
- **AutoBoard**: `https://yourdomain.com/cron/process-auto-board` (daily)

## 📊 Response Examples

### **Successful Update Response**
```json
{
    "success": true,
    "message": "Purchase referral counts updated successfully",
    "timestamp": "2025-01-27T10:30:00.000000Z",
    "data": {
        "total_accounts": 150,
        "updated_accounts": 23,
        "execution_time_ms": 1250.45,
        "errors_count": 0
    }
}
```

### **Successful AutoBoard Response**
```json
{
    "success": true,
    "message": "AutoBoard distribution processed successfully",
    "timestamp": "2025-01-27T10:30:00.000000Z",
    "data": {
        "status": "completed",
        "message": "AutoBoard distribution cycle completed"
    }
}
```

### **Health Check Response**
```json
{
    "success": true,
    "message": "Purchase Referral Service is healthy",
    "timestamp": "2025-01-27T10:30:00.000000Z",
    "data": {
        "total_accounts": 150,
        "accounts_with_referrals": 89,
        "last_check": "2025-01-27 10:30:00"
    }
}
```

### **Error Response**
```json
{
    "success": false,
    "message": "Internal server error",
    "error": "Database connection failed",
    "timestamp": "2025-01-27T10:30:00.000000Z"
}
```

## 🔍 Monitoring & Logging

### **Check Laravel Logs**
```bash
# View recent cron activity
tail -f storage/logs/laravel.log | grep "Cron:"

# View successful updates
grep "Cron: Purchase referral update triggered" storage/logs/laravel.log

# View errors
grep "Cron: Purchase referral update error" storage/logs/laravel.log
```

### **Test Endpoints Manually**
```bash
# Test health check
curl "https://yourdomain.com/cron/health"

# Test update endpoint
curl "https://yourdomain.com/cron/update-purchase-referrals"
```

## ⚠️ Important Notes

1. **Public Access**: Endpoints are publicly accessible for easy integration
2. **Rate Limiting**: Consider adding rate limiting if needed
3. **IP Logging**: All cron requests are logged with IP addresses
4. **Error Handling**: Failed updates are logged with detailed error messages
5. **Performance**: Updates run in the background and may take time for large datasets

## 🚀 Benefits

- **No Server Access Required**: Works from any external cron service
- **Simple Setup**: No authentication or configuration needed
- **Monitored**: All requests are logged for debugging
- **Flexible**: Can be called from any external service
- **Reliable**: Returns detailed success/error information

## 🔧 Troubleshooting

### **404 Not Found Error**
- Check if the URL is correct
- Verify the route is properly registered

### **500 Internal Server Error**
- Check Laravel logs for detailed error messages
- Verify database connectivity
- Check if the PurchaseReferralHelper is working

### **Slow Response Times**
- Large datasets may take time to process
- Consider running during off-peak hours
- Monitor execution time in the response

## 📞 Support

If you encounter issues:
1. Check the health endpoint first
2. Review Laravel logs for error details
3. Test manually with curl to verify the endpoint
4. Check if the routes are properly registered
