@props(['nodes' => [], 'categories' => []])

<x-chart::base component="packed-bubble" :option="\WireCharts\Support\Option::packedBubble([
    'nodes' => $nodes, 'categories' => $categories,
])" {{ $attributes }} />
