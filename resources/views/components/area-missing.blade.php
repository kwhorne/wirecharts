@props([
    'series' => [],
    'categories' => [],
    'smooth' => true,
    'connectNulls' => false,
    'legend' => true,
])

<x-chart::base component="area-missing" :option="\WireCharts\Support\Option::areaMissing([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth,
    'connectNulls' => $connectNulls, 'legend' => $legend,
])" {{ $attributes }} />
