<style>
    .crm-workspace-nav { display:flex; align-items:center; gap:.35rem; flex-wrap:wrap; margin-bottom:1.5rem; padding:.35rem; background:rgba(255,255,255,.72); border:1px solid rgba(13,27,32,.09); border-radius:14px; }
    .crm-workspace-nav a { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem .9rem; border-radius:10px; color:var(--ink-2,#33444a); text-decoration:none; font-size:.78rem; font-weight:800; }
    .crm-workspace-nav a:hover { background:var(--paper-2,#ebe9e2); color:var(--ink,#0d1b20); }
    .crm-workspace-nav a.active { background:var(--orange,#ff6200); color:var(--ink,#0d1b20); }
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
    @media (max-width: 640px) { .crm-workspace-nav .workspace-label { width:100%; margin:.3rem .55rem 0; } }
</style>
<nav class="crm-workspace-nav" aria-label="CRM workspace">
    <span class="workspace-label">CRM workspace</span>
    <a href="{{ route('admin.crm.companies') }}" class="{{ request()->routeIs('admin.crm.companies*','admin.crm.contacts*') ? 'active' : '' }}"><i class="fas fa-address-book"></i> Contacts</a>
    <a href="{{ route('admin.crm.pipeline') }}" class="{{ request()->routeIs('admin.crm.pipeline','admin.crm.deals*','admin.crm.tasks','admin.crm.documents','admin.crm.forecast','admin.crm.leads','admin.crm.stages') ? 'active' : '' }}"><i class="fas fa-chart-line"></i> Sales</a>
    <a href="{{ route('admin.crm.campaigns') }}" class="{{ request()->routeIs('admin.crm.campaigns','admin.crm.landing-pages*') ? 'active' : '' }}"><i class="fas fa-bullhorn"></i> Marketing</a>
</nav>
