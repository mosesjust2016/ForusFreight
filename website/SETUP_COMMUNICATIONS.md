# Communications Setup Guide

This document describes how to configure email and SMS services for the Forus Freight application.

## Email (Brevo Transactional Email)

### Configuration

1. **Get Your API Key**
   - Sign up at [Brevo](https://www.brevo.com)
   - Navigate to Settings → SMTP & API
   - Copy your API key

2. **Update .env**
   ```bash
   BREVO_API_KEY=xkeysib-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   BREVO_SENDER_EMAIL=noreply@forusfl.co.zm
   BREVO_SENDER_NAME="Forus Freight"
   ```

3. **Verify Configuration**
   ```bash
   php artisan email:test recipient@example.com
   ```

### Supported Email Events

- **User Registration**: OTP email sent during registration
- **Email Verification**: Verification code sent to new email addresses
- **Password Reset**: Reset link sent via email
- **Shipment Notifications**: Updates sent to customers

---

## SMS (Molo Marketing Cloud)

### Configuration

1. **Get Your Credentials**
   - Contact Molo Marketing Cloud support
   - Request API credentials (email and password)

2. **Update .env**
   ```bash
   MOLO_EMAIL=your-email@molo.example.com
   MOLO_PASSWORD=your-molo-password
   MOLO_SMS_ORIGINATOR=FORUSFL  # Your SMS sender ID
   MOLO_SMS_URL=https://api.molomarketing.cloud
   ```

3. **Verify Configuration**
   ```bash
   php artisan sms:test +260961234567
   ```

### Supported SMS Events

- **User Registration**: OTP sent during registration
- **Phone Verification**: Verification code sent to phone
- **Shipment Updates**: Status updates sent to customers
- **Two-Factor Authentication**: OTP sent during login

---

## WhatsApp (Green API)

### Configuration

1. **Get Your Credentials**
   - Sign up at [Green API](https://green-api.com)
   - Create WhatsApp instance
   - Get Instance ID and Token

2. **Update .env**
   ```bash
   GREEN_API_INSTANCE_ID=waInstanceXXXXXXXXXX
   GREEN_API_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   GREEN_API_BASE_URL=https://api.green-api.com
   ```

### Supported WhatsApp Events

- **Bulk Notifications**: Send campaigns to multiple customers
- **Customer Support**: Two-way messaging for support tickets
- **Shipment Notifications**: Status updates via WhatsApp

---

## Testing Communication Services

### Test Email
```bash
# Send test OTP email to default address
php artisan email:test

# Send test OTP email to specific address
php artisan email:test user@example.com
```

### Test SMS
```bash
# Send test OTP SMS to phone number
php artisan sms:test +260961234567
```

### View Logs
All communication attempts are logged:
```bash
tail -f storage/logs/laravel.log
```

---

## Troubleshooting

### Email Not Sending
- ✅ Check that `BREVO_API_KEY` is set and valid
- ✅ Verify sender email domain is verified in Brevo
- ✅ Check logs: `storage/logs/laravel.log`
- ✅ Test with: `php artisan email:test`

### SMS Not Sending
- ✅ Check that `MOLO_EMAIL` and `MOLO_PASSWORD` are correct
- ✅ Verify phone number is in E.164 format (+260XXXXXXXXX)
- ✅ Check logs: `storage/logs/laravel.log`
- ✅ Test with: `php artisan sms:test +260961234567`

### Error Handling
- **Missing Configuration**: Services will log warnings but not crash
- **API Failures**: Automatic retry logic with token refresh
- **Rate Limiting**: Implement queue system for bulk messages

---

## Queue System

For production, it's recommended to use queues for communication:

```bash
# Configure in .env
QUEUE_CONNECTION=database

# Create queue table
php artisan queue:table
php artisan migrate

# Start queue worker
php artisan queue:work
```

---

## Security Notes

- 🔒 Never commit `.env` file with credentials
- 🔒 Rotate API keys regularly
- 🔒 Use environment variables for sensitive data
- 🔒 Monitor API usage and costs
- 🔒 Implement rate limiting for OTP resends

---

## Support

For issues with:
- **Brevo Email**: [Brevo Support](https://www.brevo.com/support)
- **Molo SMS**: Contact your Molo account manager
- **Green API WhatsApp**: [Green API Docs](https://green-api.com/docs)
