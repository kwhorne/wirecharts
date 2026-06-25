@props([
    'series' => [],
    'categories' => [],
    'track' => 0,
    'duration' => 5000,
    'instrument' => 'sine',
    'minFreq' => 220,
    'maxFreq' => 880,
    'height' => 320,
    'theme' => null,
    'label' => 'Play chart audio',
])

@php
    $resolvedTheme = $theme ?? config('wirecharts.theme', 'auto');
    $resolvedHeight = is_numeric($height) ? $height.'px' : $height;

    $option = \WireCharts\Support\Option::cartesian('line', [
        'series' => $series,
        'categories' => $categories,
        'smooth' => true,
        'legend' => true,
    ]);

    $audio = [
        'track' => (int) $track,
        'duration' => (int) $duration,
        'instrument' => $instrument,
        'minFreq' => (int) $minFreq,
        'maxFreq' => (int) $maxFreq,
    ];

    $licensed = app(\WireCharts\Licensing\License::class)->allows('audio');
@endphp

@unless ($licensed)
    <x-chart::_locked component="audio" :height="$height" {{ $attributes }} />
@else
<div
    {{ $attributes->merge(['class' => 'wirecharts']) }}
    wire:ignore
    x-data="wireChartAudio(@js($option), @js($audio), @js($resolvedTheme))"
    x-init="render()"
>
    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
        <button
            type="button"
            @click="toggle()"
            :aria-pressed="playing"
            :aria-label="playing ? 'Stop audio' : @js($label)"
            style="display:inline-flex;align-items:center;gap:.375rem;padding:.375rem .75rem;border-radius:.5rem;border:1px solid rgb(228 228 231);background:transparent;font:500 13px/1 system-ui;cursor:pointer"
        >
            <span aria-hidden="true" x-text="playing ? '\u25A0' : '\u25B6'"></span>
            <span x-text="playing ? 'Playing…' : @js($label)"></span>
        </button>
    </div>

    <div x-ref="canvas" role="img" :aria-label="summary()" style="width:100%;height:{{ $resolvedHeight }};"></div>
</div>
@endunless
