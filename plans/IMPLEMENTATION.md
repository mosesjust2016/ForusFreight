# Shipment Notifications Implementation

**Date Implemented**: June 13, 2026  
**Status**: ✅ Complete and Ready for Testing

---

## Overview

This document details the implementation of automated shipment notifications for the ForusFreight application. Customers now receive:

1. **Email notification** when their shipment is created
2. **Email notification** when shipment status changes
3. **SMS notification** (optional) when status changes
4. **Automatic TrackingEvent creation** for audit trail and public tracking

---

## Components Implemented

### 1. ShipmentObserver (`app/Observers/ShipmentObserver.php`)

**Purpose**: Listens to Shipment model events and triggers notifications

**Events Handled**:
- `created`: Fires when shipment is first created
- `updated`: Fires when shipment fields change
- `deleted`: Fires when shipment is soft-deleted

**Key Logic**:
```php
// On creation:
- Send ShipmentCreatedNotification email
- Create initial TrackingEvent

// On update:
- Check if status changed (wasChanged('status'))
- If status changed:
  - Create TrackingEvent with old → new status
  - Send ShipmentStatusUpdatedNotification email
  - Send SMS via SmsService (if phone exists)
```

**Error Handling**:
- All email/SMS send failures are caught and logged
- Doesn't block shipment creation/update if notification fails

### 2. Mail Notification Classes

#### `ShipmentCreatedNotification` (`app/Mail/ShipmentCreatedNotification.php`)

**Implements**: `ShouldQueue` (async processing)

**Subject**: "Shipment Confirmation - Tracking #{tracking_number}"

**View**: `emails/shipment-created.blade.php`

**Data Passed**:
```php
[
    'shipment' => Shipment object,
    'customerName' => string,
    'trackingNumber' => string,
    'origin' => string,
    'destination' => string,
    'status' => string,
    'weight' => float,
    'estimatedDelivery' => datetime|null,
    'cost' => float|null,
    'trackingUrl' => string (URL to tracking page),
]
```

#### `ShipmentStatusUpdatedNotification` (`app/Mail/ShipmentStatusUpdatedNotification.php`)

**Implements**: `ShouldQueue` (async processing)

**Subject**: "Shipment Update - {status} - #{tracking_number}"

**View**: `emails/shipment-status-updated.blade.php`

**Data Passed**:
```php
[
    'shipment' => Shipment object,
    'customerName' => string,
    'trackingNumber' => string,
    'oldStatus' => string,
    'newStatus' => string,
    'statusEmoji' => string (📦, 🚚, ✅, etc.),
    'origin' => string,
    'destination' => string,
    'estimatedDelivery' => datetime|null,
    'trackingUrl' => string,
    'dashboardUrl' => string,
]
```

### 3. Email Templates

#### `resources/views/emails/shipment-created.blade.php`

Professional HTML email with:
- Header gradient (Forus colors)
- Shipment details card (tracking, status, route, weight, delivery date, cost)
- What's next timeline
- Track shipment button
- WhatsApp contact info (+260572788685)
- Footer with company info

#### `resources/views/emails/shipment-status-updated.blade.php`

Professional HTML email with:
- Status emoji in header
- Status badge (orange)
- Current shipment details
- Dynamic timeline showing progress (created → picked up → in transit → cleared → out for delivery → delivered)
- Track details button
- Tip section with tracking number
- WhatsApp contact info

### 4. Database Updates

#### `app/Models/TrackingEvent.php` (No changes needed)

Existing model already supports:
- `shipment_id` - FK to shipment
- `location` - Where the shipment is/was
- `description` - Event description
- `status` - Current shipment status
- `event_time` - When event occurred

#### `app/Observers/ShipmentObserver.php` creates TrackingEvents

Example creation:
```php
TrackingEvent::create([
    'shipment_id' => $shipment->id,
    'status' => 'In Transit',
    'description' => "Status changed from 'Pending' to 'In Transit'",
    'location' => 'Lusaka Distribution Center',
    'event_time' => now(),
]);
```

### 5. Controller Updates

#### `ShipmentController::store()` (Client Shipment Creation)

**Changes**:
- Added `generateTrackingNumber()` helper method for unique tracking numbers
- Updated flash message to inform user that confirmation email is being sent
- Observer automatically handles email & tracking event

**Before**:
```
"Shipment created successfully! Tracking number: FORUS-DAR-4521"
```

**After**:
```
"Shipment created successfully! You will receive a confirmation email shortly. Tracking number: FORUS-DAR-4521"
```

#### `AdminController::storeShipment()` (Admin Shipment Creation)

**Changes**:
- Added same `generateTrackingNumber()` method
- Updated flash message to indicate customer was notified
- Observer automatically handles email & tracking event

**Message**:
```
"Shipment created successfully! Customer notification sent. Tracking: FORUS-DAR-4521"
```

#### `AdminController::updateShipment()` (Admin Status Update)

