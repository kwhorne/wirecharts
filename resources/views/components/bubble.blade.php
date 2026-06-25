@props(['series' => [], 'legend' => true])

<x-chart::base component="bubble" :option="\WireCharts\Support\Option::scatter([
    'series' => $series, 'legend' => $legend,
], true)" {{ $attributes }} />
