@props([
    'series' => [],
    'smooth' => false,
    'zoom' => true,
    'legend' => true,
])

<x-chart::base component="line-time" :option="\WireCharts\Support\Option::lineTime([
    'series' => $series, 'smooth' => $smooth, 'zoom' => $zoom, 'legend' => $legend,
])" {{ $attributes }} />
