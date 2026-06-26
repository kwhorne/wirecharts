@props(['series' => [], 'labels' => [], 'legend' => true])

<x-chart::base component="pie-variable" :option="\WireCharts\Support\Option::pieVariable([
    'series' => $series, 'labels' => $labels, 'legend' => $legend,
])" {{ $attributes }} />
