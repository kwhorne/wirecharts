@props(['series' => [], 'xType' => 'value', 'yType' => 'value', 'rotate' => false])

<x-chart::base component="scatter3d" :option="\WireCharts\Support\Option::scatter3d([
    'series' => $series, 'xType' => $xType, 'yType' => $yType, 'rotate' => $rotate,
])" {{ $attributes }} />
