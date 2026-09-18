# CRM Contacts Import Guide

## Overview

220 customer contacts have been successfully imported into the Forus Freight CRM system on June 23, 2026.

## Import Summary

| Metric | Count |
|--------|-------|
| **Total Imported** | 220 |
| **Skipped** | 2 |
| **Errors** | 0 |
| **Success Rate** | 99.1% |

## What Was Imported

- **Customer Names**: Full names from various sectors (Auto, Retail, Manufacturing, etc.)
- **Phone Numbers**: Formatted to international format (+260XXXXXXXXX)
- **Business Names**: Company/business information when available
- **Shipment Status**: Initial status (Open, Lead, Closed, Prospect, etc.)
- **Comments/Notes**: Follow-up notes and observations from sales team
- **Dates**: Initial contact dates (May-June 2026)

## CRM Fields Populated

Each contact record includes:

- `name` - Customer name
- `email` - Generated from name and phone (local email for database)
- `phone` - Formatted phone number (+260XXX...)
- `company_name` - Business/company name
- `crm_status` - Sales status (lead, closed, prospect)
- `internal_notes` - Formatted notes with all details
- `email_verified_at` - Set to now (contacts trusted source)
- `phone_verified_at` - Set to now (contacts provided phone)
- `password` - Temporary password (user can reset)

## How to Use the Data

### View All Contacts
```bash
# In admin dashboard, navigate to CRM > Contact Management > Contacts
```

### Filter by Status
```
# Open Sales: Prospects interested in our services
# Sales Lead: Active prospects working towards orders
# Sales Closed: Completed transactions
# Prospect: Initial leads
```

### Update Contact Information
```bash
# Click on contact → Edit
# Update status, add notes, assign to agent
# Changes auto-saved
```

### Follow-Up Management
```bash
# Use "Follow up Date" field to track next action
# Comments section for interaction history
```

## Importing Additional Contacts

To import more contacts from a CSV file:

```bash
php artisan crm:import-contacts /path/to/your/file.csv
```

### CSV Format Required

```csv
Date,Customer Name,Contact Detail,Business Name,Shipment Status,Follow up Date,Comments
5/1/26,BRIAN MOYO,977777763,,SHIPPING,,
```

**Required Columns:**
- Date
- Customer Name
- Contact Detail (phone number)
- Business Name (optional)
- Shipment Status (optional)
- Follow up Date (optional)
- Comments (optional)

## Phone Number Formats Supported

The import command automatically converts phone numbers:

| Format | Input | Converts To |
|--------|-------|------------|
| 9 digits | 961234567 | +260961234567 |
| 10 digits | 0961234567 | +260961234567 |
| 11 digits | 26061234567 | +260961234567 |
| Already formatted | +260961234567 | +260961234567 |

## CRM Status Mapping

Status values are automatically normalized:

| Input | Maps To |
|-------|---------|
| "sales lead" | lead |
| "open sales" | lead |
| "sales closed" | closed |
| "prospect" | prospect |
| "travelling" | lead |
| "shipping" | lead |

## Accessing the CRM

### For Admins/Sales Team
1. Login to admin dashboard
2. Navigate to **CRM Hub** → **Contact Management**
3. View all imported contacts
4. Click on contact to view/edit details

### Features Available
- ✅ View all contacts with filters
- ✅ Edit contact information
- ✅ Add internal notes
- ✅ Track follow-up dates
- ✅ Assign to sales agents
- ✅ View shipment history
- ✅ Export contact list

## Notes on Data Quality

- **2 records skipped** due to missing/invalid phone numbers
- **Phone numbers** automatically formatted to E.164 standard
- **Email addresses** generated locally (users can update after login)
- **Status** inferred from "Shipment Status" field
- **Notes** compiled from all available fields for easy reference

## Next Steps

1. **Review Contacts**: Browse the CRM to review imported data
2. **Update Statuses**: Refine sales statuses based on current interactions
3. **Assign Agents**: Assign contacts to specific sales team members
4. **Schedule Follow-ups**: Set follow-up dates for each contact
5. **Add Interactions**: Log phone calls, meetings, emails in notes

## Database Location

All contacts stored in: `users` table with `crm_status` field

Query to view all CRM contacts:
```sql
SELECT name, email, phone, company_name, crm_status, internal_notes 
FROM users 
WHERE crm_status IN ('lead', 'closed', 'prospect');
```

## Support

For issues with CRM data:
- Check contact phone number format
- Review internal notes for complete history
- Verify assigned agent is active
- Ensure status accurately reflects current state

---

## Import Command Reference

```bash
# Import from default location
php artisan crm:import-contacts

# Import from custom file
php artisan crm:import-contacts /path/to/contacts.csv

# Show help
php artisan help crm:import-contacts
```

---

**Last Import:** June 23, 2026  
**Contacts Added:** 220  
**Import Method:** CSV → CRM Console Command
