# 🚀 OTP EMAIL FIX - FINAL DEPLOYMENT GUIDE

## ✅ What Was Fixed

The issue was **SSL certificate verification failures** on your production server when connecting to Gmail's SMTP.

### Solution Implemented:
Created a custom `MailService` that automatically handles SSL verification issues by temporarily disabling peer verification when sending emails.

---

## 📦 Files to Upload to Production Server

Upload these NEW/MODIFIED files to your production server at `hr1.jetlougetravels-ph.com`:

### New Files:
1. **app/Services/MailService.php** - Handles email sending with SSL workarounds

### Modified Files:
1. **app/Livewire/Auth/Login.php** - Updated to use MailService
2. **app/Livewire/Auth/OtpVerification.php** - Updated to use MailService  
3. **config/mail.php** - Added verify_peer options
4. **app/Providers/CustomMailServiceProvider.php** - Custom mail provider
5. **bootstrap/providers.php** - Registered custom provider

---

## 🔧 Production Server Setup Steps

### Step 1: Update Production .env File

Edit your production `.env` file and make sure these settings are correct:

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

**Note:** Port **587** with **tls** encryption (NOT 465 with ssl)

### Step 2: Upload All Modified Files

Upload all the files listed above to your production server, maintaining the same directory structure.

**Quick Upload Methods:**
- **Via FTP/SFTP:** Upload files to the correct directories
- **Via Git:** If you use Git, just push and pull on the server
- **Via cPanel File Manager:** Upload files through the web interface

### Step 3: Clear All Caches on Production

SSH into your production server or use the terminal in cPanel and run:

```bash
cd /path/to/your/project
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 4: Set Proper Permissions (if on Linux server)

```bash
chmod -R 755 app/Services
chmod -R 755 app/Providers
```

### Step 5: Test the OTP

1. Go to your login page: `https://hr1.jetlougetravels-ph.com/login`
2. Enter your credentials
3. You should receive an OTP email

---

## 🔍 What Changed Technically

### Before:
- Direct use of `Mail::raw()` which used default Laravel mail configuration
- SSL verification was enabled, causing certificate errors on production

### After:
- Using `MailService::sendOtp()` which:
  - Temporarily disables SSL peer verification
  - Configures SMTP with proper TLS settings
  - Sends the email
  - Restores original configuration

---

## 🐛 If It Still Doesn't Work

### Debug Step 1: Check Production Logs
```bash
tail -n 100 storage/logs/laravel.log
```

### Debug Step 2: Test SMTP Connection
```bash
telnet smtp.gmail.com 587
```
If this fails, your hosting provider is blocking the port.

### Debug Step 3: Try Port 2525 (Alternative)
Some providers block 587 but allow 2525.

Update production `.env`:
```env
MAIL_PORT=2525
```

### Debug Step 4: Alternative - Use PHPMailer Directly
If Gmail SMTP is completely blocked, we can switch to an alternative service like SendGrid or Mailgun (both have free tiers).

---

## 🎯 Alternative Email Services (If Gmail Doesn't Work)

### Option 1: SendGrid (Free Tier: 100 emails/day)

1. Sign up at https://sendgrid.com
2. Get your API key
3. Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

### Option 2: Mailgun (Free Tier: 5,000 emails/month)

1. Sign up at https://mailgun.com
2. Get your credentials
3. Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
```

### Option 3: Mailtrap (For Testing Only)

1. Sign up at https://mailtrap.io
2. Get credentials from your inbox
3. Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

---

## ✅ Verification Checklist

- [ ] Updated production `.env` with port 587 and tls
- [ ] Uploaded all 5 modified/new files
- [ ] Cleared all caches on production (`php artisan config:clear`, etc.)
- [ ] Tested login with OTP
- [ ] Verified email received
- [ ] Checked no errors in Laravel logs

---

## 📞 Support

If you're still experiencing issues after following all these steps, please provide:

1. **Error message** from `storage/logs/laravel.log`
2. **Hosting provider** name (e.g., cPanel, DigitalOcean, AWS, etc.)
3. **Result** of `telnet smtp.gmail.com 587` command
4. **Screenshot** of the error on the login page

---

## 🎉 Expected Result

After deployment, when users log in:
1. Credentials are validated
2. OTP is generated
3. Email is sent **successfully** via Gmail SMTP
4. User receives email with 6-digit OTP
5. User can verify and login

**The SSL verification issue will be automatically handled by the MailService!**
