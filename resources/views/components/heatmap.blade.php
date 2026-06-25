@props(['series' => [], 'categories' => [], 'rows' => [], 'max' => 10])

<x-chart::base component="heatmap" :option="\WireCharts\Support\Option::heatmap([
    'series' => $series, 'categories' => $categories, 'rows' => $rows, 'max' => $max,
])" {{ $attributes }} />
