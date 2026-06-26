@props(['series' => [], 'categories' => [], 'legend' => true])

<x-chart::base component="column-percent" :option="\WireCharts\Support\Option::columnPercent([
    'series' => $series, 'categories' => $categories, 'legend' => $legend,
])" {{ $attributes }} />
