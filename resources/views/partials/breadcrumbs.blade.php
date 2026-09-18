@php
// $crumbs: array of ['label' => string, 'url' => string|null] — last item should have url => null (current page)
$crumbs = $crumbs ?? [];
$allCrumbs = array_merge([['label' => 'Home', 'url' => route('home')]], $crumbs);
@endphp
<nav aria-label="Breadcrumb" style="padding: 1rem 0; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <ol style="display: flex; flex-wrap: wrap; align-items: center; gap: .4rem; list-style: none; margin: 0; padding: 0; font-size: .85rem;">
            @foreach($allCrumbs as $i => $crumb)
                <li style="display: flex; align-items: center; gap: .4rem;">
                    @if($crumb['url'] && !$loop->last)
                        <a href="{{ $crumb['url'] }}" style="color: #64748b; text-decoration: none; font-weight: 600;">{{ $crumb['label'] }}</a>
                        <i class="fas fa-chevron-right" style="font-size: .65rem; color: #cbd5e1;" aria-hidden="true"></i>
                    @else
                        <span style="color: #1e293b; font-weight: 700;" aria-current="page">{{ $crumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        @foreach($allCrumbs as $i => $crumb)
        {
            "@type": "ListItem",
            "position": {{ $i + 1 }},
            "name": {!! json_encode($crumb['label']) !!}
            @if($crumb['url']), "item": {!! json_encode($crumb['url']) !!}@endif
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
