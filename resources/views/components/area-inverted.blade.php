@props([
    'series' => [],
    'categories' => [],
    'smooth' => true,
    'legend' => true,
])

<x-chart::base component="area-inverted" :option="\WireCharts\Support\Option::areaInverted([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth, 'legend' => $legend,
])" {{ $attributes }} />
