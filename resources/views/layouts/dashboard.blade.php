<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Forus Freight'))</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <meta name="robots" content="noindex, nofollow">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --bg-body: #f4f7f6;
            --bg-sidebar: #ffffff;
            --primary: #007f7f;
            --primary-green: #4caf50;
            --primary-green-light: #e8f5e9;
            --text-dark: #2d3436;
            --text-gray: #636e72;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(0,0,0,0.05);
            z-index: 100;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(76, 175, 80, 0.3) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(76, 175, 80, 0.3);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(76, 175, 80, 0.5);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 3rem;
            text-decoration: none;
        }

        .sidebar-logo img {
            height: 52px;
            width: auto;
        }

        .sidebar-logo span {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -1px;
        }

        .nav-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex-grow: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1.25rem;
            text-decoration: none;
            color: var(--text-gray);
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            background: var(--primary-green-light);
            color: var(--primary-green);
        }

        .nav-item.active {
            background: var(--primary-green-light);
            color: var(--primary-green);
            box-shadow: 0 2px 10px rgba(76, 175, 80, 0.1);
        }

        .nav-item i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        /* Collapsible Sidebar Menu */
        .nav-item-collapsible {
            display: flex;
            flex-direction: column;
        }

        .nav-sub-group {
            max-height: 0;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            padding-left: 1.5rem;
            opacity: 0;
            visibility: hidden;
        }

        .nav-item-collapsible.expanded .nav-sub-group {
            max-height: 300px;
            opacity: 1;
            visibility: visible;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .nav-item-collapsible .chevron {
            margin-left: auto;
            font-size: 0.7rem;
            transition: transform 0.3s ease;
            opacity: 0.6;
        }

        .nav-item-collapsible.expanded .chevron {
            transform: rotate(180deg);
        }

        .sub-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .sub-nav-item:hover {
            color: var(--primary-green);
            background: #f8fafc;
        }

        .sub-nav-item i {
            font-size: 0.4rem;
            opacity: 0.4;
        }

        .sub-nav-item.active {
            color: var(--primary-green);
            background: var(--primary-green-light);
        }

        .main-content {
            margin-left: 280px;
            padding: 3.5rem;
            min-height: 100vh;
        }

        .welcome-section {
            margin-bottom: 3.5rem;
        }

        .sidebar-footer {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 2rem 3rem;
            width: calc(100% - var(--sidebar-width));
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .search-box {
            position: relative;
            width: 400px;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border-radius: 50px;
            border: 1px solid rgba(0,0,0,0.05);
            background: var(--white);
            font-size: 0.9rem;
            outline: none;
            box-shadow: var(--shadow);
        }

        .search-box input:focus {
            box-shadow: var(--shadow), 0 0 0 3px rgba(76, 175, 80, 0.35);
        }

        .search-box i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn-create {
            background: #ff6200;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            border: none;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 98, 0, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 98, 0, 0.4);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            box-shadow: var(--shadow);
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 220px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid #f1f5f9;
            margin-top: 1rem;
            padding: 0.75rem;
            display: none;
            z-index: 1000;
        }

        .user-dropdown.active {
            display: block;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes flashSlideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .flash-message {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.1rem 1.25rem;
            border-radius: 16px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 1.5rem;
            animation: flashSlideIn 0.3s ease;
        }
        .flash-message > i:first-child { font-size: 1.15rem; margin-top: 0.1rem; }
        .flash-message > span { flex: 1; }
        .flash-dismiss {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: 0.6;
            font-size: 0.85rem;
            padding: 0.15rem;
            flex-shrink: 0;
        }
        .flash-dismiss:hover { opacity: 1; }
        .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .flash-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: var(--primary-green-light);
            color: var(--primary-green);
        }

        .dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 0.5rem 0;
        }

        .user-profile img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-profile span {
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Mobile Adjustments */
        @media (max-width: 1024px) {
            .sidebar { 
                transform: translateX(-100%); 
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; padding: 1.5rem; }
            .search-box { width: 100%; max-width: 200px; }
            #mobileToggle { display: block !important; }
            #closeSidebar { display: block !important; }
        }
    </style>
    @yield('styles')
    @livewireStyles
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <a href="{{ Auth::user()->isStaff() ? route('admin.dashboard') : route('dashboard') }}" class="sidebar-logo" style="margin-bottom: 0;">
                <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight">
            </a>
            <button id="closeSidebar" style="display: none; background: none; border: none; font-size: 1.25rem; color: var(--text-gray); cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="nav-group">
            @php
                $u = Auth::user();
                $isAdmin = $u->is_admin;
                $isStaff = $isAdmin || $u->hasAnyRole(['admin_staff', 'sales']);
                $canContacts = $u->hasPermission('crm.contacts.view');
                $canCompanies = $u->hasPermission('crm.companies.view');
                $canPipeline = $u->hasPermission('crm.pipeline.view');
                $canDeals = $u->hasPermission('crm.deals.manage');
                $canTasks = $u->hasPermission('crm.tasks.manage');
                $canDocs = $u->hasPermission('crm.documents.manage');
                $canForecast = $u->hasPermission('crm.forecast.view');
                $canLeads = $u->hasPermission('crm.leads.manage');
                $canCampaigns = $u->hasPermission('crm.campaigns.manage');
                $canLanding = $u->hasPermission('crm.landing_pages.manage');
                $canTickets = $u->hasPermission('crm.tickets.manage');
                $canKB = $u->hasPermission('crm.knowledge_base.manage');
                $canCRMReports = $u->hasPermission('crm.reports.view');
                $canCommunications = $u->hasPermission('crm.communications.manage');
                $canShipments = $u->hasPermission('admin.shipments.view');
                $canSysReports = $u->hasPermission('admin.reports.view');
                $canExchange = $u->hasPermission('admin.exchange_rates.manage');
                $canCMS = $u->hasPermission('admin.cms.manage');
                $canUserMgmt = $isAdmin; // only super-admin manages roles
            @endphp

            @if($isAdmin || $u->hasAnyPermission(['admin.shipments.view','admin.reports.view','admin.exchange_rates.manage']))
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    Admin Home
                </a>
                @if($canShipments)
                <div class="nav-item-collapsible {{ request()->routeIs('admin.shipments') || request()->routeIs('admin.shipments.bulk*') ? 'expanded' : '' }}">
                    <a href="javascript:void(0)" onclick="toggleSidebarMenu(this)" class="nav-item {{ request()->routeIs('admin.shipments') || request()->routeIs('admin.shipments.bulk*') ? 'active' : '' }}">
                        <i class="fas fa-location-crosshairs"></i>
                        <span>Shipments</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </a>
                    <div class="nav-sub-group">
                        <a href="{{ route('admin.shipments') }}" class="sub-nav-item {{ request()->routeIs('admin.shipments') && !request()->routeIs('admin.shipments.bulk*') ? 'active' : '' }}">
                            <i class="fas fa-circle"></i> All Shipments
                        </a>
                        <a href="{{ route('admin.shipments.bulk.index') }}" class="sub-nav-item {{ request()->routeIs('admin.shipments.bulk*') ? 'active' : '' }}">
                            <i class="fas fa-circle"></i> Bulk Shipment Import
                        </a>
                    </div>
                </div>
                @endif
                @if($canSysReports)
                <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    System Reports
                </a>
                @endif
                @if($canExchange)
                <a href="{{ route('admin.exchange-rates') }}" class="nav-item {{ request()->routeIs('admin.exchange-rates*') ? 'active' : '' }}">
                    <i class="fas fa-coins"></i>
                    Exchange Rates
                </a>
                @endif
            @endif

            <!-- CRM Hub -->
            @if($isAdmin || $u->hasAnyPermission(['crm.contacts.view','crm.companies.view','crm.pipeline.view','crm.deals.manage','crm.tasks.manage','crm.documents.manage','crm.forecast.view','crm.leads.manage','crm.campaigns.manage','crm.landing_pages.manage','crm.tickets.manage','crm.knowledge_base.manage','crm.reports.view','crm.communications.manage']))
            <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; margin: 1.5rem 0 0.5rem 1.25rem; letter-spacing: 0.05em;">CRM Hub</div>

            @if($canContacts || $canCompanies)
            <div class="nav-item-collapsible {{ request()->routeIs('admin.crm.companies*') || request()->routeIs('admin.crm.contacts*') || request()->routeIs('admin.clients*') || request()->has('status') ? 'expanded' : '' }}">
                <a href="javascript:void(0)" onclick="toggleSidebarMenu(this)" class="nav-item {{ request()->routeIs('admin.crm.companies*') || request()->routeIs('admin.crm.contacts*') || request()->routeIs('admin.clients*') ? 'active' : '' }}">
                    <i class="fas fa-address-book"></i>
                    <span>Contact Management</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-sub-group">
                    @if($canCompanies)
                    <a href="{{ route('admin.crm.companies') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.companies*') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Companies
                    </a>
                    @endif
                    {{-- Was gated on crm.contacts.view but pointed at
                         admin.clients (shipment customers), which needs the
                         separate admin.clients.view permission — a dead end
                         for Sales, which has the former but not the latter.
                         Gate on the permission the destination actually
                         requires instead. --}}
                    @if(Auth::user()->hasPermission('admin.clients.view'))
                    <a href="{{ route('admin.clients') }}" class="sub-nav-item {{ request()->routeIs('admin.clients*') || request()->has('status') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Contacts
                    </a>
                    @endif
                </div>
            </div>
            @endif

            @if($canPipeline || $canDeals || $canTasks || $canDocs || $canForecast || $canLeads)
            <div class="nav-item-collapsible {{ request()->routeIs('admin.crm.pipeline') || request()->routeIs('admin.crm.deals*') || request()->routeIs('admin.crm.tasks') || request()->routeIs('admin.crm.documents') || request()->routeIs('admin.crm.forecast') || request()->routeIs('admin.crm.leads') || request()->routeIs('admin.crm.stages') ? 'expanded' : '' }}">
                <a href="javascript:void(0)" onclick="toggleSidebarMenu(this)" class="nav-item {{ request()->routeIs('admin.crm.pipeline') || request()->routeIs('admin.crm.deals*') || request()->routeIs('admin.crm.tasks') || request()->routeIs('admin.crm.documents') || request()->routeIs('admin.crm.forecast') || request()->routeIs('admin.crm.leads') || request()->routeIs('admin.crm.stages') ? 'active' : '' }}">
                    <i class="fas fa-funnel-dollar"></i>
                    <span>Sales Automation</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-sub-group">
                    @if($canPipeline)
                    <a href="{{ route('admin.crm.pipeline') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.pipeline') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Pipeline
                    </a>
                    @endif
                    @if($canLeads)
                    <a href="{{ route('admin.crm.leads') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.leads') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Lead Routing
                    </a>
                    @endif
                    @if($canTasks)
                    <a href="{{ route('admin.crm.tasks') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.tasks') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Tasks
                    </a>
                    @endif
                    @if($canDocs)
                    <a href="{{ route('admin.crm.documents') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.documents') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Quotes & Proposals
                    </a>
                    @endif
                    @if($canForecast)
                    <a href="{{ route('admin.crm.forecast') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.forecast') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Forecasting
                    </a>
                    @endif
                </div>
            </div>
            @endif

            @if($canCampaigns || $canLanding)
            <div class="nav-item-collapsible {{ request()->routeIs('admin.crm.campaigns') || request()->routeIs('admin.crm.landing-pages') ? 'expanded' : '' }}">
                <a href="javascript:void(0)" onclick="toggleSidebarMenu(this)" class="nav-item {{ request()->routeIs('admin.crm.campaigns') || request()->routeIs('admin.crm.landing-pages') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Marketing</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-sub-group">
                    @if($canCampaigns)
                    <a href="{{ route('admin.crm.campaigns') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.campaigns') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Campaigns
                    </a>
                    @endif
                    @if($canLanding)
                    <a href="{{ route('admin.crm.landing-pages') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.landing-pages') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Landing Pages
                    </a>
                    @endif
                </div>
            </div>
            @endif

            @if($canCommunications)
            <div class="nav-item-collapsible {{ request()->routeIs('admin.crm.communications*') ? 'expanded' : '' }}">
                <a href="javascript:void(0)" onclick="toggleSidebarMenu(this)" class="nav-item {{ request()->routeIs('admin.crm.communications*') ? 'active' : '' }}">
                    <i class="fas fa-paper-plane"></i>
                    <span>Communications</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-sub-group">
                    <a href="{{ route('admin.crm.communications.sms') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.communications.sms*') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Bulk SMS
                    </a>
                    <a href="{{ route('admin.crm.communications.whatsapp') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.communications.whatsapp*') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Bulk WhatsApp
                    </a>
                </div>
            </div>
            @endif

            @if($canTickets || $canKB)
            <div class="nav-item-collapsible {{ request()->routeIs('admin.crm.tickets*') || request()->routeIs('admin.crm.knowledge-base*') ? 'expanded' : '' }}">
                <a href="javascript:void(0)" onclick="toggleSidebarMenu(this)" class="nav-item {{ request()->routeIs('admin.crm.tickets*') || request()->routeIs('admin.crm.knowledge-base*') ? 'active' : '' }}">
                    <i class="fas fa-headset"></i>
                    <span>Customer Support</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-sub-group">
                    @if($canTickets)
                    <a href="{{ route('admin.crm.tickets') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.tickets*') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Tickets
                    </a>
                    @endif
                    @if($canKB)
                    <a href="{{ route('admin.crm.knowledge-base') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.knowledge-base*') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Knowledge Base
                    </a>
                    @endif
                </div>
            </div>
            @endif

            @if($canCRMReports)
            <div class="nav-item-collapsible {{ request()->routeIs('admin.crm.reports') || request()->routeIs('admin.crm.analytics') ? 'expanded' : '' }}">
                <a href="javascript:void(0)" onclick="toggleSidebarMenu(this)" class="nav-item {{ request()->routeIs('admin.crm.reports') || request()->routeIs('admin.crm.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Reporting & AI</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-sub-group">
                    <a href="{{ route('admin.crm.reports') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.reports') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> CRM Dashboard
                    </a>
                    <a href="{{ route('admin.crm.analytics') }}" class="sub-nav-item {{ request()->routeIs('admin.crm.analytics') ? 'active' : '' }}">
                        <i class="fas fa-circle"></i> Analytics
                    </a>
                </div>
            </div>
            @endif
            @endif

            @if($canCMS)
            <a href="{{ route('admin.cms.pages.index') }}" class="nav-item {{ request()->routeIs('admin.cms.pages*') ? 'active' : '' }}">
                <i class="fas fa-globe"></i>
                <span>Website CMS</span>
            </a>
            @endif

            @if($canUserMgmt)
            <a href="{{ route('admin.staff.index') }}" class="nav-item {{ request()->routeIs('admin.staff*') ? 'active' : '' }}">
                <i class="fas fa-users-gear"></i>
                <span>Staff & Roles</span>
            </a>
            <a href="{{ route('admin.settings.phone-countries') }}" class="nav-item {{ request()->routeIs('admin.settings.phone-countries*') ? 'active' : '' }}">
                <i class="fas fa-earth-africa"></i>
                <span>Phone Countries</span>
            </a>
            @endif

            @if(!Auth::user()->is_admin && !Auth::user()->hasAnyPermission(['crm.contacts.view','crm.companies.view','crm.pipeline.view','crm.deals.manage','crm.tasks.manage','crm.documents.manage','crm.forecast.view','crm.leads.manage','crm.campaigns.manage','crm.landing_pages.manage','crm.tickets.manage','crm.knowledge_base.manage','crm.reports.view','admin.shipments.view','admin.reports.view','admin.exchange_rates.manage']))
                <!-- Main Navigation -->
                <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; margin: 1.5rem 0 0.5rem 1.25rem; letter-spacing: 0.05em;">Main Navigation</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-house"></i>
                    Dashboard
                </a>
                <a href="{{ route('client.tracking.auto') }}" class="nav-item {{ request()->routeIs('client.tracking.auto') || request()->routeIs('tracking.show') ? 'active' : '' }}">
                    <i class="fas fa-location-crosshairs"></i>
                    Real-time Tracking
                </a>
                <a href="{{ route('client.getting-started') }}" class="nav-item {{ request()->routeIs('client.getting-started') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    Getting Started
                </a>

                <!-- Cargo Operations -->
                <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; margin: 1.5rem 0 0.5rem 1.25rem; letter-spacing: 0.05em;">Shipments</div>
                <a href="{{ route('client.shipments') }}" class="nav-item {{ request()->routeIs('client.shipments') ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i>
                    My Shipments
                </a>
                <a href="{{ route('client.shipments.create') }}" class="nav-item {{ request()->routeIs('client.shipments.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    New Request
                </a>

                <!-- Finance -->
                <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; margin: 1.5rem 0 0.5rem 1.25rem; letter-spacing: 0.05em;">Finance & Billing</div>
                <a href="{{ route('client.invoices') }}" class="nav-item {{ request()->routeIs('client.invoices*') || request()->routeIs('client.payments*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Invoices & Payments
                </a>
            @endif

            <!-- Shared Account Section -->
            <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; margin: 1.5rem 0 0.5rem 1.25rem; letter-spacing: 0.05em;">Account & Settings</div>
            @if($isStaff)
                <a href="{{ route('admin.profile') }}" class="nav-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i>
                    My Profile
                </a>
                <a href="{{ route('admin.security') }}" class="nav-item {{ request()->routeIs('admin.security') ? 'active' : '' }}">
                    <i class="fas fa-shield-halved"></i>
                    Security
                </a>
                <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-sliders"></i>
                    Settings
                </a>
                <a href="{{ route('admin.help') }}" class="nav-item {{ request()->routeIs('admin.help') ? 'active' : '' }}">
                    <i class="fas fa-circle-question"></i>
                    Help Center
                </a>
            @else
                <a href="{{ route('client.profile') }}" class="nav-item {{ request()->routeIs('client.profile') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i>
                    My Profile
                </a>
                <a href="{{ route('client.security') }}" class="nav-item {{ request()->routeIs('client.security') ? 'active' : '' }}">
                    <i class="fas fa-shield-halved"></i>
                    Security
                </a>
                <a href="{{ route('client.settings') }}" class="nav-item {{ request()->routeIs('client.settings') ? 'active' : '' }}">
                    <i class="fas fa-sliders"></i>
                    Settings
                </a>
                <a href="{{ route('client.help') }}" class="nav-item {{ request()->routeIs('client.help') ? 'active' : '' }}">
                    <i class="fas fa-circle-question"></i>
                    Help Center
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
                {{-- This layout is shared by admin and client pages, and each
                     logs in on a separate guard so both sessions can be held
                     in the same browser at once. Tell the logout route which
                     one *this* page's Logout link belongs to, so it doesn't
                     also end the other, still-active session. --}}
                <input type="hidden" name="guard" value="{{ Auth::user()->isStaff() ? 'admin' : 'web' }}">
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button id="mobileToggle" style="display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-dark);">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="top-actions">
                <div style="display: flex; gap: 1rem; color: var(--text-gray); font-size: 1.2rem;">
                    <i class="far fa-bell" style="cursor: pointer;"></i>
                </div>

                <div class="user-profile" id="userDropdownTrigger">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4caf50&color=fff" alt="{{ Auth::user()->name }}">
                    <span>{{ explode(' ', Auth::user()->name)[0] }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.7rem; color: var(--text-gray);"></i>

                    <!-- Dropdown Menu -->
                    <div class="user-dropdown" id="userDropdown">
                        <div style="padding: 0.5rem 1rem;">
                            <p style="font-size: 0.85rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">{{ Auth::user()->name }}</p>
                            <p style="font-size: 0.7rem; color: var(--text-gray); font-weight: 600;">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="dropdown-divider"></div>
                        @if(Auth::user()->isStaff())
                            <a href="{{ route('admin.profile') }}" class="dropdown-item">
                                <i class="fas fa-user-circle"></i>
                                My Profile
                            </a>
                            <a href="{{ route('admin.settings') }}" class="dropdown-item">
                                <i class="fas fa-sliders"></i>
                                Account Settings
                            </a>
                        @else
                            <a href="{{ route('client.profile') }}" class="dropdown-item">
                                <i class="fas fa-user-circle"></i>
                                My Profile
                            </a>
                            <a href="{{ route('client.settings') }}" class="dropdown-item">
                                <i class="fas fa-sliders"></i>
                                Account Settings
                            </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item" style="color: #ef4444;">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        {{-- Centralised so every admin/client page gets this automatically —
             most pages here never rendered session('success')/('error') or
             validation errors at all, so a failed action could silently
             produce no feedback whatsoever. --}}
        <div id="globalFlashMessages" style="padding: 0 2.5rem;">
            @if(session('success'))
                <div class="flash-message flash-success" role="status">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="flash-dismiss" onclick="this.closest('.flash-message').remove()" aria-label="Dismiss"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="flash-message flash-error" role="alert">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="flash-dismiss" onclick="this.closest('.flash-message').remove()" aria-label="Dismiss"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('info'))
                <div class="flash-message flash-info" role="status">
                    <i class="fas fa-circle-info"></i>
                    <span>{{ session('info') }}</span>
                    <button type="button" class="flash-dismiss" onclick="this.closest('.flash-message').remove()" aria-label="Dismiss"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if($errors->any())
                <div class="flash-message flash-error" role="alert">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>
                        @if($errors->count() === 1)
                            {{ $errors->first() }}
                        @else
                            <strong>Please fix the following:</strong>
                            <ul style="margin:0.35rem 0 0 1.1rem; padding:0;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </span>
                    <button type="button" class="flash-dismiss" onclick="this.closest('.flash-message').remove()" aria-label="Dismiss"><i class="fas fa-times"></i></button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <script>
        function toggleSidebarMenu(element) {
            const container = element.parentElement;
            container.classList.toggle('expanded');
        }

        // Mobile Toggle
        document.getElementById('mobileToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('active');
        });

        document.getElementById('closeSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('active');
        });

        // User Dropdown Toggle
        const dropdownTrigger = document.getElementById('userDropdownTrigger');
        const userDropdown = document.getElementById('userDropdown');

        dropdownTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!dropdownTrigger.contains(event.target)) {
                userDropdown.classList.remove('active');
            }

            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('mobileToggle');
            if (window.innerWidth <= 1024 && sidebar && toggle && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Applies to every plain form on every admin/client page automatically
        // (almost none of them showed any feedback while a submit was in
        // flight, so a slow request looked identical to a dead click and
        // invited double-submits). Livewire forms already have their own
        // wire:loading handling and are skipped here so the two don't fight.
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (e.defaultPrevented) return; // an inline onsubmit (e.g. a declined confirm()) already cancelled this
            if (form.hasAttribute('wire:submit') || form.hasAttribute('wire:submit.prevent')) return;
            if (form.dataset.noLoader !== undefined) return; // explicit opt-out escape hatch

            // Per-form copy via data-loading-label (e.g. "Uploading & importing…"),
            // so the spinner answers "what is happening" instead of a generic
            // "Processing..." everywhere. Falls back to the default.
            const label = form.dataset.loadingLabel || 'Processing...';

            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (btn) {
                if (btn.disabled) return;
                if (btn.tagName === 'BUTTON') {
                    btn.dataset.originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> ' + label;
                } else {
                    btn.dataset.originalValue = btn.value;
                    btn.value = label;
                }
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.style.cursor = 'not-allowed';
            });
        });
    </script>
    @livewireScripts
</body>
</html>
