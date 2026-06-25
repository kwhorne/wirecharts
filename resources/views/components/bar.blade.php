@props(['series' => [], 'categories' => [], 'stack' => false, 'legend' => true])

<x-chart::base component="bar" :option="\WireCharts\Support\Option::cartesian('bar', [
    'series' => $series, 'categories' => $categories, 'stack' => $stack, 'legend' => $legend,
    'horizontal' => true,
])" {{ $attributes }} />
