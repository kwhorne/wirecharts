@props(['categories' => [], 'low' => [], 'high' => [], 'name' => 'Range'])

<x-chart::base component="column-range" :option="\WireCharts\Support\Option::columnRange([
    'categories' => $categories, 'low' => $low, 'high' => $high, 'name' => $name,
])" {{ $attributes }} />
