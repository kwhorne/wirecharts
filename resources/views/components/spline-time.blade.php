@props([
    'series' => [],
    'zoom' => true,
    'legend' => true,
    'symbolSize' => 6,
])

<x-chart::base component="spline-time" :option="\WireCharts\Support\Option::splineTime([
    'series' => $series, 'zoom' => $zoom, 'legend' => $legend, 'symbolSize' => $symbolSize,
])" {{ $attributes }} />
