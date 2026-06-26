@props(['categories' => [], 'low' => [], 'high' => [], 'name' => 'Range'])

<x-chart::base component="bar-range" :option="\WireCharts\Support\Option::barRange([
    'categories' => $categories, 'low' => $low, 'high' => $high, 'name' => $name,
])" {{ $attributes }} />
