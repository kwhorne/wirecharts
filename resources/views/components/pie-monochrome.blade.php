@props(['series' => [], 'labels' => [], 'legend' => true])

<x-chart::base component="pie-monochrome" :option="\WireCharts\Support\Option::pieMonochrome([
    'series' => $series, 'labels' => $labels, 'legend' => $legend,
])" {{ $attributes }} />
