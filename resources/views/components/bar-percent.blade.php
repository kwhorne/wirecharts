@props(['series' => [], 'categories' => [], 'legend' => true])

<x-chart::base component="bar-percent" :option="\WireCharts\Support\Option::barPercent([
    'series' => $series, 'categories' => $categories, 'legend' => $legend,
])" {{ $attributes }} />
