@props([
    'series' => [],
    'categories' => [],
    'annotations' => [],
    'smooth' => true,
    'legend' => true,
    'average' => false,
])

<x-chart::base component="line-annotated" :option="\WireCharts\Support\Option::lineAnnotated([
    'series' => $series, 'categories' => $categories, 'annotations' => $annotations,
    'smooth' => $smooth, 'legend' => $legend, 'average' => $average,
])" {{ $attributes }} />
