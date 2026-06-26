@props([
    'series' => [],
    'categories' => [],
    'smooth' => false,
    'legend' => true,
])

<x-chart::base component="area-stacked" :option="\WireCharts\Support\Option::areaStacked([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth, 'legend' => $legend,
])" {{ $attributes }} />
