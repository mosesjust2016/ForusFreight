# Forus Freight CRM — Step-by-Step Tutorial

## 1. Getting Into the CRM

1. Open your browser and go to: `http://127.0.0.1:8000/admin/login`
2. Log in with:
   - **Email:** `admin@test.local`
   - **Password:** `password123`
3. You will land on the **Admin Dashboard**.
4. Look for the **CRM Hub** section in the left sidebar.

---

## 2. CRM Dashboard (Bird's-Eye View)

**Path:** `Admin Sidebar → Reporting & AI → CRM Dashboard`

This is your command center. It shows:
- **Total Contacts & Companies** — how many people and businesses are in the system.
- **Open Pipeline & Won Revenue** — money on the table vs. money already closed.
- **Pending / Overdue Tasks** — things you need to do.
- **Open Tickets** — unresolved customer support issues.
- **AI Insights** — automatic alerts like:
  - Overdue tasks
  - Urgent tickets
  - High-value deals closing soon
  - Re-engagement opportunities

---

## 3. Contact Management

### 3.1 Companies
**Path:** `CRM Hub → Contact Management → Companies`

- Click **Add Company** to register a corporate client (e.g., Zambia Mining Corp).
- Fill in name, industry, address, city, annual revenue, employee count.
- Assign an agent if needed.
- Click a company row to **View** or **Edit** it.
- Inside a company page, you can **Link Contacts** to associate employees/decision-makers with that company.

### 3.2 Contacts (Clients Directory)
**Path:** `Admin Sidebar → Clients`

- Filter by **Leads, Active, High Value, Blocked**.
- Click any client name to see their profile.
- Click the **360° View** icon to open the full contact view.

### 3.3 360° Contact View
**Path:** Click a client → "360° View" or go directly to `/admin/crm/contacts/{id}/360`

This page aggregates everything about one person:
- **Lifetime Value** — total money spent.
- **Activity Timeline** — shipments, emails, calls, deals, tickets in chronological order.
- **Notes & Preferences** — log calls, meetings, purchases, or preferences.
- **Linked Companies** — which corporate accounts they belong to.

**How to add a note:**
1. Scroll to "Notes & Preferences".
2. Select the note type (Note, Call, Email, Meeting, Purchase, Preference).
3. Type your content.
4. Click **Add Note**.

---

## 4. Sales Pipeline & Opportunity Management

### 4.1 The Pipeline
**Path:** `CRM Hub → Sales Automation → Pipeline`

- This shows all deals organized by stage.
- At the top, you see **stage cards** with deal counts and total value.
- Use the filter bar to filter by:
  - Stage (Prospect, Qualified, Proposal, etc.)
  - Agent (who owns the deal)
  - Priority (Low, Medium, High)
- To move a deal forward, use the **Move →** dropdown in the Actions column and select the next stage.

### 4.2 Creating a Deal
**Path:** Pipeline page → **New Deal** button

1. Enter deal **Title**.
2. Link to a **Company** and/or **Contact**.
3. Select the current **Stage**.
4. Enter **Value** and **Currency** (default ZMW).
5. Set **Expected Close Date**.
6. Choose **Priority** and add any internal **Notes**.
7. Click Save.

### 4.3 Deal Stages
**Path:** `CRM Hub → Sales Automation → Stages`

- Add custom stages to match your sales process.
- For each stage, set:
  - **Name** (e.g., "Proposal Sent")
  - **Color** (for visual identification)
  - **Position** (order in the pipeline)
  - **Win Probability** (0–100%)
  - **Closed / Won** flags (for forecast calculations)

### 4.4 Lead Routing / Scoring
**Path:** `CRM Hub → Sales Automation → Lead Routing`

- All users with `crm_status = lead` appear here.
- Each lead shows a **Lead Score** (0–100), colored green/yellow/red.
- To convert a lead to an active client:
  1. Use the **Assign →** dropdown to assign an agent.
  2. The system automatically changes their status from `lead` to `active`.

