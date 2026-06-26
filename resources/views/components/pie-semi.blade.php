@props(['series' => [], 'labels' => [], 'legend' => true])

<x-chart::base component="pie-semi" :option="\WireCharts\Support\Option::pieSemi([
    'series' => $series, 'labels' => $labels, 'legend' => $legend,
])" {{ $attributes }} />
