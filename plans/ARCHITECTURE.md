# ForusFreight Architecture & Codebase Documentation

**Last Updated**: June 13, 2026  
**Project Owner**: Moses J Banda

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture](#architecture)
3. [Tech Stack](#tech-stack)
4. [Directory Structure](#directory-structure)
5. [Database Schema](#database-schema)
6. [Authentication & Authorization](#authentication--authorization)
7. [Core Features](#core-features)
8. [Route Structure](#route-structure)
9. [Data Flow](#data-flow)
10. [Services & Integrations](#services--integrations)
11. [Frontend Architecture (Livewire)](#frontend-architecture-livewire)
12. [Deployment Pipeline](#deployment-pipeline)

---

## Project Overview

**ForusFreight** is a comprehensive freight and logistics management platform that combines:

- **Main Platform** (Laravel 11 + Livewire) - Core CRM, shipment management, and public-facing features
- **Ecommerce Portal** - Separate services for API, admin dashboard, and storefront
- **Infrastructure** - Terraform configs for cloud deployment

**Key Services**:
- Shipment tracking and management
- CRM (Customer Relationship Management)
- Marketing automation (WhatsApp campaigns, SMS)
- Bulk communications
- Support ticket system
- Knowledge base
- Landing page builder
- CMS for public content

---

## Architecture

### High-Level System Design

```
┌─────────────────────────────────────────────────────────────┐
│                      User Layer                              │
├─────────────────────────────────────────────────────────────┤
│  Public Website │ Client Dashboard │ Admin Panel │ API Users  │
└────────────────────────┬────────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
┌───────▼───────┐ ┌──────▼──────┐ ┌──────▼──────┐
│  Livewire UI  │ │  REST API   │ │ Console Cmds│
│  (Volt/Blades)│ │  (Routes)   │ │ (Scheduled) │
└───────┬───────┘ └──────┬──────┘ └──────┬──────┘
        │                │                │
        └────────────────┼────────────────┘
                         │
        ┌────────────────▼────────────────┐
        │  Controllers & Services Layer    │
        │ (Business Logic & Orchestration)│
        └────────────────┬────────────────┘
                         │
        ┌────────────────▼────────────────┐
        │    Database Models (Eloquent)    │
        │    & Data Access Layer          │
        └────────────────┬────────────────┘
                         │
        ┌────────────────▼────────────────┐
        │   MySQL Database                │
        │   (Core data store)             │
        └────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│           External Integrations                              │
├─────────────────────────────────────────────────────────────┤
│ GreenAPI (WhatsApp) │ Brevo (Email) │ SMS Service           │
│ BOZ Exchange Rates  │ File Storage (Cloud)                  │
└─────────────────────────────────────────────────────────────┘
```

### Layered Architecture

1. **Presentation Layer** - Livewire components, Volt routing, Blade views
2. **API Layer** - RESTful endpoints for client integrations
3. **Application Layer** - Controllers, Services, Business logic
4. **Data Layer** - Eloquent models, Database migrations
5. **Integration Layer** - External APIs (WhatsApp, Email, SMS)

---

## Tech Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| **Framework** | Laravel | 11.x |
| **Server Language** | PHP | 8.1+ |
| **Frontend Framework** | Livewire | 3.6.4+ |
| **Server-Side Routing** | Livewire Volt | 1.7.0+ |
| **Styling** | Tailwind CSS | Latest |
| **Build Tool** | Vite | Latest |
| **Template Engine** | Blade | Built-in |
| **Database** | MySQL | 5.7+ / 8.0+ |
| **ORM** | Eloquent | Laravel 11 |
| **Task Queue** | Laravel Queue | Redis/Database |
| **Authentication** | Laravel Auth + Custom RBAC | Built-in |
| **CI/CD** | GitHub Actions | Workflows |
| **Infrastructure** | Terraform | Hetzner Cloud |

---

## Directory Structure

```
website/
├── app/
│   ├── Console/
│   │   └── Commands/              # Scheduled tasks
│   │       ├── WhatsappProcessCampaigns.php
│   │       ├── WhatsappPollIncoming.php
│   │       ├── ResolveAbWinners.php
│   │       └── CheckCampaignAlerts.php
│   │
│   ├── Http/
│   │   └── Controllers/           # Main request handlers
│   │       ├── AdminController.php            # Admin operations
│   │       ├── CrmContactController.php       # CRM contact management
│   │       ├── CrmSalesController.php         # Sales pipeline
│   │       ├── CrmMarketingController.php     # Marketing campaigns
│   │       ├── CrmSupportController.php       # Tickets & KB
│   │       ├── CrmReportController.php        # Analytics
│   │       ├── BulkCommunicationsController.php # SMS/WhatsApp
│   │       ├── ShipmentController.php         # Shipment CRUD
│   │       ├── TrackingController.php         # Tracking system
│   │       ├── QuoteController.php            # Quote requests
│   │       ├── ProfileController.php          # User profiles
│   │       ├── PublicContentController.php    # Public pages
│   │       ├── CmsAdminController.php         # Content management
│   │       ├── ExchangeRateController.php     # Currency conversion
│   │       ├── UserManagementController.php   # RBAC
│   │       └── PhoneCountryController.php     # Phone whitelist
│   │
│   ├── Models/                   # Eloquent models (31 total)
│   │   ├── User.php              # User auth + CRM fields
│   │   ├── Shipment.php          # Shipment records
│   │   ├── Company.php           # B2B companies
│   │   ├── Deal.php              # Sales deals
│   │   ├── SupportTicket.php     # Support tickets
│   │   ├── Campaign.php          # Marketing campaigns
│   │   ├── WhatsappCampaign.php   # WhatsApp campaigns
│   │   ├── MessageTemplate.php    # Communication templates
│   │   ├── TrackingEvent.php      # Shipment events
│   │   ├── Role.php              # Custom RBAC roles
│   │   ├── CmsPage.php           # CMS pages
│   │   ├── KnowledgeBaseArticle.php
│   │   └── [26 more models...]
│   │
│   ├── Services/                 # Business logic
│   │   ├── BozExchangeRateService.php    # Currency rates
│   │   ├── GreenApiService.php           # WhatsApp integration
│   │   ├── SmsService.php                # SMS sending
│   │   ├── BrevoMailService.php          # Email delivery
│   │   ├── ObservabilityService.php      # Logging/monitoring
│   │   └── [Other domain services]
│   │
│   ├── Mail/                     # Email notifications
│   │   ├── EmailVerificationOtp.php
│   │   └── SecurityAlert.php
│   │
│   ├── Jobs/                     # Queue jobs (for async tasks)
│   │
│   ├── Livewire/                 # Livewire components
│   │   └── [Interactive components]
│   │
│   ├── Providers/                # Service providers
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── RouteServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── VoltServiceProvider.php
│   │
│   └── Exceptions/               # Custom exception handling
│
├── routes/
│   ├── web.php                   # Main application routes (285 lines)
│   ├── auth.php                  # Authentication routes
│   ├── api.php                   # REST API endpoints
│   └── console.php               # Console/CLI routes
│
├── resources/
│   ├── views/                    # Blade templates
│   │   ├── layouts/
│   │   │   ├── app.blade.php     # Main layout (with footer, WhatsApp button)
│   │   │   └── dashboard.blade.php
│   │   ├── admin/
│   │   │   ├── shipments/
│   │   │   ├── clients/
│   │   │   ├── cms/
│   │   │   └── settings/
│   │   ├── livewire/             # Volt components
│   │   │   ├── pages/auth/
│   │   │   └── [other Volt pages]
│   │   └── [public pages]
│   │
│   └── css/
│       └── app.css               # Tailwind styles
│
├── database/
│   ├── migrations/               # Schema changes (20 migrations)
│   ├── seeders/                  # Data seeders
│   └── factories/                # Eloquent factories
│
├── config/                       # Laravel configuration
├── bootstrap/
├── storage/                      # Logs, cache, uploads
├── public/                       # Web-accessible files
├── tests/                        # PHPUnit tests
│
├── composer.json                 # PHP dependencies
├── package.json                  # Node.js dependencies
├── .env                          # Environment variables
├── .env.prod                     # Production env
└── artisan                       # Laravel CLI

Ecommerce/
├── forus-digital-api/            # API service (Node/Fastify?)
├── forus-digital-admin-portal/   # Admin dashboard
└── forus-digital-storefront/     # Customer storefront

TerraForm/                        # Infrastructure as Code
└── [Hetzner Cloud configs]
```

---

## Database Schema

### Core Tables

#### **users**
Stores all user accounts (customers, admins, staff)
```
- id (PK)
- name, email, phone, password
- is_admin (boolean)
- crm_status (lead, prospect, customer, inactive)
- assigned_agent (FK: users.id)
- lead_score (0-100)
- last_engagement_at (datetime)
- company_id (FK: companies.id)
- company_name, address
- phone_verified_at, email_verified_at
- preferences (JSON)
```

#### **shipments**
Tracks all freight shipments
```
- id (PK)
- user_id (FK: users.id)
- tracking_number (unique)
- status (pending, in_transit, delivered, etc.)
- from (origin), to (destination)
- service (service type)
- weight, dimensions, description
- estimated_delivery, shipment_date (datetime)
- cost, border_status
- history, images (JSON arrays)
- service_type
```

#### **tracking_events**
Historical tracking updates for each shipment
```
- id (PK)
- shipment_id (FK: shipments.id)
- event_type, status
- description
- location, coordinates
- timestamp
```

#### **companies**
B2B company records (linked to users)
```
- id (PK)
- name, email, phone
- industry, size
- address, city, country
- created_at, updated_at
```

#### **deals**
Sales pipeline deals
```
- id (PK)
- user_id (FK: users.id)
- stage_id (FK: deal_stages.id)
- value (decimal)
- expected_close_date
- notes
```

#### **deal_stages**
Customizable sales pipeline stages
```
- id (PK)
- name (Prospecting, Negotiation, Won, Lost, etc.)
- order
```

#### **support_tickets**
Customer support requests
```
- id (PK)
- user_id (FK: users.id)
- subject, description
- status (open, in_progress, resolved, closed)
- priority (low, medium, high)
- assigned_to (FK: users.id)
```

#### **whatsapp_campaigns**
WhatsApp bulk messaging campaigns
```
- id (PK)
- name, description
- message_template (FK: message_templates.id)
- status (draft, scheduled, running, completed, paused)
- scheduled_at, started_at, completed_at
- total_recipients, delivered, failed
- ab_test_variant (A or B)
- auto_winner (true if auto-winner detected)
- alert_threshold (%)
```

#### **whatsapp_campaign_recipients**
Recipient tracking for WhatsApp campaigns
```
- id (PK)
- campaign_id (FK: whatsapp_campaigns.id)
- phone_number
- status (pending, sent, delivered, failed)
- timestamp
```

#### **message_templates**
Pre-defined message templates
```
- id (PK)
- name, content
- channel (sms, whatsapp, both)
- variables (JSON - dynamic placeholders)
```

#### **communications_logs**
Complete log of all communications sent
```
- id (PK)
- user_id (FK: users.id)
- channel (sms, whatsapp, email)
- recipient, message, status
- timestamp
```

#### **cms_pages**
CMS-managed public pages
```
- id (PK)
- slug (unique, for URL)
- title, meta_description, meta_keywords
- sections (JSON - flexible content)
- status (published, draft)
- last_edited_by (FK: users.id)
```

#### **roles**
Custom RBAC roles
```
- id (PK)
- name (admin, sales, support, etc.)
- permissions (JSON array of permission strings)
```

#### **Other Important Tables**
- `tasks` - CRM tasks
- `documents` - Sales documents
- `landing_pages` - Marketing landing pages
- `campaigns` - General marketing campaigns
- `contact_notes` - Notes on contacts
- `analytics_snapshots` - Periodic analytics data
- `exchange_rates` - Currency conversion rates
- `currency_hedges` - Currency hedging records
- `knowledge_base_articles` - Support KB
- `phone_countries` - Allowed phone number countries

---

## Authentication & Authorization

### Authentication Flow - Client Application

#### 1. User Registration Flow
**File**: `resources/views/livewire/pages/auth/register.blade.php`

```
User → Registration Form (name, email, phone, password)
  ↓
Validate:
  - Name: required, max 255
  - Email: unique, valid format
  - Phone: matches active country dial codes via regex
  - Password: Laravel defaults (8+ chars, uppercase, number, symbol)
  - Terms: must be accepted
  ↓
User::create() + Hash::make(password)
  ↓
Generate OTPs:
  - Email OTP (6 digits) → BrevoMailService::sendOtpEmail()
  - Phone OTP (6 digits) → SmsService::sendOtp()
  ↓
Auth::login($user) [auto-login]
  ↓
Redirect to /verify-email (verification.notice)
```

#### 2. Email Verification Flow
**File**: `resources/views/livewire/pages/auth/verify-email.blade.php`

```
User at /verify-email
  ↓
Enter 6-digit code from email
  ↓
User::verifyEmailOtp($code):
  - Check if OTP matches and not expired
  - Mark email_verified_at = now()
  ↓
If Phone NOT verified → Redirect to /verify-phone
If Phone IS verified → Redirect to /dashboard
  ↓
Resend OTP: generateEmailOtp() → BrevoMailService::sendOtpEmail()
```

#### 3. Phone Verification Flow
**File**: `resources/views/livewire/pages/auth/verify-phone.blade.php`

```
User at /verify-phone
  ↓
Enter 6-digit code from SMS
  ↓
User::verifyPhoneOtp($code):
  - Check if OTP matches and not expired
  - Mark phone_verified_at = now()
  ↓
Success → Redirect to /dashboard
  ↓
Resend OTP: generatePhoneOtp() → SmsService::sendOtp()
```

#### 4. Login Flow
**File**: `resources/views/livewire/pages/auth/login.blade.php`

```
User → Email + Password
  ↓
LoginForm::authenticate():
  - Laravel's Auth::attempt() with email/password
  ↓
Session::regenerate() [security]
  ↓
Check email_verified_at:
  - If NOT verified → generateEmailOtp() → Redirect to /verify-email
  - If verified → Continue
  ↓
Check phone_verified_at:
  - If NOT verified → generatePhoneOtp() → Redirect to /verify-phone
  - If verified → Continue
  ↓
redirectIntended(default: /dashboard)
```

**Note**: Unlike registration, login does NOT auto-generate OTPs. It only sends OTPs if verification was previously incomplete.

#### 5. Session Management
- **Type**: Standard Laravel session (cookie-based)
- **Duration**: Configured in `config/session.php`
- **Security**: CSRF tokens on all forms (Livewire auto-generates)
- **Regeneration**: After login, on password change

### Authorization (RBAC)

Custom role-based access control with fine-grained permissions:

**Roles**:
- `admin` / `super-admin` - Full system access
- `admin_staff` - Staff access (shipments, clients, reports)
- `sales` - Sales team (CRM pipeline, deals, leads)
- `support` - Support team (tickets, KB)
- `marketing` - Marketing (campaigns, communications)
- `user` / `customer` - Basic client access

**Permission Checks**:
```php
// Middleware format
Route::middleware('permission:admin.shipments.manage')->group(...)
```

**Available Permissions**:
- `admin.shipments.manage` / `.view`
- `admin.clients.manage` / `.view`
- `admin.reports.view`
- `admin.exchange_rates.manage`
- `admin.users.manage`
- `crm.companies.manage` / `.view`
- `crm.contacts.manage` / `.view`
- `crm.pipeline.view`
- `crm.deals.manage`
- `crm.tasks.manage`
- `crm.documents.manage`
- `crm.forecast.view`
- `crm.leads.manage`
- `crm.stages.manage`
- `crm.campaigns.manage`
- `crm.landing_pages.manage`
- `crm.communications.manage`
- `crm.tickets.manage`
- `crm.knowledge_base.manage`
- `crm.reports.view`
- `admin.cms.manage`

---

## Core Features

### 1. Shipment Management
**Purpose**: Track freight from origin to destination

**Key Entities**:
- Shipment (main record)
- TrackingEvent (status updates)
- User (owner)

**Features**:
- Create shipments (admin or client)
- Track shipments in real-time
- Update shipment status with events
- Attach images to shipments
- View shipment history
- Export tracking data

**Routes**:
- `GET /client/shipments` - List client's shipments
- `POST /client/shipments` - Create shipment
- `GET /admin/shipments` - Admin view all
- `GET /tracking/{tracking_number}` - Public tracking
- `POST /track/check` - Check tracking status

### 2. CRM System
**Purpose**: Manage customer relationships, sales pipeline, and communication

**Sub-modules**:

#### 2a. Contact Management
- Store companies and contacts
- Link contacts to companies
- Add notes and attachments
- View 360° contact profile

**Controllers**: `CrmContactController`
**Models**: `Company`, `ContactNote`, `Document`

#### 2b. Sales Pipeline
- Kanban-style deal management
- Customizable deal stages
- Deal value tracking
- Expected close dates
- Forecast reporting

**Controllers**: `CrmSalesController`
**Models**: `Deal`, `DealStage`, `Task`

#### 2c. Marketing Automation
- Email/SMS/WhatsApp campaigns
- Landing page builder
- A/B testing for campaigns
- Recipient list management
- Campaign scheduling

**Controllers**: `CrmMarketingController`, `BulkCommunicationsController`
**Models**: `Campaign`, `WhatsappCampaign`, `LandingPage`, `MessageTemplate`

#### 2d. Support System
- Ticket creation and tracking
- Support team assignment
- Ticket replies/comments
- Knowledge base articles
- Status management (open → closed)

**Controllers**: `CrmSupportController`
**Models**: `SupportTicket`, `TicketReply`, `KnowledgeBaseArticle`

#### 2e. Reporting & Analytics
- Sales dashboard
- Revenue forecasts
- Campaign performance
- Communication logs
- Lead scoring

**Controllers**: `CrmReportController`
**Models**: `AnalyticsSnapshot`, `CommunicationsLog`

### 3. Bulk Communications
**Purpose**: Send SMS and WhatsApp messages at scale

**Features**:
- CSV upload of recipients
- Message template system
- Scheduled sending
- Delivery tracking
- Campaign A/B testing
- Auto-winner detection
- Performance alerts

**Services**:
- `GreenApiService` - WhatsApp integration
- `SmsService` - SMS delivery
- `BrevoMailService` - Email delivery

**Console Commands**:
- `whatsapp:process-campaigns` - Send pending messages
- `whatsapp:poll-incoming` - Receive incoming messages
- `resolve:ab-winners` - Determine campaign winners
- `check:campaign-alerts` - Monitor thresholds

### 4. Exchange Rate Management
**Purpose**: Currency conversion for international logistics

**Features**:
- Sync rates from BOZ (Bank of Zambia)
- Manual rate overrides
- Currency hedging
- Rate history tracking

**Services**: `BozExchangeRateService`
**Models**: `ExchangeRate`, `CurrencyHedge`

### 5. Content Management (CMS)
**Purpose**: Manage public website content without coding

**Features**:
- Create/edit dynamic pages
- Set meta tags for SEO
- Flexible section-based content (JSON)
- Image upload
- Publish/draft status
- Footer management (includes WhatsApp number: `260572788685`)

**Controllers**: `CmsAdminController`
**Models**: `CmsPage`

### 6. Public Content
**Purpose**: Display public-facing pages and landing pages

**Features**:
- Knowledge base (public)
- Landing pages (with form submissions)
- Quote request forms
- Tracking page
- Services and about pages

**Controllers**: `PublicContentController`

---

## Route Structure

### Public Routes (No Authentication)
```
GET  /                           # Home
GET  /about                      # About us
GET  /services                   # Services
GET  /quote                      # Quote form
POST /quote/submit               # Submit quote
GET  /contact                    # Contact page
GET  /terms                      # Terms of service
GET  /track                      # Tracking page
POST /track/check                # Check tracking
GET  /kb                         # Knowledge base
GET  /kb/{slug}                  # KB article
GET  /lp/{slug}                  # Landing page
POST /lp/{slug}/submit           # Landing form
GET  /api/exchange-rate/current  # Public API
```

### Protected Routes (Authenticated + Fully Verified)
```
GET  /dashboard                  # User dashboard
GET  /client/shipments           # Client's shipments
POST /client/shipments           # Create shipment
GET  /client/invoices            # Client invoices
GET  /client/settings            # Client settings
GET  /client/profile             # Profile edit
```

### Admin Routes (Auth + Verified + Admin/Staff Role)
```
GET  /admin/dashboard            # Admin dashboard

# Shipments
GET  /admin/shipments            # View all
GET  /admin/shipments/create     # Create form
POST /admin/shipments            # Store
GET  /admin/shipments/{id}/edit  # Edit form
PUT  /admin/shipments/{id}       # Update

# Clients
GET  /admin/clients              # View all
GET  /admin/clients/create       # Create form
POST /admin/clients              # Store
GET  /admin/clients/{id}         # Show
GET  /admin/clients/{id}/edit    # Edit form

# Reports
GET  /admin/reports              # System reports
GET  /admin/reports/export       # Export

# Exchange Rates
GET  /admin/exchange-rates       # Manage rates
POST /admin/exchange-rates/sync  # Sync from API
POST /admin/exchange-rates/hedge # Create hedge

# Staff Management
GET  /admin/staff                # Staff list
POST /admin/staff/{id}/roles     # Assign roles
DELETE /admin/staff/{id}/roles/{role} # Remove role

# Phone Settings
GET  /admin/settings/phone-countries  # Whitelist
POST /admin/settings/phone-countries  # Add country
```

### CRM Routes (Admin + CRM Permissions)
```
# Companies
GET  /admin/crm/companies        # List
GET  /admin/crm/companies/create # Create
POST /admin/crm/companies        # Store
GET  /admin/crm/companies/{id}   # Show
GET  /admin/crm/companies/{id}/edit # Edit
PUT  /admin/crm/companies/{id}   # Update

# Pipeline
GET  /admin/crm/pipeline         # Kanban view
GET  /admin/crm/stages           # Stage list
POST /admin/crm/stages           # Create stage

# Deals
GET  /admin/crm/deals/create     # Create form
POST /admin/crm/deals            # Store
GET  /admin/crm/deals/{id}       # Show
PUT  /admin/crm/deals/{id}/stage # Update stage

# Campaigns
GET  /admin/crm/campaigns        # List
POST /admin/crm/campaigns        # Create
PUT  /admin/crm/campaigns/{id}   # Update

# Communications
GET  /admin/crm/communications/sms
GET  /admin/crm/communications/whatsapp
POST /admin/crm/communications/sms/send
POST /admin/crm/communications/whatsapp/send
```

### CMS Routes
```
GET  /admin/cms/pages            # List pages
GET  /admin/cms/pages/create     # Create form
POST /admin/cms/pages            # Store
GET  /admin/cms/pages/{id}/edit  # Edit form
PUT  /admin/cms/pages/{id}       # Update
DELETE /admin/cms/pages/{id}     # Delete
POST /admin/cms/upload           # Image upload
```

---

## Data Flow

### Flow 1: Customer Creates Shipment (Client)

**File**: `app/Http/Controllers/ShipmentController.php::store()`

```
1. Authenticated user at /client/shipments/create
2. Form submitted with:
   - origin (required)
   - destination (required)
   - weight (required, numeric, min 0.1)
   - dimensions (optional)
   - description (optional)
   - service_type (required)

3. Validation passes

4. Shipment::create():
   - user_id: Auth::id()
   - tracking_number: Generate "FORUS-" + origin prefix + random(1000-9999)
     Example: "FORUS-DAR-4521"
   - origin, destination, weight, dimensions, description, service_type
   - status: "Order Placed" (hardcoded)
   - created_at: now()

5. DATABASE STATE:
   - New record in shipments table
   - user_id linked to authenticated customer

6. NOTIFICATIONS:
   ⚠️ CURRENT: NO AUTOMATIC NOTIFICATIONS SENT
   ⚠️ Manual Option: Admin can send email/SMS via:
      - /admin/clients/{user} → Send Message button
      - Bulk communications (SMS/WhatsApp)

7. Response:
   - Redirect to /client/shipments
   - Flash: 'Shipment created successfully! Tracking number: FORUS-...'
   - User sees new shipment in dashboard
```

**What's NOT Implemented Yet**:
- ❌ Automatic email to customer with tracking number
- ❌ Automatic SMS to customer
- ❌ Notification on shipment creation event
- ❌ Real-time WebSocket updates
- ❌ Customer SMS alerts on status changes

**To Add Notifications**:
Create a listener for Shipment model events:
```php
// In EventServiceProvider.php
protected $listen = [
    'eloquent.created: App\Models\Shipment' => [
        'App\Listeners\SendShipmentCreatedNotification',
    ],
];

// Create: app/Listeners/SendShipmentCreatedNotification.php
// Send email/SMS via BrevoMailService/SmsService
```

### Flow 1b: Admin Creates Shipment (for Customer)

**File**: `app/Http/Controllers/AdminController.php::storeShipment()`

```
1. Admin at /admin/shipments/create

2. Form submitted with:
   - user_id (required, must exist - dropdown of clients)
   - origin (required)
   - destination (required)
   - status (required, dropdown: Order Placed, Pending, In Transit, At Border, 
             Cleared, Out for Delivery, Delivered, Cancelled)
   - estimated_delivery (optional, date)
   - cost (optional, numeric)
   - weight (optional, numeric)
   - dimensions (optional, string)
   - description (optional, string)
   - images (optional, array of files)

3. Validation passes

4. Shipment::create() with:
   - user_id: from form
   - tracking_number: "FORUS-" + origin prefix + random(1000-9999)
   - All validated fields

5. Image Upload (if present):
   - Store each image: shipments/{shipment_id}/filename
   - Save paths to images JSON array

6. DATABASE STATE:
   - New shipment record
   - Images stored in storage/app/public/shipments/{shipment_id}/

7. NOTIFICATIONS:
   ⚠️ CURRENT: NO AUTOMATIC NOTIFICATIONS SENT
   ⚠️ Admin must manually notify customer via:
      - /admin/clients/{user} → Send Message
      - Bulk SMS/WhatsApp campaigns
      - CMS communications

8. Response:
   - Redirect to /admin/shipments
   - Flash: 'Shipment created successfully!'
   - Admin sees new shipment in list
```

**Difference from Client Creation**:
- Admin can set initial status (not just "Order Placed")
- Admin can set cost, estimated delivery
- Admin can upload images immediately
- Admin explicitly chooses the customer
- Customer is NOT auto-notified

---

### Flow 2: Admin Sends WhatsApp Campaign

```
1. Admin navigates to Communications → WhatsApp
2. Upload CSV of phone numbers
3. Select message template
4. Configure scheduling/A/B test
5. Submit → BulkCommunicationsController::sendWhatsapp()
6. WhatsappCampaign record created (status: scheduled)
7. Console command: whatsapp:process-campaigns
   - Picks up scheduled campaigns
   - Calls GreenApiService for each recipient
   - Updates WhatsappCampaignRecipient status
8. Incoming messages polled by whatsapp:poll-incoming
9. Campaign analytics updated

Schedule:
- `whatsapp:process-campaigns` → every minute (queue listener)
- `whatsapp:poll-incoming` → every 5 minutes
- `resolve:ab-winners` → every 30 minutes
- `check:campaign-alerts` → every 15 minutes
```

### Flow 2b: Admin Updates Shipment Status

**File**: `app/Http/Controllers/AdminController.php::updateShipment()`

```
1. Admin at /admin/shipments/{shipment}/edit

2. Form submitted with:
   - status (required, new status)
   - origin, destination, current_border (optional)
   - estimated_delivery (optional, date)
   - cost (optional, numeric)
   - images (optional, additional image uploads)

3. Validation passes

4. Shipment::update():
   - All validated fields updated
   - Existing images preserved, new ones appended

5. DATABASE STATE:
   - Shipment status changed
   - History NOT automatically tracked (no TrackingEvent created)
   - Images appended to images JSON

6. NOTIFICATIONS:
   ⚠️ CURRENT: NO AUTOMATIC NOTIFICATIONS SENT TO CUSTOMER
   ⚠️ Manual Options:
      - Send SMS via bulk communications
      - Send WhatsApp via bulk communications
      - Create TrackingEvent manually and send custom notification

7. Response:
   - Redirect back with Flash: 'Shipment updated successfully!'
   - Admin sees updated shipment details

IMPORTANT: 
- Changing status does NOT create TrackingEvent automatically
- No customer notification is triggered
- History is not maintained except in images/description
```

**To Track Status Changes Automatically**:
Create an observer:
```php
// Create: app/Observers/ShipmentObserver.php
// Listen to shipment updates and create TrackingEvent
// Send customer notification via email/SMS
```

---

### Flow 3: Public User Tracks Shipment

```
1. User visits /track
2. Form: enter tracking number
3. POST to /track/check → TrackingController::check()
4. Query Shipment + TrackingEvents
5. Return tracking page with status timeline
```

### Flow 4: User Registration + Two-Factor Verification

**Location**: `resources/views/livewire/pages/auth/register.blade.php` & `auth/verify-*.blade.php`

```
Step 1: Registration (register.blade.php)
1. User fills out form: name, email, phone, password
2. Phone number validated against PhoneCountry whitelist (active countries only)
3. User must accept Terms & Conditions
4. POST /register → Livewire component validates & creates user

Step 2: OTP Generation & Sending
1. Email OTP generated (6-digit code)
2. Email OTP sent via BrevoMailService (sendOtpEmail)
3. Phone OTP generated (6-digit code)  
4. Phone OTP sent via SmsService (sendOtp)
5. User auto-logged in (Auth::login())

Step 3: Email Verification
1. Redirected to /verify-email
2. User enters 6-digit email OTP
3. User calls verify() method
4. User::verifyEmailOtp() validates OTP
5. If email_verified_at not set, OTP is marked as used
6. Success → Redirect to /verify-phone

Step 4: Phone Verification
1. Redirected to /verify-phone
2. User enters 6-digit SMS OTP
3. User calls verify() method
4. User::verifyPhoneOtp() validates OTP
5. If phone_verified_at not set, OTP is marked as used
6. Success → Redirect to /dashboard

Resend Functionality:
- Email: generateEmailOtp() creates new OTP, BrevoMailService sends it
- Phone: generatePhoneOtp() creates new OTP, SmsService sends it
- Both preserve user session for resend retries
```

**Key Validations**:
- Email: Must be unique in users table
- Password: Laravel Rules\Password::defaults() (min 8 chars, uppercase, number, symbol)
- Phone: Must match regex pattern of active phone countries' dial codes
- Terms: Must be accepted

**Database Fields Updated**:
- `email_verified_at` - Timestamp when email OTP verified
- `phone_verified_at` - Timestamp when phone OTP verified
- `phone_otp` - Current phone OTP (hashed)
- `phone_otp_expires_at` - When phone OTP expires
- `email_otp` - Current email OTP (hashed)
- `email_otp_expires_at` - When email OTP expires

### Flow 5: CRM Deal Management

```
1. Sales rep views pipeline (/admin/crm/pipeline)
2. Kanban board grouped by DealStage
3. User drags deal card to new stage
4. PUT /admin/crm/deals/{deal}/stage
5. CrmSalesController updates deal stage
6. Deal status changed in DB
7. Dashboard reflects new position
```

---

## Services & Integrations

### External Integrations

#### 1. WhatsApp (GreenAPI)
**Service**: `GreenApiService`
**Purpose**: Send/receive WhatsApp messages at scale
**Integration Points**:
- `BulkCommunicationsController::sendWhatsapp()`
- Console command: `whatsapp:process-campaigns`
- Console command: `whatsapp:poll-incoming`

**Configuration**: `.env` variables
```
GREENAPI_INSTANCE_ID=
GREENAPI_ACCESS_TOKEN=
GREENAPI_WEBHOOK_URL=
```

#### 2. Email (Brevo/Sendinblue)
**Service**: `BrevoMailService`
**Purpose**: Transactional and marketing emails
**Usage**:
- Account verification
- Password resets
- Order confirmations
- Shipment updates
- Campaign newsletters

#### 3. SMS (Generic SMS Service)
**Service**: `SmsService`
**Purpose**: OTP and transactional SMS
**Usage**:
- Phone verification OTP
- Shipment status updates
- Campaign messages

#### 4. Exchange Rates (BOZ)
**Service**: `BozExchangeRateService`
**Purpose**: Currency conversion (ZMW ↔ USD, etc.)
**Console Command**: `php artisan schedule:run`
- Periodic sync from Bank of Zambia API

#### 5. File Storage
**Purpose**: Store shipment images, documents, campaign attachments
**Storage Options**:
- Local (`public/` directory)
- Cloud (S3, etc. via `.env` configuration)

### Observability
**Service**: `ObservabilityService`
**Purpose**: Logging, monitoring, error tracking
**Integration**: Custom logging in services

---

## Frontend Architecture (Livewire)

### Technology Stack
- **Livewire 3** - Real-time interactive components
- **Livewire Volt** - File-based component routing
- **Blade Templates** - Server-side template engine
- **Tailwind CSS** - Utility-first styling
- **Alpine.js** - Lightweight interactivity
- **Vite** - Build tooling

### Layout Structure

#### Main Layout (`resources/views/layouts/app.blade.php`)
```html
<html>
  <head>
    - Meta tags
    - Tailwind CSS (from Vite)
    - Font Awesome (WhatsApp icon)
  </head>
  <body>
    - Header/Navigation
    - Main content (@yield or Livewire component)
    - Footer
    - WhatsApp float button (wa.me/260572788685)
    - Scripts
  </body>
</html>
```

#### Dashboard Layout (`resources/views/layouts/dashboard.blade.php`)
- Sidebar navigation
- Top bar
- User menu
- Main content area

### Key Templates

| Path | Purpose |
|------|---------|
| `views/welcome.blade.php` | Home page |
| `views/about.blade.php` | About page |
| `views/quote.blade.php` | Quote form |
| `views/contact.blade.php` | Contact form |
| `views/admin/shipments/` | Shipment CRUD views |
| `views/admin/clients/` | Client management |
| `views/admin/cms/pages/sections/` | CMS page sections |
| `views/admin/cms/pages/sections/footer.blade.php` | Footer editor (includes WhatsApp number input) |
| `views/livewire/pages/auth/` | Volt auth pages |

### Livewire Components (Volt-style)

Located in `resources/views/livewire/` with PHP-in-Blade syntax:

```php
<?php
use Livewire\Volt\Component;
use App\Models\Shipment;

new class extends Component {
    public $shipments;
    
    #[Livewire\Attributes\On('refresh')]
    public function refresh() {
        $this->shipments = Shipment::all();
    }
};
?>

<div>
    <h1>Shipments</h1>
    @foreach ($shipments as $shipment)
        <div wire:key="shipment-{{ $shipment->id }}">
            ...
        </div>
    @endforeach
</div>
```

---

## Deployment Pipeline

### Environment
- **Local Dev**: `php artisan serve` + `npm run dev`
- **Production**: cPanel shared hosting (via SSH/rsync)
- **Ecommerce Services**: Docker Compose on VM (46.62.161.138)

### GitHub Actions Deployment (`/.github/workflows/deploy.yml`)

**Trigger**: Push to `main` branch

**Steps**:
1. Checkout code
2. Setup PHP environment
3. Cache dependencies
4. Run tests (optional)
5. Build assets (Vite)
6. SSH into cPanel server
7. Pull latest code (git pull)
8. Run migrations (`php artisan migrate`)
9. Clear caches (`php artisan cache:clear`)
10. Publish assets
11. Restart queues

**Required GitHub Secrets**:
- `CPANEL_HOST` - Server hostname
- `CPANEL_USERNAME` - SSH user
- `CPANEL_SSH_KEY` - Private key (Ed25519/RSA)
- `CPANEL_SSH_PASSPHRASE` - Key password (if encrypted)
- `CPANEL_PORT` - SSH port (default 22)
- `CPANEL_DEPLOY_PATH` - Path on server (e.g., `/home/user/public_html`)
- `VM_SSH_KEY` - For Ecommerce services VM deploy

### Ecommerce Services Deployment
- **Target**: `root@46.62.161.138:/opt/forus-digital`
- **Method**: Docker Compose
- **Services**:
  - `forus-digital-api` (backend)
  - `forus-digital-admin-portal` (dashboard)
  - `forus-digital-storefront` (frontend)

---

## Console Commands & Scheduling

### Available Commands

| Command | Frequency | Purpose |
|---------|-----------|---------|
| `whatsapp:process-campaigns` | Every 1 min | Send pending WhatsApp messages |
| `whatsapp:poll-incoming` | Every 5 min | Receive incoming WhatsApp |
| `resolve:ab-winners` | Every 30 min | Determine A/B test winners |
| `check:campaign-alerts` | Every 15 min | Monitor alert thresholds |
| `migrate` | Manual | Run database migrations |
| `cache:clear` | After deploy | Clear app cache |
| `queue:listen` | Dev mode | Process queued jobs |

### Scheduling
**File**: `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('whatsapp:process-campaigns')->everyMinute();
    $schedule->command('whatsapp:poll-incoming')->everyFiveMinutes();
    $schedule->command('resolve:ab-winners')->everyThirtyMinutes();
    $schedule->command('check:campaign-alerts')->everyFifteenMinutes();
}
```

---

## Key Dependencies & Packages

### Production Dependencies
- `laravel/framework: ^10.48`
- `livewire/livewire: ^3.6.4`
- `livewire/volt: ^1.7.0`
- `guzzlehttp/guzzle: ^7.10` - HTTP client (for external APIs)
- `doctrine/dbal: ^3.10` - Database introspection

### Development Dependencies
- `laravel/breeze: ^1.29` - Auth scaffolding
- `laravel/pint: ^1.13` - Code style fixer
- `laravel/sail: ^1.26` - Docker environment
- `phpunit/phpunit: ^10.5` - Testing

---

## Important Contact Information

**Official WhatsApp Number**: `260572788685`  
*This number is maintained in:*
- `app/Http/Controllers/CmsAdminController.php` (line 208)
- `resources/views/layouts/app.blade.php` (line 858) - fallback
- `resources/views/admin/cms/pages/sections/footer.blade.php` - editable

---

## Development Workflow

### Local Setup
```bash
cd website
cp .env.example .env
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate
php artisan serve
```

### Development Commands
```bash
# Compile CSS/JS in watch mode
npm run dev

# Full dev environment (server + queue + vite)
composer run dev

# Run tests
composer run test

# Create new migration
php artisan make:migration create_table_name

# Create new model
php artisan make:model ModelName -m

# Run migrations
php artisan migrate

# Rollback
php artisan migrate:rollback

# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Git Workflow
1. Create feature branch: `git checkout -b feature/description`
2. Make changes and commit: `git commit -m "message"`
3. Push to origin: `git push origin feature/description`
4. Create Pull Request
5. After merge to `main`, GitHub Actions auto-deploys

---

## Security Considerations

1. **Authentication**: Two-factor verification (email + phone OTP)
2. **Authorization**: Fine-grained permission system via RBAC
3. **Input Validation**: Server-side validation on all inputs
4. **Password Hashing**: Laravel's bcrypt (default)
5. **CSRF Protection**: Token in forms (built-in middleware)
6. **SQL Injection Prevention**: Eloquent ORM parameterized queries
7. **XSS Prevention**: Blade auto-escaping `{{ }}`
8. **Rate Limiting**: Optional middleware for API endpoints
9. **Environment Variables**: Sensitive config in `.env` (not in git)
10. **HTTPS**: Required in production (cPanel SSL)

---

## Monitoring & Debugging

### Logging
- **Location**: `storage/logs/`
- **Channel**: `single` or `daily` (configurable)
- **Level**: Set in `.env` with `APP_LOG_LEVEL`

### Error Handling
- **Local**: Detailed error page with stack trace
- **Production**: Generic error page, details in log

### Queue Monitoring
```bash
# Watch queue in real-time
php artisan queue:listen --tries=1

# Monitor specific job
php artisan queue:monitor
```

---

## Future Improvements / Roadmap

1. **API Documentation** - Swagger/OpenAPI spec
2. **Testing** - Comprehensive test suite
3. **Performance** - Caching strategy, database indexing
4. **Analytics** - Dashboard analytics
5. **Mobile App** - Native or React Native app
6. **Webhooks** - External system integration via webhooks
7. **Advanced Reporting** - BI integration

---

## References

- **Laravel Docs**: https://laravel.com/docs
- **Livewire Docs**: https://livewire.laravel.com
- **Tailwind Docs**: https://tailwindcss.com/docs
- **Eloquent ORM**: https://laravel.com/docs/eloquent

---

**Document Version**: 1.0  
**Last Updated**: June 13, 2026  
**Author**: Claude Code (AI Assistant)  
**Project Owner**: Moses J Banda
