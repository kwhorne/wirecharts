@props([
    'categories' => [],
    'low' => [],
    'high' => [],
    'data' => [],
    'name' => 'Range',
    'smooth' => true,
])

<x-chart::base component="area-range" :option="\WireCharts\Support\Option::areaRange([
    'categories' => $categories, 'low' => $low, 'high' => $high,
    'data' => $data, 'name' => $name, 'smooth' => $smooth,
])" {{ $attributes }} />
