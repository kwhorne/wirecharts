@props(['series' => [], 'categories' => [], 'legend' => true])

<x-chart::base component="bar-stacked" :option="\WireCharts\Support\Option::barStacked([
    'series' => $series, 'categories' => $categories, 'legend' => $legend,
])" {{ $attributes }} />
