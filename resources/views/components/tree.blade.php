@props(['data' => []])

<x-chart::base component="tree" :option="\WireCharts\Support\Option::tree(['data' => $data])" {{ $attributes }} />
