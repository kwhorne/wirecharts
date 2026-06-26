@props([
    'series' => [],
    'categories' => [],
    'smooth' => true,
    'legend' => true,
    'duration' => 1200,
    'easing' => 'cubicOut',
])

<x-chart::base component="line-animated" :option="\WireCharts\Support\Option::lineAnimated([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth,
    'legend' => $legend, 'duration' => $duration, 'easing' => $easing,
])" {{ $attributes }} />
