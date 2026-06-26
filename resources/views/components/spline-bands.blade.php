@props([
    'series' => [],
    'categories' => [],
    'bands' => [],
    'smooth' => true,
    'legend' => true,
])

<x-chart::base component="spline-bands" :option="\WireCharts\Support\Option::splineBands([
    'series' => $series, 'categories' => $categories, 'bands' => $bands,
    'smooth' => $smooth, 'legend' => $legend,
])" {{ $attributes }} />
