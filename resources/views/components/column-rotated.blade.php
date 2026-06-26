@props(['series' => [], 'categories' => [], 'legend' => true, 'rotate' => 45])

<x-chart::base component="column-rotated" :option="\WireCharts\Support\Option::columnRotated([
    'series' => $series, 'categories' => $categories, 'legend' => $legend, 'rotate' => $rotate,
])" {{ $attributes }} />
