@props(['series' => [], 'indicators' => [], 'legend' => true])

<x-chart::base component="radar" :option="\WireCharts\Support\Option::radar([
    'series' => $series, 'indicators' => $indicators, 'legend' => $legend,
])" {{ $attributes }} />
