@props([
    'series' => [],
    'categories' => [],
    'sampling' => 'lttb',
    'zoom' => true,
    'legend' => true,
])

<x-chart::base component="line-boost" :option="\WireCharts\Support\Option::lineBoost([
    'series' => $series, 'categories' => $categories, 'sampling' => $sampling,
    'zoom' => $zoom, 'legend' => $legend,
])" {{ $attributes }} />
