@props(['data' => [], 'series' => []])

<x-chart::base component="sunburst" :option="\WireCharts\Support\Option::sunburst([
    'data' => $data, 'series' => $series,
])" {{ $attributes }} />
