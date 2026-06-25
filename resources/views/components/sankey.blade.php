@props(['nodes' => [], 'links' => []])

<x-chart::base component="sankey" :option="\WireCharts\Support\Option::sankey([
    'nodes' => $nodes, 'links' => $links,
])" {{ $attributes }} />
