@props([
    'categories' => [],
    'line' => [],
    'bands' => [],
    'lineName' => 'Median',
    'smooth' => true,
    'color' => [249, 115, 22],
])

<x-chart::base component="area-fan" :option="\WireCharts\Support\Option::areaFan([
    'categories' => $categories, 'line' => $line, 'bands' => $bands,
    'lineName' => $lineName, 'smooth' => $smooth, 'color' => $color,
])" {{ $attributes }} />
