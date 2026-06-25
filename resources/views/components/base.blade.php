@props([
    'option' => [],
    'height' => 320,
    'theme' => null,
    'model' => null,
    'component' => null,
])

@php
    $resolvedTheme = $theme ?? config('wirecharts.theme', 'auto');
    $resolvedHeight = is_numeric($height) ? $height.'px' : $height;

    $licensed = $component === null
        || app(\WireCharts\Licensing\License::class)->allows($component);
@endphp

@if (! $licensed)
    <x-chart::_locked :component="$component" :height="$height" {{ $attributes }} />
@else
    <div
        {{ $attributes->merge(['class' => 'wirecharts']) }}
        wire:ignore
        x-data="wireChart(@js($option), @js($resolvedTheme), @js($model))"
        x-init="render()"
    >
        <div x-ref="canvas" style="width: 100%; height: {{ $resolvedHeight }};"></div>
    </div>
@endif
