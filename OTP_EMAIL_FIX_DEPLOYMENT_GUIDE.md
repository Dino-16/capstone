# OTP Email Fix - Production Deployment Guide

## Problem
OTP emails work locally but fail on the production domain (hr1.jetlougetravels-ph.com) due to:
- Port blocking by hosting provider
- SSL/TLS verification issues
- Network configuration differences

## Changes Made Locally

### 1. Updated .env Configuration
Changed from SSL (port 465) to TLS (port 587):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=olearenzemarkmendoza@gmail.com
MAIL_PASSWORD=uphlzecujtkcguxi
MAIL_ENCRYPTION=tls
```

### 2. Enhanced config/mail.php
Added SSL verification options:
```php
'verify_peer' => env('MAIL_VERIFY_PEER', true),
'verify_peer_name' => env('MAIL_VERIFY_PEER_NAME', true),
```

### 3. Created CustomMailServiceProvider
Location: `app/Providers/CustomMailServiceProvider.php`
This provider handles SSL verification bypass when needed.

### 4. Registered the Provider
Added to `bootstrap/providers.php`

## Deployment Steps for Production Server

### Step 1: Update Production .env File
SSH into your server or use your hosting control panel to edit the `.env` file on your production server at `hr1.jetlougetravels-ph.com`.

Update these lines:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=olearenzemarkmendoza@gmail.com
MAIL_PASSWORD=uphlzecujtkcguxi
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=olearenzemarkmendoza@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 2: Upload Updated Files
Upload these modified files to your production server:
- `config/mail.php`
- `app/Providers/CustomMailServiceProvider.php`
- `bootstrap/providers.php`

### Step 3: Clear Cache on Production
Run these commands on your production server:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 4: Test the Configuration

#### Option A: If Port 587 Works (Most Common)
The configuration should now work. Test by logging in.

#### Option B: If Port 587 is Also Blocked
Some hosting providers block both ports. Add these to production `.env`:
```env
MAIL_VERIFY_PEER=false
MAIL_VERIFY_PEER_NAME=false
```

Then run `php artisan config:clear` again.

#### Option C: If Still Not Working - Alternative Solutions

**Solution 1: Use a Different SMTP Service**
Consider using:
- **Mailtrap** (for development/staging)
- **SendGrid** (free tier: 100 emails/day)
- **Mailgun** (free tier: 10,000 emails/month)
- **Amazon SES** (cheap and reliable)

**Solution 2: Use Gmail API Instead of SMTP**
Gmail API is more reliable than SMTP for production.

**Solution 3: Contact Your Hosting Provider**
Ask them to:
- Unblock outbound SMTP on port 587
- Provide their recommended SMTP configuration
- Or enable the `fsockopen()` and `stream_socket_client()` functions

### Step 5: Verify Gmail Security Settings  
Make sure:
1. "Less secure app access" is enabled (if Gmail account type supports it)
2. The app password `uphlzecujtkcguxi` is still valid
3. Gmail hasn't flagged the server's IP as suspicious

## Testing Checklist
- [ ] Updated production .env file
- [ ] Uploaded all modified files
- [ ] Ran cache clearing commands
- [ ] Tested login with OTP
- [ ] Checked Laravel logs: `storage/logs/laravel.log`
- [ ] Verified email was sent (check Gmail sent folder)

## Debugging Commands (Run on Production Server)
```bash
# Check if port 587 is accessible
telnet smtp.gmail.com 587

# Check Laravel logs
tail -n 50 storage/logs/laravel.log

# Test email configuration
php artisan tinker
Mail::raw('Test email', function($msg) { $msg->to('your-email@example.com')->subject('Test'); });
```

## Alternative: Quick Test with Mailtrap
If you need a quick working solution for testing:

1. Sign up at https://mailtrap.io (free)
2. Get your credentials from Mailtrap inbox
3. Update production `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

This will allow you to test OTP functionality (emails go to Mailtrap inbox instead of real addresses).

## Common Hosting Provider Solutions

### cPanel/Shared Hosting
- Usually blocks ports 465 and 25
- Port 587 typically works
- May need to use VPS if blocked

### DigitalOcean/VPS
- All ports should work
- May need to configure firewall rules

### AWS/Heroku
- Often blocks SMTP ports
- Recommended to use Amazon SES or SendGrid

## Need More Help?
If issues persist after trying these solutions, please provide:
1. Error message from `storage/logs/laravel.log`
2. Your hosting provider name
3. Result of `telnet smtp.gmail.com 587` command
