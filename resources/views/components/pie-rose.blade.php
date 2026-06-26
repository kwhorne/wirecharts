@props(['series' => [], 'labels' => [], 'legend' => true])

<x-chart::base component="pie-rose" :option="\WireCharts\Support\Option::pieRose([
    'series' => $series, 'labels' => $labels, 'legend' => $legend,
])" {{ $attributes }} />
