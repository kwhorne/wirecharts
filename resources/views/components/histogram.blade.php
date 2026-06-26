@props(['data' => [], 'bins' => 10])

<x-chart::base component="histogram" :option="\WireCharts\Support\Option::histogram([
    'data' => $data, 'bins' => $bins,
])" {{ $attributes }} />
