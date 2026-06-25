@props(['series' => [], 'categories' => []])

<x-chart::base component="boxplot" :option="\WireCharts\Support\Option::boxplot([
    'series' => $series, 'categories' => $categories,
])" {{ $attributes }} />
