@props(['series' => [], 'categories' => [], 'stack' => false, 'legend' => true])

<x-chart::base component="column" :option="\WireCharts\Support\Option::cartesian('column', [
    'series' => $series, 'categories' => $categories, 'stack' => $stack, 'legend' => $legend,
])" {{ $attributes }} />
