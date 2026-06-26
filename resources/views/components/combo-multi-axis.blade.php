@props(['series' => [], 'categories' => [], 'axes' => ['', '', ''], 'legend' => true])

<x-chart::base component="combo-multi-axis" :option="\WireCharts\Support\Option::comboMultiAxis([
    'series' => $series, 'categories' => $categories, 'axes' => $axes, 'legend' => $legend,
])" {{ $attributes }} />
