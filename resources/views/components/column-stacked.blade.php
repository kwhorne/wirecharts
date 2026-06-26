@props(['series' => [], 'categories' => [], 'legend' => true])

<x-chart::base component="column-stacked" :option="\WireCharts\Support\Option::columnStacked([
    'series' => $series, 'categories' => $categories, 'legend' => $legend,
])" {{ $attributes }} />
