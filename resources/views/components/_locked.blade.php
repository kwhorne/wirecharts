@props([
    'component' => '',
    'height' => 320,
])

@php
    $resolvedHeight = is_numeric($height) ? $height.'px' : $height;
    $purchaseUrl = config('wirecharts.purchase_url', 'https://wirecharts.io/pro');
@endphp

<div
    {{ $attributes->merge(['class' => 'wirecharts wirecharts-locked']) }}
    role="img"
    aria-label="{{ $component ? ucfirst($component).' chart' : 'Chart' }} — WireCharts Pro, license required"
    style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;width:100%;height:{{ $resolvedHeight }};border:1px dashed rgb(212 212 216);border-radius:.75rem;color:rgb(113 113 122);font:500 13px/1.4 system-ui;text-align:center;padding:1rem"
>
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <rect x="3" y="11" width="18" height="11" rx="2"></rect>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
    </svg>
    <div><strong>WireCharts Pro</strong></div>
    <div>The <code>{{ $component ?: 'this' }}</code> chart requires a Pro license.</div>
    <a href="{{ $purchaseUrl }}" style="color:rgb(79 70 229);text-decoration:underline">Get a license &rarr;</a>
</div>