### 4.5 Tasks
**Path:** `CRM Hub → Sales Automation → Tasks`

- Track follow-ups, calls, meetings, emails, proposals.
- Stats at the top: Total, Pending, Overdue, Completed.
- Filter by status or type.
- **Quick Create:** Fill the form at the top of the page.
- To mark a task done, click the **checkmark icon**.

### 4.6 Quotes & Proposals
**Path:** `CRM Hub → Sales Automation → Quotes & Proposals`

- Create Quotes, Proposals, Contracts, or Invoices.
- Each document has a status: **Draft → Sent → Accepted / Rejected / Expired**.
- From the list, click the **paper plane icon** to mark a draft as "Sent".

### 4.7 Forecasting
**Path:** `CRM Hub → Sales Automation → Forecasting`

- **3-Month Outlook:** Shows expected deals per month, total value, and **weighted forecast** (value × win probability).
- **Agent Performance:** Compares agents by deal count and pipeline value.
- **Quota Tracking:** Visual progress bars against a 100,000 ZMW target.

---

## 5. Marketing Automation

### 5.1 Campaigns
**Path:** `CRM Hub → Marketing → Campaigns`

- Track Email, Social, Digital Ad, and Mixed campaigns.
- Top cards show: Active campaigns, Total Budget, Leads Generated, Conversions.
- **Create a campaign:** Use the Quick Create form (name, type, status, dates, budget).
- **Update results:** Edit Spent, Leads, and Conversions inline and click the **Save icon**.

### 5.2 Landing Pages
**Path:** `CRM Hub → Marketing → Landing Pages`

- Create landing pages with a unique **slug**, UTM source, and UTM medium.
- Track **Views** and **Submissions** to calculate **Conversion Rate**.
- Status workflow: **Draft → Published → Archived**.
- Use the publish/archive buttons to change status.

---

## 6. Communications

### 6.1 Bulk SMS
**Path:** `CRM Hub → Communications → Bulk SMS`

- Select contacts from the list (filter by Leads, Active, High Value, or Company).
- Compose a message up to 640 characters.
- Click **Send Bulk SMS** to dispatch via Molo Marketing Cloud.
- Results show how many were sent vs failed.

### 6.2 Bulk WhatsApp
**Path:** `CRM Hub → Communications → Bulk WhatsApp`

Before sending bulk WhatsApp, make sure your account is set up:
1. **QR Setup** — Click "QR Setup" to authorize your WhatsApp number via Green API.
2. **Warm-up** — New numbers are vulnerable to blocking. A banner shows your warm-up day (aim for 10+ days before heavy bulk).
3. **Response Ratio** — Keep your 7-day response ratio at 50% or higher. Start messages with a question to get replies.
4. **Pre-send Checklist** — The page shows:
   - Account authorized?
   - Message queue empty?
   - Response ratio healthy?
   - Warm-up complete?
5. **Best-practice Templates** — Use the template buttons:
   - **Question First** — e.g. "Do you want to receive our latest freight rates?"
   - **Short & Personal** — Use `{name}` for personalization.
   - **Promo with Opt-out** — Always include "Reply STOP to unsubscribe."
6. **Queue** — Clear the message queue before each bulk send using the "Clear" button.
7. **Opt-outs** — Recipients who reply STOP are automatically unsubscribed and excluded from future sends.

**Auto-processing:**
- The system polls for incoming messages every minute automatically.
- STOP replies are processed and the sender is unsubscribed instantly.
- You can also manually poll at `/admin/crm/communications/whatsapp/receive`.

**Anti-blocking tips:**
- Keep messages short (under 800 chars ideally).
- Address recipients by name (`{name}`).
- Ask questions to encourage replies.
- Always include an opt-out footer.
- Verify each recipient has WhatsApp before sending.
- Never send more than 100 messages/day on a new number.

---

## 7. Customer Service & Ticketing

### 7.1 Support Tickets
**Path:** `CRM Hub → Customer Support → Tickets`

