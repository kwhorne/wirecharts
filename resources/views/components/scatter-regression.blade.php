@props(['series' => [], 'legend' => true])

<x-chart::base component="scatter-regression" :option="\WireCharts\Support\Option::scatterRegression([
    'series' => $series, 'legend' => $legend,
])" {{ $attributes }} />
