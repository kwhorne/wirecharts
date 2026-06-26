@props(['series' => [], 'labels' => [], 'legend' => true])

<x-chart::base component="pie-gradient" :option="\WireCharts\Support\Option::pieGradient([
    'series' => $series, 'labels' => $labels, 'legend' => $legend,
])" {{ $attributes }} />