**Changes**:
- Detects status changes before update
- After update, observer sends email & creates TrackingEvent if status changed
- Updated flash message to show if status changed and notification was sent

**Message Examples**:
```
"Shipment updated successfully!"
"Shipment updated successfully! Customer notification sent (Pending → In Transit)"
```

### 6. Service Updates

#### `SmsService::sendShipmentUpdate()` (New Method)

**Location**: `app/Services/SmsService.php`

**Purpose**: Send SMS shipment status updates

**Usage**:
```php
app(SmsService::class)->sendShipmentUpdate(
    $phone,
    "Your shipment FORUS-DAR-4521 has been In Transit. Track: forusfreight.com/tracking"
);
```

**Implementation**: Wraps existing `send()` method for clarity

**Message Format Example**:
```
Your Forus Freight shipment FORUS-DAR-4521 has been In Transit. Track: https://forusfreight.com/tracking
```

### 7. Service Provider Registration

#### `AppServiceProvider::boot()` (Updated)

**Changes**:
```php
Shipment::observe(ShipmentObserver::class);
```

This registers the observer to listen to all Shipment model events.

---

## Data Flow

### When Customer Creates Shipment

```
ShipmentController::store()
  ↓
User::create() [Eloquent create]
  ↓
Observers trigger:
  ShipmentObserver::created()
    ├─ Mail::send(ShipmentCreatedNotification)
    │   ├─ Queued async (ShouldQueue)
    │   └─ Sent via configured mail driver (Brevo)
    │
    └─ TrackingEvent::create()
        ├─ status: "Order Placed"
        ├─ description: "Shipment created and confirmed"
        └─ location: Origin location
  ↓
Redirect with flash message
```

### When Admin Updates Shipment Status

```
AdminController::updateShipment()
  ↓
$shipment->update([
    'status' => 'In Transit',
    ...
])
  ↓
Observers trigger:
  ShipmentObserver::updated()
    ├─ Check: wasChanged('status')? 
    │   YES → Continue
    │   NO  → Skip notifications
    │
    ├─ TrackingEvent::create()
    │   ├─ status: "In Transit"
    │   ├─ description: "Status changed from 'Pending' to 'In Transit'"
    │   └─ location: Current location or origin
    │
    ├─ Mail::send(ShipmentStatusUpdatedNotification)
    │   ├─ Queued async (ShouldQueue)
    │   └─ Sent via configured mail driver (Brevo)
    │
    └─ SmsService::sendShipmentUpdate()
        ├─ Gets customer phone from $shipment->user->phone
        ├─ Sends via Molo Marketing Cloud SMS API
        └─ Logged if successful/failed
  ↓
Redirect with flash message
```

---

## Configuration

### Mail Configuration

Uses existing Laravel mail configuration in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_brevo_username
MAIL_PASSWORD=your_brevo_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@forusfreight.com
MAIL_FROM_NAME="Forus Freight"
```

**Currently Uses**: `BrevoMailService` wrapper (can be switched to Laravel's native mail)

### Queue Configuration

Mails are queued by default (`ShouldQueue`):

```env
QUEUE_CONNECTION=database  # or 'redis', 'sync' for testing
```

To process queued jobs:
```bash
php artisan queue:listen --tries=1
```

Or for production with supervisor:
```bash
php artisan queue:work
```

### SMS Configuration

Existing Molo Marketing Cloud configuration:

```env
MOLO_EMAIL=your_molo_email
MOLO_PASSWORD=your_molo_password
MOLO_ORIGINATOR=FORUSFL
MOLO_URL=https://api.molomarketing.cloud
```

---

## Testing

### Manual Testing

#### 1. Test Shipment Creation (Client)

```bash
# Open registration
http://localhost:8000/register

# Create account
# Verify email OTP (123456 in local)
# Verify phone OTP (123456 in local)

# Go to /client/shipments/create
# Fill form and submit

# Check:
# ✅ Flash message says "confirmation email shortly"
# ✅ Email received with tracking number
# ✅ Database: shipments table has new record
# ✅ Database: tracking_events has initial event
```

#### 2. Test Shipment Update (Admin)

```bash
# Login as admin
http://localhost:8000/admin/login

# Go to /admin/shipments
# Click edit on any shipment
# Change status from "Pending" to "In Transit"
# Submit

# Check:
# ✅ Flash message shows status change & notification sent
# ✅ Email received with new status
# ✅ SMS received (if phone configured)
# ✅ Database: tracking_events has new event with status
```

#### 3. Test in Local (Sync Queue)

```bash
# .env
QUEUE_CONNECTION=sync  # Send mail immediately instead of queuing

# Restart app and test creation/update
```

#### 4. Test with MailHog (Local Email Testing)

```bash
# .env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_ENCRYPTION=null

# Run MailHog
docker run -p 1025:1025 -p 8025:8025 mailhog/mailhog

