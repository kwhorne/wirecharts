@props([
    'series' => [],
    'categories' => [],
    'smooth' => true,
    'interval' => 400,
    'height' => 320,
    'theme' => null,
    'label' => 'Replay',
])

@php
    $resolvedTheme = $theme ?? config('wirecharts.theme', 'auto');
    $resolvedHeight = is_numeric($height) ? $height.'px' : $height;

    $option = \WireCharts\Support\Option::lineRace([
        'series' => $series,
        'categories' => $categories,
        'smooth' => $smooth,
    ]);

    $config = ['interval' => (int) $interval];

    $licensed = app(\WireCharts\Licensing\License::class)->allows('line-race');
@endphp

@unless ($licensed)
    <x-chart::_locked component="line-race" :height="$height" {{ $attributes }} />
@else
    <div
        {{ $attributes->merge(['class' => 'wirecharts']) }}
        wire:ignore
        x-data="wireChartRace(@js($option), @js($resolvedTheme), @js($config))"
        x-init="render()"
    >
        <div style="display:flex;justify-content:flex-end;margin-bottom:.5rem">
            <button
                type="button"
                @click="play()"
                style="display:inline-flex;align-items:center;gap:.375rem;padding:.375rem .75rem;border-radius:.5rem;border:1px solid rgb(228 228 231);background:transparent;font:500 13px/1 system-ui;cursor:pointer"
            >
                <span aria-hidden="true">&#x21bb;</span>
                <span>{{ $label }}</span>
            </button>
        </div>

        <div x-ref="canvas" style="width: 100%; height: {{ $resolvedHeight }};"></div>
    </div>
@endunless
