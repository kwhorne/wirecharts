@props(['series' => [], 'categories' => [], 'legend' => true])

<x-chart::base component="combo-line-column" :option="\WireCharts\Support\Option::comboLineColumn([
    'series' => $series, 'categories' => $categories, 'legend' => $legend,
])" {{ $attributes }} />
