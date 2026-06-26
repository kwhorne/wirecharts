@props([
    'series' => [],
    'categories' => [],
    'smooth' => true,
    'stack' => false,
    'legend' => true,
])

<x-chart::base component="area-gradient" :option="\WireCharts\Support\Option::areaGradient([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth,
    'stack' => $stack, 'legend' => $legend,
])" {{ $attributes }} />
