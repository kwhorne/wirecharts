@props([
    'categories' => [],
    'low' => [],
    'high' => [],
    'line' => [],
    'rangeName' => 'Range',
    'lineName' => 'Average',
    'smooth' => true,
])

<x-chart::base component="area-range-line" :option="\WireCharts\Support\Option::areaRangeLine([
    'categories' => $categories, 'low' => $low, 'high' => $high, 'line' => $line,
    'rangeName' => $rangeName, 'lineName' => $lineName, 'smooth' => $smooth,
])" {{ $attributes }} />
