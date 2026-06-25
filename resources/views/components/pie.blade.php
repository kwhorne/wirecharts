@props(['series' => [], 'labels' => [], 'legend' => true, 'rose' => false])

<x-chart::base component="pie" :option="\WireCharts\Support\Option::pie([
    'series' => $series, 'labels' => $labels, 'legend' => $legend, 'rose' => $rose,
], false)" {{ $attributes }} />
