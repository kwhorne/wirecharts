@props(['value' => 0, 'min' => 0, 'max' => 100, 'name' => '', 'progress' => false])

<x-chart::base component="gauge" :option="\WireCharts\Support\Option::gauge([
    'value' => $value, 'min' => $min, 'max' => $max, 'name' => $name, 'progress' => $progress,
])" {{ $attributes }} />
