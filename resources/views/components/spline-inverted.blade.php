@props(['series' => [], 'categories' => [], 'legend' => true, 'symbolSize' => 6])

<x-chart::base component="spline-inverted" :option="\WireCharts\Support\Option::splineInverted([
    'series' => $series, 'categories' => $categories, 'legend' => $legend, 'symbolSize' => $symbolSize,
])" {{ $attributes }} />
