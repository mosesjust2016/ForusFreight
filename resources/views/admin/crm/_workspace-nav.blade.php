<style>
    .crm-workspace-nav { display:flex; align-items:center; gap:.35rem; flex-wrap:wrap; margin-bottom:1.5rem; padding:.4rem; background:rgba(255,255,255,.9); border:1px solid rgba(13,27,32,.09); border-radius:14px; box-shadow:0 8px 20px rgba(13,27,32,.04); position:sticky; top:1rem; z-index:20; }
    .crm-workspace-nav a { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem .9rem; border-radius:10px; color:var(--ink-2,#33444a); text-decoration:none; font-size:.78rem; font-weight:800; }
    .crm-workspace-nav a:hover { background:var(--paper-2,#ebe9e2); color:var(--ink,#0d1b20); }
    .crm-workspace-nav a.active { background:var(--orange,#ff6200); color:var(--ink,#0d1b20); box-shadow:0 4px 10px rgba(255,98,0,.2); }
    .crm-workspace-nav details { position:relative; }
    .crm-workspace-nav summary { list-style:none; cursor:pointer; display:inline-flex; align-items:center; gap:.45rem; padding:.65rem .9rem; border-radius:10px; color:var(--ink-2,#33444a); font-size:.78rem; font-weight:800; }
    .crm-workspace-nav summary::-webkit-details-marker { display:none; }
    .crm-workspace-nav details[open] summary { background:var(--paper-2,#ebe9e2); }
    .crm-workspace-nav .crm-more-menu { position:absolute; z-index:10; top:calc(100% + .35rem); right:0; min-width:190px; padding:.35rem; background:#fff; border:1px solid rgba(13,27,32,.1); border-radius:12px; box-shadow:0 14px 30px rgba(13,27,32,.14); }
    .crm-workspace-nav .crm-more-menu a { width:100%; box-sizing:border-box; }
    .crm-workspace-nav .workspace-label { margin:0 .45rem 0 .6rem; color:var(--muted,#56666c); font-size:.68rem; font-weight:900; text-transform:uppercase; letter-spacing:.08em; }
    .crm-grid, .stage-card, .stat-card, .deal-card, .pipeline-column { border:1px solid rgba(13,27,32,.08); box-shadow:0 12px 30px rgba(13,27,32,.06); }
    .crm-grid { width:100%; border-radius:18px; background:#fff; }
    .crm-form-card { width:100%; max-width:none !important; box-sizing:border-box; }
    .stat-card { border-radius:16px; position:relative; overflow:hidden; text-align:left !important; }
    .stat-card::after { content:''; position:absolute; inset:auto 0 0 0; height:3px; background:var(--orange,#ff6200); }
    .stage-card { border-radius:16px; transition:transform .18s ease, box-shadow .18s ease; }
    .stage-card:hover, .deal-card:hover { transform:translateY(-3px); box-shadow:0 18px 38px rgba(13,27,32,.12); }
    .deal-card { border-radius:14px; border-color:rgba(13,27,32,.08) !important; }
    .deal-value { color:var(--orange,#ff6200) !important; }
    .action-btn:hover { background:var(--ink,#0d1b20) !important; color:#fff !important; }
    .btn-primary, .action-primary { background:var(--orange,#ff6200) !important; color:var(--ink,#0d1b20) !important; }
    .crm-page-intro, .welcome-section { max-width:100%; }
    .crm-page-intro h1, .welcome-section h1 { letter-spacing:-.035em; }
    .filter-bar a.active, .filter-bar .active { background:var(--orange,#ff6200) !important; color:var(--ink,#0d1b20) !important; }
    .crm-grid table th { color:var(--muted,#56666c) !important; letter-spacing:.07em; }
    @media (max-width: 760px) { .crm-grid { padding:1.15rem !important; border-radius:14px; } .stat-card { padding:1.1rem !important; } }
    .crm-page-intro h1 { letter-spacing:-.03em; }
    @media (max-width: 640px) { .crm-workspace-nav { position:relative; top:auto; overflow-x:auto; flex-wrap:nowrap; scrollbar-width:none; } .crm-workspace-nav::-webkit-scrollbar { display:none; } .crm-workspace-nav .workspace-label { flex:0 0 auto; margin:.3rem .55rem 0; } .crm-workspace-nav a, .crm-workspace-nav summary { flex:0 0 auto; } }
</style>
<nav class="crm-workspace-nav" aria-label="CRM workspace">
    <span class="workspace-label">CRM workspace</span>
    <a href="{{ route('admin.crm.home') }}" class="{{ request()->routeIs('admin.crm.home') ? 'active' : '' }}"><i class="fas fa-gauge-high"></i> Overview</a>
    <a href="{{ route('admin.crm.companies') }}" class="{{ request()->routeIs('admin.crm.companies*','admin.crm.contacts*') ? 'active' : '' }}"><i class="fas fa-address-book"></i> Contacts</a>
    <a href="{{ route('admin.crm.pipeline') }}" class="{{ request()->routeIs('admin.crm.pipeline','admin.crm.deals*','admin.crm.tasks','admin.crm.documents','admin.crm.forecast','admin.crm.leads','admin.crm.stages') ? 'active' : '' }}"><i class="fas fa-chart-line"></i> Sales</a>
    @if(Route::has('admin.crm.communications.whatsapp'))
        <a href="{{ route('admin.crm.communications.whatsapp') }}" class="{{ request()->routeIs('admin.crm.communications*') ? 'active' : '' }}"><i class="fab fa-whatsapp"></i> Inbox</a>
    @endif
    <a href="{{ route('admin.crm.campaigns') }}" class="{{ request()->routeIs('admin.crm.campaigns','admin.crm.landing-pages*') ? 'active' : '' }}"><i class="fas fa-bullhorn"></i> Campaigns</a>
    <details>
        <summary><i class="fas fa-ellipsis"></i> More</summary>
        <div class="crm-more-menu">
            @if(Route::has('admin.crm.tickets'))
                <a href="{{ route('admin.crm.tickets') }}" class="{{ request()->routeIs('admin.crm.tickets*','admin.crm.knowledge-base*') ? 'active' : '' }}"><i class="fas fa-headset"></i> Support</a>
            @endif
            @if(Route::has('admin.crm.reports'))
                <a href="{{ route('admin.crm.reports') }}" class="{{ request()->routeIs('admin.crm.reports*','admin.crm.analytics') ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Reports</a>
            @endif
            <a href="{{ route('admin.crm.documents') }}" class="{{ request()->routeIs('admin.crm.documents') ? 'active' : '' }}"><i class="fas fa-folder-open"></i> Documents</a>
            <a href="{{ route('admin.crm.stages') }}" class="{{ request()->routeIs('admin.crm.stages') ? 'active' : '' }}"><i class="fas fa-sliders"></i> Configuration</a>
        </div>
    </details>
</nav>
