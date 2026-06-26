@props(['series' => [], 'legend' => true])

<x-chart::base component="scatter-symbols" :option="\WireCharts\Support\Option::scatterSymbols([
    'series' => $series, 'legend' => $legend,
])" {{ $attributes }} />
