# AI Extraction Troubleshooting Guide

## Issues Fixed

### ✅ Issue 1: Qualification Status Misalignment
**Problem:** The qualification status shows "Not Qualified" even when AI ratings are 90+ (should be "Exceptional")

**Root Cause:** The AI was providing incorrect qualification strings that didn't match the scores.

**Solution:** Modified `ApplyNow.php` to ALWAYS calculate qualification status from the rating score, ignoring AI-provided qualification strings.

**Score Ranges:**
- 90-100: Exceptional
- 80-89: Highly Qualified
- 70-79: Qualified
- 60-69: Moderately Qualified
- 50-59: Marginally Qualified
- 0-49: Not Qualified

### ✅ Issue 2: AI Extraction Not Working on Production Domain
**Problem:** AI extraction works locally but fails on the production domain.

**Solution:** Added comprehensive error logging and checks for common production issues.

---

## How to Fix Existing Misaligned Records

You have 3 options to fix existing records:

### Option 1: Using Artisan Command (Recommended)
```bash
# First, preview what will change (dry run)
php artisan fix:qualification-status --dry-run

# Then apply the changes
php artisan fix:qualification-status
```

### Option 2: Using SQL
```bash
# Run the SQL script directly
mysql -u your_username -p your_database < fix_qualification_status.sql

# Or use Laravel's DB connection
php artisan db
# Then paste the UPDATE query from fix_qualification_status.sql
```

### Option 3: Manual Fix in Applications Component
The Applications component already has the fix in the `updateFilteredResume()` method (lines 264-282). Simply edit and save each record through the UI.

---

## Diagnosing Production Issues

### Step 1: Run the Diagnostic Script
Upload `diagnose_ai_extraction.php` to your production server and run:

```bash
php diagnose_ai_extraction.php
```

This will check:
- ✓ PHP version and extensions
- ✓ Laravel installation
- ✓ PDF Parser package availability
- ✓ OpenAI package configuration
- ✓ API key setup
- ✓ File permissions
- ✓ OpenAI API connectivity
- ✓ Log file access
- ✓ Memory and timeout limits

### Step 2: Check Laravel Logs
After someone submits an application, check the logs:

```bash
tail -f storage/logs/laravel.log
```

Look for these specific log entries:
- `"PDF parsed successfully"` - PDF parsing is working
- `"OpenAI API response received"` - AI is responding
- `"AI resume analysis successful"` - Full process completed
- Any ERROR or WARNING messages

### Step 3: Common Issues and Solutions

#### Issue: PdfParser class not found
```bash
composer require smalot/pdfparser
```

#### Issue: OpenAI API key not set
1. Check `.env` file has `OPENAI_API_KEY=your_key_here`
2. Clear config cache: `php artisan config:clear`
3. Restart your web server

#### Issue: File permission errors
```bash
# Make storage writable
chmod -R 775 storage/
chown -R www-data:www-data storage/

# Create resumes directory if missing
mkdir -p storage/app/public/resumes
chmod -R 775 storage/app/public/resumes
```

#### Issue: OpenAI SSL errors
Add to `.env`:
```
OPENAI_SSL_VERIFY=false
```

Then clear config:
```bash
php artisan config:clear
```

#### Issue: Memory or timeout errors
Edit `php.ini` or `.htaccess`:
```
memory_limit = 256M
max_execution_time = 120
upload_max_filesize = 10M
post_max_size = 10M
```

#### Issue: Composer timeout during install
```bash
composer install --no-scripts
composer dump-autoload
```

---

## Testing the Fix

### Test Locally:
1. Submit a test application with a resume
2. Check `storage/logs/laravel.log` for the AI analysis logs
3. Go to Applications page and verify the qualification status matches the score

### Test on Production:
1. Upload the updated `ApplyNow.php` file
2. Run diagnostics: `php diagnose_ai_extraction.php`
3. Fix any issues shown with ❌ or ⚠ symbols
4. Submit a test application
5. Monitor logs: `tail -f storage/logs/laravel.log`
6. Verify the application appears with correct rating and qualification

---

## Deployment Checklist

When deploying to production:

- [ ] Upload updated `app/Livewire/Website/ApplyNow.php`
- [ ] Ensure `.env` has valid `OPENAI_API_KEY`
- [ ] Run `php diagnose_ai_extraction.php` to verify setup
- [ ] Check `composer.json` includes:
  - `"smalot/pdfparser": "^2.0"`
  - `"openai-php/laravel": "^0.8"`
- [ ] Run `composer install` if packages are missing
- [ ] Set proper file permissions (775 for storage)
- [ ] Clear caches: `php artisan config:clear && php artisan cache:clear`
- [ ] Fix existing records: `php artisan fix:qualification-status`
- [ ] Test with a sample application
- [ ] Monitor logs for any errors

---

## Need More Help?

### Check These Log Entries:
The enhanced logging will now show exactly where the process fails:

1. **"Resume file does not exist"** → File upload issue
2. **"Resume file is not readable"** → Permission issue
3. **"PdfParser class not found"** → Package not installed
4. **"PDF parsing failed"** → Corrupt PDF or parser issue
5. **"Attempting OpenAI API call"** with `"api_key_set": false` → .env issue
6. **"OpenAI API call failed"** → Network, API key, or quota issue
7. **"Resume content is empty"** → PDF couldn't be read

### Get Support:
- Include the output from `diagnose_ai_extraction.php`
- Share relevant log entries from `storage/logs/laravel.log`
- Specify your server environment (PHP version, hosting provider)
