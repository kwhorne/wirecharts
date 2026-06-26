@props(['categories' => [], 'data' => [], 'name' => 'Count'])

<x-chart::base component="pareto" :option="\WireCharts\Support\Option::pareto([
    'categories' => $categories, 'data' => $data, 'name' => $name,
])" {{ $attributes }} />
