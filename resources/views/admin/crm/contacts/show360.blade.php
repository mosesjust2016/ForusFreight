@extends('layouts.dashboard')

@section('title', $user->name . ' - 360 View - Forus Freight')

@section('styles')
<style>
    .info-card { background: white; border-radius: 24px; padding: 2rem; box-shadow: var(--shadow); height: 100%; }
    .section-title { font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
    .detail-item { margin-bottom: 1.25rem; }
    .detail-label { font-size: 0.75rem; color: var(--text-gray); font-weight: 800; text-transform: uppercase; margin-bottom: 0.25rem; }
    .detail-value { font-size: 1rem; font-weight: 700; color: var(--text-dark); }
    .timeline-item { position: relative; padding-left: 2rem; padding-bottom: 1.5rem; border-left: 2px solid #f1f5f9; }
    .timeline-item:last-child { border-left: 2px solid transparent; }
    .timeline-dot { position: absolute; left: -8px; top: 2px; width: 14px; height: 14px; border-radius: 50%; background: white; border: 3px solid var(--primary-green); }
    .timeline-date { font-size: 0.7rem; color: var(--text-gray); font-weight: 700; margin-bottom: 0.25rem; }
    .timeline-title { font-weight: 800; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 0.25rem; }
    .timeline-desc { font-size: 0.85rem; color: #475569; font-weight: 600; }
    .action-btn { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; background: #f8fafc; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-btn:hover { background: #1e293b; color: white; }

    @media (max-width: 900px) {
        div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
        div[style*="grid-template-columns: 2fr 1fr"] { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 480px) {
        div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: 1fr !important; }
    }
</style>
@endsection

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('admin.clients') }}" style="color: var(--text-gray); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-arrow-left"></i> Back to Client Directory
    </a>
</div>

<div style="background: white; border-radius: 24px; padding: 2.5rem; box-shadow: var(--shadow); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; align-items: center; gap: 1.5rem;">
        <div style="width: 80px; height: 80px; border-radius: 20px; background: var(--primary-green-light); color: var(--primary-green); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 900;">{{ substr($user->name, 0, 1) }}</div>
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 900; color: var(--text-dark); margin-bottom: 0.25rem;">{{ $user->name }}</h1>
            <div style="display: flex; gap: 1.5rem; color: var(--text-gray); font-size: 0.9rem; align-items: center;">
                <span><i class="fas fa-envelope"></i> {{ $user->email ?: 'No email on file' }}</span>
                <span><i class="fas fa-phone"></i> {{ $user->phone ?? 'No phone' }}</span>
                <span><i class="fas fa-building"></i> {{ $user->company?->name ?? 'No company' }}</span>
            </div>
        </div>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('admin.clients.edit', $user) }}" class="action-btn" style="width: auto; padding: 0 1rem; font-weight: 800; background: var(--primary-green); color: white; border-radius: 10px;"><i class="fas fa-pen"></i> Edit</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="info-card" style="text-align: center;">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Lifetime Value</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ usd($lifetimeValue) }}</div>
    </div>
    <div class="info-card" style="text-align: center;">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Shipments</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ $shipments->count() }}</div>
    </div>
    <div class="info-card" style="text-align: center;">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Deals</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ $user->deals->count() }}</div>
    </div>
    <div class="info-card" style="text-align: center;">
        <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 0.5rem;">Support Tickets</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);">{{ $user->tickets->count() }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <div class="info-card" style="margin-bottom: 2rem;">
            <h2 class="section-title"><i class="fas fa-clock-rotate-left" style="color: #8b5cf6;"></i> Activity Timeline</h2>
            <div style="padding-left: 0.5rem;">
                @forelse($activities as $activity)
                <div class="timeline-item">
                    <div class="timeline-dot" style="border-color: {{ $activity['color'] }};"></div>
                    <div class="timeline-date">{{ $activity['date']->diffForHumans() }}</div>
                    <div class="timeline-title">{{ $activity['title'] }}</div>
                    <div class="timeline-desc">{{ $activity['description'] }}</div>
                </div>
                @empty
                <p style="color: var(--text-gray); text-align: center; padding: 2rem 0;">No activity recorded yet.</p>
                @endforelse
            </div>
        </div>

        <div class="info-card">
            <h2 class="section-title"><i class="fas fa-sticky-note" style="color: #f59e0b;"></i> Notes & Preferences</h2>
            @forelse($notes as $note)
            <div style="background: #f8fafc; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.65rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">{{ strtoupper($note->type) }}</span>
                    <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 700;">{{ $note->created_at->diffForHumans() }} by {{ $note->creator->name }}</span>
                </div>
                <div style="font-size: 0.9rem; color: #475569; font-weight: 600; line-height: 1.5;">{{ $note->content }}</div>
            </div>
            @empty
            <p style="color: var(--text-gray); text-align: center; padding: 1rem 0;">No notes yet.</p>
            @endforelse
            {{ $notes->links() }}

            <form action="{{ route('admin.crm.contacts.notes.store', $user) }}" method="POST" style="margin-top: 1.5rem;">
                @csrf
                <div style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <select name="type" required style="padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.85rem; outline: none; background: white;">
                        <option value="note">Note</option>
                        <option value="call">Call</option>
                        <option value="email">Email</option>
                        <option value="meeting">Meeting</option>
                        <option value="purchase">Purchase</option>
                        <option value="preference">Preference</option>
                    </select>
                    <input type="text" name="metadata" placeholder='{"key":"value"} (optional)' style="flex-grow: 1; padding: 0.6rem 1rem; border: 2px solid #f1f5f9; border-radius: 10px; font-size: 0.85rem; outline: none;">
                </div>
                <textarea name="content" required rows="3" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f1f5f9; border-radius: 12px; font-size: 0.9rem; outline: none; resize: none; margin-bottom: 0.75rem;" placeholder="Add a note, log a call, or record a preference..."></textarea>
                <button type="submit" style="padding: 0.6rem 1.25rem; border-radius: 10px; background: var(--primary-green); color: white; border: none; font-weight: 800; cursor: pointer; font-size: 0.9rem;"><i class="fas fa-plus"></i> Add Note</button>
            </form>
        </div>
    </div>

    <div>
        <div class="info-card" style="margin-bottom: 2rem;">
            <h2 class="section-title"><i class="fas fa-address-card" style="color: #3b82f6;"></i> Contact Details</h2>
            <div class="detail-item">
                <div class="detail-label">Email</div>
                <div class="detail-value">{{ $user->email ?: 'No email on file' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Phone</div>
                <div class="detail-value">{{ $user->phone ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Company</div>
                <div class="detail-value">{{ $user->company?->name ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">CRM Status</div>
                <div class="detail-value">{{ ucfirst($user->crm_status) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Lead Score</div>
                <div class="detail-value">{{ $user->lead_score }}/100</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Last Engagement</div>
                <div class="detail-value">{{ $user->last_engagement_at?->diffForHumans() ?? 'Never' }}</div>
            </div>
        </div>

        <div class="info-card" style="margin-bottom: 2rem;">
            <h2 class="section-title"><i class="fas fa-heart" style="color: #ef4444;"></i> Preferences</h2>
            @if(!empty($preferences))
                @foreach($preferences as $key => $value)
                <div class="detail-item">
                    <div class="detail-label">{{ ucfirst($key) }}</div>
                    <div class="detail-value">{{ is_array($value) ? implode(', ', $value) : $value }}</div>
                </div>
                @endforeach
            @else
            <p style="color: var(--text-gray); font-size: 0.9rem;">No preferences recorded.</p>
            @endif
        </div>

        <div class="info-card">
            <h2 class="section-title"><i class="fas fa-building-user" style="color: #22c55e;"></i> Linked Companies</h2>
            @forelse($user->companies as $comp)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <div>
                    <div style="font-weight: 800; font-size: 0.9rem;">{{ $comp->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-gray);">{{ $comp->pivot->role }}</div>
                </div>
                <a href="{{ route('admin.crm.companies.show', $comp) }}" class="action-btn"><i class="fas fa-eye"></i></a>
            </div>
            @empty
            <p style="color: var(--text-gray); font-size: 0.9rem;">Not linked to any companies.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
