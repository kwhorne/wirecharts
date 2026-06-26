@props(['series' => [], 'labels' => [], 'legend' => true])

<x-chart::base component="pie-labels" :option="\WireCharts\Support\Option::pieLabels([
    'series' => $series, 'labels' => $labels, 'legend' => $legend,
])" {{ $attributes }} />
