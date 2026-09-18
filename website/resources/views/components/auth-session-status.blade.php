@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium animate-fade-in flex items-center gap-3']) }}>
        <i class="fas fa-info-circle text-emerald-500"></i>
        {{ $status }}
    </div>
@endif
