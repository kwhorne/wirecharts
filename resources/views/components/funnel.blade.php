@props(['series' => [], 'labels' => [], 'legend' => true])

<x-chart::base component="funnel" :option="\WireCharts\Support\Option::funnel([
    'series' => $series, 'labels' => $labels, 'legend' => $legend,
])" {{ $attributes }} />
