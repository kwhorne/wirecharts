@props(['series' => [], 'labels' => [], 'legend' => true])

<x-chart::base component="donut" :option="\WireCharts\Support\Option::pie([
    'series' => $series, 'labels' => $labels, 'legend' => $legend,
], true)" {{ $attributes }} />
