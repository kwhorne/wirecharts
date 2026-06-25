@props(['series' => [], 'categories' => []])

<x-chart::base component="candlestick" :option="\WireCharts\Support\Option::candlestick([
    'series' => $series, 'categories' => $categories,
])" {{ $attributes }} />
