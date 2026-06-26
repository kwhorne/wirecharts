@props([
    'series' => [],
    'categories' => [],
    'stack' => false,
    'legend' => true,
])

<x-chart::base component="areaspline" :option="\WireCharts\Support\Option::areaspline([
    'series' => $series, 'categories' => $categories, 'stack' => $stack, 'legend' => $legend,
])" {{ $attributes }} />
