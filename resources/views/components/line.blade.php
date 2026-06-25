@props(['series' => [], 'categories' => [], 'smooth' => false, 'step' => false, 'stack' => false, 'legend' => true])

<x-chart::base component="line" :option="\WireCharts\Support\Option::cartesian('line', [
    'series' => $series, 'categories' => $categories,
    'smooth' => $smooth, 'step' => $step, 'stack' => $stack, 'legend' => $legend,
])" {{ $attributes }} />
