# Testing MailService Locally

## Quick Test

Run this in your terminal to test the MailService:

```bash
php artisan tinker
```

Then paste this code:

```php
$result = \App\Services\MailService::sendOtp('test@example.com', '123456', 'Test OTP');
var_dump($result);
exit;
```

## Expected Output

If working correctly, you should see:
```
array(2) {
  ["success"]=>
  bool(true)
  ["message"]=>
  string(21) "OTP sent successfully"
}
```

If there's an error, you'll see:
```
array(2) {
  ["success"]=>
  bool(false)
  ["message"]=>
  string(...) "Failed to send OTP: [error message]"
}
```

## Common Errors

### "Could not establish connection"
- Check MAIL_HOST, MAIL_PORT in .env
- Make sure port 587 is not blocked
- Try `telnet smtp.gmail.com 587`

### "Authentication failed"
- Check MAIL_USERNAME and MAIL_PASSWORD
- Verify app password is still valid
- Try generating a new app password

### "Address must not be null"
- Check MAIL_FROM_ADDRESS in .env
- Make sure it's set to a valid email
- Try: `php artisan config:clear`
