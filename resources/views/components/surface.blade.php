@props(['series' => [], 'data' => [], 'rotate' => false])

<x-chart::base component="surface" :option="\WireCharts\Support\Option::surface([
    'series' => $series, 'data' => $data, 'rotate' => $rotate,
])" {{ $attributes }} />
