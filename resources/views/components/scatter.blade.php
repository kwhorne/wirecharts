@props(['series' => [], 'legend' => true])

<x-chart::base component="scatter" :option="\WireCharts\Support\Option::scatter([
    'series' => $series, 'legend' => $legend,
], false)" {{ $attributes }} />
