@props(['series' => [], 'categories' => []])

<x-chart::base component="lollipop" :option="\WireCharts\Support\Option::lollipop([
    'series' => $series, 'categories' => $categories,
])" {{ $attributes }} />
