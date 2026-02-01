# Production Deployment Guide - AI Extraction Fix

## 📋 Pre-Deployment Checklist

Before you start, ensure you have:
- [ ] FTP/SSH access to your production server
- [ ] Database credentials (for fixing existing records)
- [ ] OpenAI API key (check `.env` file)
- [ ] Backup of current production files
- [ ] Backup of production database

---

## 🚀 Deployment Steps

### Step 1: Upload Updated File

Upload the following file to your production server:

**File:** `app/Livewire/Website/ApplyNow.php`
**Location:** `/path/to/your/project/app/Livewire/Website/ApplyNow.php`

Using FTP, SFTP, or Git:
```bash
# If using Git
git pull origin main

# If using FTP/SFTP
# Upload: app/Livewire/Website/ApplyNow.php
```

### Step 2: Upload Diagnostic Tools

Upload these helper files to your project root:

1. `diagnose_ai_extraction.php` → `/path/to/your/project/diagnose_ai_extraction.php`
2. `app/Console/Commands/FixQualificationStatus.php` → `/path/to/your/project/app/Console/Commands/FixQualificationStatus.php`

### Step 3: Clear Laravel Caches

SSH into your server and run:
```bash
cd /path/to/your/project

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild autoload
composer dump-autoload
```

### Step 4: Run Diagnostics

Still in SSH, run the diagnostic script:
```bash
php diagnose_ai_extraction.php
```

**What to look for:**
- ✅ All items should have green checkmarks
- ❌ Red X marks indicate problems that need fixing
- ⚠ Yellow warnings may need attention

### Step 5: Fix Common Issues

Based on diagnostic results:

#### If "PdfParser class NOT found":
```bash
composer require smalot/pdfparser
```

#### If "OpenAI facade NOT found":
```bash
composer require openai-php/laravel
```

#### If "OPENAI_API_KEY is NOT set":
```bash
nano .env
# Add: OPENAI_API_KEY=your_actual_key_here
# Save and exit (Ctrl+X, Y, Enter)

php artisan config:clear
```

#### If "Storage directory is NOT writable":
```bash
chmod -R 775 storage/
chown -R www-data:www-data storage/
# Or replace www-data with your web server user (apache, nginx, etc.)
```

#### If SSL verification errors:
```bash
nano .env
# Add: OPENAI_SSL_VERIFY=false
# Save and exit

php artisan config:clear
```

### Step 6: Fix Existing Database Records

Run the artisan command to fix all misaligned qualification statuses:
```bash
# First, preview what will change
php artisan fix:qualification-status --dry-run

# Then apply the changes
php artisan fix:qualification-status
```

**Alternative:** If artisan doesn't work, use SQL directly:
```bash
# Upload fix_qualification_status.sql to your server
mysql -u your_db_user -p your_db_name < fix_qualification_status.sql
```

### Step 7: Test the Application

1. **Submit a Test Application:**
   - Go to your job application page
   - Fill out the form with test data
   - Upload a sample resume (PDF)
   - Submit

2. **Check the Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   
   Look for:
   - `[INFO] PDF parsed successfully`
   - `[INFO] Attempting OpenAI API call`
   - `[INFO] OpenAI API response received`
   - `[INFO] AI resume analysis successful`

3. **Verify in Admin Panel:**
   - Go to Applications page
   - Check that the rating score and qualification status align
   - Example: Score: 95.6 should show "Exceptional" (green)

### Step 8: Monitor for 24 Hours

Keep an eye on the logs for any new errors:
```bash
# Check logs periodically
tail -100 storage/logs/laravel.log

# Or set up real-time monitoring
tail -f storage/logs/laravel.log | grep -E 'ERROR|WARNING|AI resume'
```

---

## 🔍 Verification Checklist

After deployment, verify:

### ✅ Code Deployment
- [ ] Updated `ApplyNow.php` is on the server
- [ ] File permissions are correct (644 for PHP files)
- [ ] Caches have been cleared

