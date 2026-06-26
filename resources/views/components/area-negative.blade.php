@props([
    'series' => [],
    'categories' => [],
    'smooth' => true,
    'legend' => false,
    'positiveColor' => '#22c55e',
    'negativeColor' => '#ef4444',
])

<x-chart::base component="area-negative" :option="\WireCharts\Support\Option::areaNegative([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth, 'legend' => $legend,
    'positiveColor' => $positiveColor, 'negativeColor' => $negativeColor,
])" {{ $attributes }} />
