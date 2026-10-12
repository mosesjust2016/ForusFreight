<nav class="communication-tabs" aria-label="Communication channels">
    <a href="{{ route('admin.crm.communications.sms') }}" class="{{ request()->routeIs('admin.crm.communications.sms') ? 'active' : '' }}">
        <i class="fas fa-comment-sms"></i> SMS
    </a>
    <a href="{{ route('admin.crm.communications.whatsapp') }}" class="{{ request()->routeIs('admin.crm.communications.whatsapp') ? 'active' : '' }}">
        <i class="fab fa-whatsapp"></i> WhatsApp
    </a>
</nav>
