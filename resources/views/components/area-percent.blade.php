@props([
    'series' => [],
    'categories' => [],
    'smooth' => false,
    'legend' => true,
])

<x-chart::base component="area-percent" :option="\WireCharts\Support\Option::areaPercent([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth, 'legend' => $legend,
])" {{ $attributes }} />
