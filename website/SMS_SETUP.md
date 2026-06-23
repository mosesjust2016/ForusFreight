# SMS Service Setup - Molo Marketing Cloud

## Current Status
❌ **SMS service is currently DISABLED** - Placeholder credentials are configured.

SMS will log warnings instead of attempting to send messages until you configure real credentials.

---

## How to Enable SMS

### Step 1: Get Your Molo Credentials

Contact your Molo account manager or visit [Molo Marketing Cloud](https://molomarketing.cloud) to get:
- **Email**: Your Molo account email
- **Password**: Your Molo API password
- **SMS Originator**: Your branded sender ID (currently "FORUSFL")

### Step 2: Update .env File

Edit `/Users/moses/Desktop/LinuxProjects/ForusFreight/website/.env` and replace:

```bash
# BEFORE (Placeholder)
MOLO_EMAIL=your-molo-email@example.com
MOLO_PASSWORD=your-molo-password

# AFTER (Your Real Credentials)
MOLO_EMAIL=your-actual-email@molo.com
MOLO_PASSWORD=your-actual-password
```

### Step 3: Verify Configuration

Test the SMS service:
```bash
cd /Users/moses/Desktop/LinuxProjects/ForusFreight/website
php artisan sms:test +260961234567
```

**Expected Output:**
```
Testing SMS to: +260961234567
✅ SMS sent successfully to +260961234567
```

**If SMS is still not configured:**
```
Testing SMS to: +260961234567
⚠️  Molo SMS service is not properly configured.
Please set MOLO_EMAIL and MOLO_PASSWORD in your .env file.
```

---

## Email Service (Already Enabled ✅)

Email is fully configured and ready to use:

```bash
# Test email
php artisan email:test your-email@example.com

# Expected Output:
# ✅ Email sent successfully to your-email@example.com
```

---

## SMS Flow in Application

When a user registers:

1. **Registration Form Submitted**
   - User enters name, email, phone, password

2. **User Created** 
   - Account created in database
   - `phone_verified_at` is NULL initially

3. **OTP Sent**
   - Email OTP → Brevo (✅ Working)
   - SMS OTP → Molo (⚠️ Needs Configuration)

4. **Verification Pages**
   - User visits `/verify-email` (email verification)
   - User visits `/verify-phone` (phone verification)

5. **After Verification**
   - User can access dashboard
   - Both `email_verified_at` and `phone_verified_at` are set

---

## API Endpoints

### Login/Registration SMS Flow
```
POST /register
  ├─ Validate phone number
  ├─ Create user
  ├─ Generate OTP
  ├─ Send Email OTP (Brevo) ✅
  ├─ Send SMS OTP (Molo) ⚠️
  └─ Auto-login user

GET /verify-phone
  └─ Show verification form

POST /verify-phone
  ├─ Validate OTP
  ├─ Mark phone as verified
  └─ Redirect to dashboard
```

---

## Troubleshooting

### SMS Not Sending

**Check 1: Is the service configured?**
```bash
php artisan sms:test +260961234567
```

**Check 2: Are credentials correct?**
- Verify email and password in `.env`
- Test login on Molo dashboard

**Check 3: Is phone number valid?**
- Must be E.164 format: `+260XXXXXXXXX`
- Example: `+260961234567` ✅
- Not valid: `0961234567` ❌

**Check 4: Check the logs**
```bash
tail -f storage/logs/laravel.log | grep -i sms
```

---

## Phone Number Formats Accepted

The SMS service normalizes phone numbers to E.164 format:

| Format | Input | Output | Valid |
|--------|-------|--------|-------|
| With +260 | +260961234567 | +260961234567 | ✅ |
| With 260 | 260961234567 | +260961234567 | ✅ |
| With 0 | 0961234567 | +260961234567 | ✅ |
| International | +27123456789 | +27123456789 | ✅ |

---

## Rate Limiting

To prevent abuse, implement OTP rate limiting:

```php
// In your controller
$rateLimit = Cache::get("otp:sent:{$phone}");
if ($rateLimit) {
    return "Please wait before requesting another OTP";
}

// Send OTP
$service->sendOtp($phone, $otp);

// Cache for 60 seconds
Cache::put("otp:sent:{$phone}", true, 60);
```

---

## Production Checklist

- [ ] Real Molo credentials obtained
- [ ] MOLO_EMAIL configured
- [ ] MOLO_PASSWORD configured
- [ ] `php artisan sms:test` passes
- [ ] SMS OTP sending verified manually
- [ ] Queue system configured (for bulk SMS)
- [ ] Rate limiting implemented
- [ ] Logs monitored
- [ ] API costs tracked
- [ ] Compliance requirements met

---

## Support Resources

- **Molo Marketing Cloud**: [https://molomarketing.cloud](https://molomarketing.cloud)
- **Brevo Email**: [https://www.brevo.com](https://www.brevo.com)
- **Green API WhatsApp**: [https://green-api.com](https://green-api.com)

---

## Common Issues

### "SMS service is not properly configured"
**Solution:** Update MOLO_EMAIL and MOLO_PASSWORD in .env with real credentials

### "Could not obtain a valid access token"
**Solution:** Verify credentials are correct and Molo account is active

### "Invalid phone number format"
**Solution:** Use E.164 format with country code (+260...)

### Logs show "Molo SMS: could not obtain a valid access token"
**Solution:** Check that email and password are correct, or contact Molo support

---

## Cost Estimation

**Typical Usage Patterns:**
- Registration: 1 Email + 1 SMS per new user
- Verification: 1 SMS resend (if needed)
- Notifications: Variable based on business

**Approximate Monthly Costs** (rough estimate):
- Email: 100,000 @ $0.00015 = $15
- SMS: 10,000 @ $0.05 = $500
- WhatsApp: Variable

Monitor usage and set alerts for unexpected spikes!

---

## Next: Ecommerce Module

Once SMS is working on the website, apply same configuration to:
- `Ecommerce/forus-digital-api/`
- `Ecommerce/forus-digital-admin-portal/`
- `Ecommerce/forus-digital-storefront/`
