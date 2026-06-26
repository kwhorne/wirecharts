@props(['series' => [], 'categories' => [], 'legend' => true, 'symbolSize' => 8])

<x-chart::base component="spline" :option="\WireCharts\Support\Option::spline([
    'series' => $series, 'categories' => $categories, 'legend' => $legend, 'symbolSize' => $symbolSize,
])" {{ $attributes }} />
