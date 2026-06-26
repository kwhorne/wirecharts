@props([
    'series' => [],
    'categories' => [],
    'legend' => true,
])

<x-chart::base component="streamgraph" :option="\WireCharts\Support\Option::streamgraph([
    'series' => $series, 'categories' => $categories, 'legend' => $legend,
])" {{ $attributes }} />