- Omni-channel view: tickets from **Email, Chat, Phone, Web**.
- Top stats: Open, In Progress, Resolved, Urgent.
- Filter by status or priority.
- **Create a ticket:** Use the Quick Create form (subject, channel, priority, assignee).
- Click the **eye icon** to open a ticket detail page.

### 7.2 Ticket Detail Page
**Path:** Click any ticket

- Shows full conversation history.
- **Status dropdown:** Open → In Progress → Waiting → Resolved → Closed.
- **Assign dropdown:** Reassign to any admin agent.
- **Reply:** Type a message and click **Send Reply**.
- Check **Internal Note** if the reply should not be visible to the customer.

### 7.3 Knowledge Base
**Path:** `CRM Hub → Customer Support → Knowledge Base`

- Publish self-service articles to reduce ticket volume.
- Categories: General, Billing, Technical, Shipping, Account.
- **Create article:** Title, slug, category, status, and content.
- Check **Internal Only** if the article is for agents only.
- Change status inline: Draft / Published / Archived.
- View counts show how often customers/readers accessed each article.

---

## 8. Analytics & Reporting

### 8.1 CRM Analytics
**Path:** `CRM Hub → Reporting & AI → Analytics`

- Filter by period: **Last 7 Days, 30 Days, Quarter, or Year**.
- **Deals by Stage:** Bar chart showing deal distribution.
- **Contact Growth:** Bar chart of new sign-ups over time.
- **Agent Performance:** Table with deals count, pipeline value, and quota progress bars.
- **Tickets by Status:** Breakdown with percentage bars.
- **Campaign Performance:** Leads and conversions per campaign.

---

## 9. Quick Reference: URL Cheat Sheet

| Feature | URL |
|---|---|
| Admin Login | `/admin/login` |
| Admin Dashboard | `/admin/dashboard` |
| CRM Dashboard | `/admin/crm/reports` |
| Analytics | `/admin/crm/analytics` |
| Companies | `/admin/crm/companies` |
| Pipeline | `/admin/crm/pipeline` |
| Stages | `/admin/crm/stages` |
| Leads | `/admin/crm/leads` |
| Tasks | `/admin/crm/tasks` |
| Documents | `/admin/crm/documents` |
| Forecast | `/admin/crm/forecast` |
| Campaigns | `/admin/crm/campaigns` |
| Landing Pages | `/admin/crm/landing-pages` |
| Tickets | `/admin/crm/tickets` |
| Knowledge Base | `/admin/crm/knowledge-base` |
| Clients | `/admin/clients` |
| Bulk SMS | `/admin/crm/communications/sms` |
| Bulk WhatsApp | `/admin/crm/communications/whatsapp` |
| WhatsApp QR | `/admin/crm/communications/whatsapp/qr` |

---

## 10. Workflow Example: Closing a Deal

1. **Lead comes in** → Appears in `/admin/crm/leads`. Assign to agent → becomes Active.
2. **Create a Deal** → `/admin/crm/pipeline` → New Deal → Stage = "Prospect".
3. **Create Tasks** → `/admin/crm/tasks` → "Call lead", "Send proposal".
4. **Move Deal** → As conversations progress, update stage: Qualified → Proposal → Negotiation.
5. **Send Document** → `/admin/crm/documents` → Create Quote → Mark as Sent.
6. **Close Deal** → Move stage to "Closed Won". Revenue automatically appears in Forecasting.
7. **Support** → If the client has issues, create a ticket in `/admin/crm/tickets`.

---

## 11. Tips

- **Lead Score:** Auto-calculated based on activity. High scores (green) are hot leads.
- **Overdue Tasks:** Check the CRM Dashboard AI Insights — it warns you automatically.
- **Urgent Tickets:** Also flagged in AI Insights so nothing slips through.
- **360° View:** Always check this before calling a client — you'll see their full history.
- **Forecast Confidence:** The weighted forecast uses stage win probability to give realistic revenue projections.

---

**Happy CRM-ing! 🚛📊**
