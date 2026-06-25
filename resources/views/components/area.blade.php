@props(['series' => [], 'categories' => [], 'smooth' => false, 'stack' => false, 'legend' => true])

<x-chart::base component="area" :option="\WireCharts\Support\Option::cartesian('area', [
    'series' => $series, 'categories' => $categories,
    'smooth' => $smooth, 'stack' => $stack, 'legend' => $legend,
])" {{ $attributes }} />