# View emails: http://localhost:8025
# Test creation/update and see emails
```

### Automated Testing (Future)

Create tests in `tests/Feature/ShipmentNotificationTest.php`:

```php
public function test_shipment_created_email_sent()
{
    Mail::fake();
    
    // Create shipment
    $response = $this->post('/client/shipments', [...]);
    
    // Assert email was sent
    Mail::assertSent(ShipmentCreatedNotification::class);
}

public function test_shipment_status_update_creates_tracking_event()
{
    $shipment = Shipment::factory()->create();
    
    $shipment->update(['status' => 'In Transit']);
    
    $event = TrackingEvent::where('shipment_id', $shipment->id)->latest()->first();
    
    $this->assertEquals('In Transit', $event->status);
}
```

---

## Troubleshooting

### Email Not Sending

**Check 1**: Is queue listener running?
```bash
# Check if queue is set to 'sync' for testing
php artisan queue:listen
```

**Check 2**: Mail configuration
```php
// Test mail config
Mail::raw('Test', function ($message) {
    $message->to('test@example.com');
});
```

**Check 3**: Logs
```bash
tail -f storage/logs/laravel.log
```

### SMS Not Sending

**Check 1**: Phone number format
- Must match PhoneCountry dial code
- Example: +260972345678

**Check 2**: Molo credentials
```bash
# Test in tinker
php artisan tinker
> app(SmsService::class)->send('+260972345678', 'Test message')
```

**Check 3**: Logs for API errors
```bash
grep -i "molo\|sms" storage/logs/laravel.log
```

### Notifications Sent But Not to Customer

**Check**: User model has:
- `email` field (for email notifications)
- `phone` field (for SMS notifications)

```php
// In tinker
$user = User::find(1);
$user->email;  // Should not be null
$user->phone;  // Should not be null
```

---

## Disabling Notifications (if needed)

### Temporarily Disable All Notifications

Comment out observer in `AppServiceProvider.php`:

```php
// Shipment::observe(ShipmentObserver::class);
```

### Disable Only Email Notifications

In `ShipmentObserver.php`:

```php
public function created(Shipment $shipment): void
{
    // Comment out:
    // Mail::send(new ShipmentCreatedNotification($shipment));
    
    TrackingEvent::create([...]);
}
```

### Disable Only SMS Notifications

In `ShipmentObserver.php`, `updated()` method:

```php
// Comment out:
// app(SmsService::class)->sendShipmentUpdate(...)
```

---

## Future Enhancements

1. **WhatsApp Notifications**
   - Use GreenAPI to send WhatsApp messages
   - Template-based messages with status updates

2. **Customer Preferences**
   - Allow customers to opt-in/opt-out of notifications
   - Store in user `preferences` JSON field

3. **Notification Templates**
   - Use `MessageTemplate` model for email/SMS
   - Admin can customize messages

4. **Scheduled Notifications**
   - Send follow-up emails if not delivered after 5 days
   - Remind customer to track shipment

5. **Webhook Events**
   - Publish events to external systems
   - Trigger third-party integrations

6. **Notification History**
   - Log all notifications sent
   - Track delivery status (bounced, opened, clicked)

7. **Multi-language Support**
   - Translate emails to customer's language
   - Locale-aware date formatting

---

## Files Modified/Created

### Created Files
- ✅ `app/Observers/ShipmentObserver.php` - Main observer logic
- ✅ `app/Mail/ShipmentCreatedNotification.php` - Creation email
- ✅ `app/Mail/ShipmentStatusUpdatedNotification.php` - Status update email
- ✅ `resources/views/emails/shipment-created.blade.php` - Creation email template
- ✅ `resources/views/emails/shipment-status-updated.blade.php` - Status update template

### Modified Files
- ✅ `app/Providers/AppServiceProvider.php` - Register observer
- ✅ `app/Http/Controllers/ShipmentController.php` - Add tracking # generator, update messages
- ✅ `app/Http/Controllers/AdminController.php` - Add tracking # generator, update messages
- ✅ `app/Services/SmsService.php` - Add sendShipmentUpdate() method

### No Database Migrations Needed
- TrackingEvent table already exists with required columns
- Mail notification uses standard Laravel Mailable

---

## Deployment Checklist

Before deploying to production:

- [ ] Ensure Brevo SMTP credentials are in `.env`
- [ ] Ensure Molo SMS credentials are in `.env`
- [ ] Ensure queue driver is configured (Redis recommended)
- [ ] Run `php artisan queue:work` as supervisor service
- [ ] Test shipment creation email
- [ ] Test status update email + SMS
- [ ] Monitor logs for errors
- [ ] Set up email bounce handling
- [ ] Configure sender email (from MAIL_FROM_ADDRESS)

---

## Rollback (if needed)

If something breaks:

1. **Disable observer**: Comment out in `AppServiceProvider.php`
2. **Revert notification files**: Delete notification classes
3. **Clear cache**: `php artisan cache:clear`
4. **Restart queue**: `php artisan queue:restart`

---

**Implementation Complete** ✅  
Ready for testing and deployment!