### ✅ Dependencies
- [ ] `smalot/pdfparser` is installed
- [ ] `openai-php/laravel` is installed
- [ ] `composer.lock` is up to date

### ✅ Configuration
- [ ] `.env` has valid `OPENAI_API_KEY`
- [ ] `.env` has `OPENAI_SSL_VERIFY=false` (if needed)
- [ ] Config cache is cleared

### ✅ File System
- [ ] `storage/` is writable (775)
- [ ] `storage/app/public/resumes/` exists and is writable
- [ ] Symbolic link exists: `public/storage` → `storage/app/public`

### ✅ Database
- [ ] Existing records have been updated
- [ ] All qualification statuses now align with scores

### ✅ Testing
- [ ] Test application submitted successfully
- [ ] AI extraction completed without errors
- [ ] Rating score and qualification status are aligned
- [ ] Logs show successful processing

---

## 🆘 Troubleshooting Guide

### Problem: "Class not found" errors
**Solution:**
```bash
composer install --no-dev
composer dump-autoload
```

### Problem: AI extraction still fails silently
**Solution:**
```bash
# Check what error is being logged
tail -100 storage/logs/laravel.log | grep -A 5 "ERROR"

# Enable debug mode temporarily (CAUTION: Only on test server)
nano .env
# Set: APP_DEBUG=true
# Save, then check error messages in browser
```

### Problem: OpenAI API timeout
**Solution:**
```bash
# Increase PHP timeouts in php.ini or .htaccess
max_execution_time=120
memory_limit=256M
```

### Problem: "Too many requests" from OpenAI
**Solution:**
- Check your OpenAI usage quota
- Verify API key is valid
- Consider upgrading OpenAI plan if needed

### Problem: PDF parsing fails
**Solution:**
```bash
# Test PDF parser directly
php -r "require 'vendor/autoload.php'; \$p = new \Smalot\PdfParser\Parser(); echo 'OK';"

# If fails, reinstall
composer remove smalot/pdfparser
composer require smalot/pdfparser
```

---

## 📊 Expected Results

### Before Fix:
| Score | Status (Wrong) |
|-------|----------------|
| 93.8  | Not Qualified ❌ |
| 95.6  | Not Qualified ❌ |
| 93.8  | Not Qualified ❌ |

### After Fix:
| Score | Status (Correct) |
|-------|------------------|
| 93.8  | Exceptional ✅ |
| 95.6  | Exceptional ✅ |
| 93.8  | Exceptional ✅ |

---

## 🔐 Security Notes

1. **Never commit `.env` file** - It contains sensitive API keys
2. **Disable debug mode in production** - Set `APP_DEBUG=false`
3. **Keep API keys secure** - Don't log or expose them
4. **Monitor API usage** - Check OpenAI dashboard for unusual activity
5. **Backup database before fixes** - Always have a rollback plan

---

## 📞 Support

If you encounter issues during deployment:

1. **Collect Information:**
   - Output from `diagnose_ai_extraction.php`
   - Last 100 lines of `storage/logs/laravel.log`
   - PHP version: `php -v`
   - Laravel version: `php artisan --version`
   - Server environment (cPanel, Plesk, VPS, etc.)

2. **Common Hosting Issues:**
   - **Shared Hosting:** May not allow Composer, may have restricted `curl`
   - **Cloudflare:** May block OpenAI API calls
   - **Firewall:** May need to whitelist OpenAI IP addresses

3. **Review Documentation:**
   - `AI_EXTRACTION_TROUBLESHOOTING.md` - Detailed troubleshooting
   - `FIX_SUMMARY.md` - Quick reference guide

---

## ✅ Deployment Complete

Once all checks pass:
- Test application flow works end-to-end
- Qualification statuses are aligned with scores
- No errors in logs after submitting applications
- Diagnostic script shows all green checkmarks

**Your AI extraction is now working correctly! 🎉**
