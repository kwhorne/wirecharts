@props(['data' => [], 'series' => []])

<x-chart::base component="treemap" :option="\WireCharts\Support\Option::treemap([
    'data' => $data, 'series' => $series,
])" {{ $attributes }} />
