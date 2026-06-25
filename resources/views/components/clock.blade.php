@props([
    'height' => 320,
    'theme' => null,
    'color' => null,
    'options' => [],
])

@php
    $resolvedTheme = $theme ?? config('wirecharts.theme', 'auto');
    $resolvedHeight = is_numeric($height) ? $height.'px' : $height;

    $option = \WireCharts\Support\Option::clock([
        'color' => $color,
        'options' => $options,
    ]);

    $licensed = app(\WireCharts\Licensing\License::class)->allows('clock');
@endphp

@unless ($licensed)
    <x-chart::_locked component="clock" :height="$height" {{ $attributes }} />
@else
    <div
        {{ $attributes->merge(['class' => 'wirecharts']) }}
        wire:ignore
        x-data="wireChartClock(@js($option), @js($resolvedTheme))"
        x-init="render()"
    >
        <div x-ref="canvas" style="width: 100%; height: {{ $resolvedHeight }};"></div>
    </div>
@endunless
