@props([
    'series' => [],
    'categories' => [],
    'smooth' => false,
    'legend' => true,
])

<x-chart::base component="area-stacked-inverted" :option="\WireCharts\Support\Option::areaStackedInverted([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth, 'legend' => $legend,
])" {{ $attributes }} />
