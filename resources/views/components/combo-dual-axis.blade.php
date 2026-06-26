@props(['series' => [], 'categories' => [], 'axisNames' => ['', ''], 'legend' => true])

<x-chart::base component="combo-dual-axis" :option="\WireCharts\Support\Option::comboDualAxis([
    'series' => $series, 'categories' => $categories, 'axisNames' => $axisNames, 'legend' => $legend,
])" {{ $attributes }} />
