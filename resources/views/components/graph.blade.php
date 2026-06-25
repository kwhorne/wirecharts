@props(['nodes' => [], 'links' => [], 'categories' => [], 'layout' => 'force', 'legend' => false])

<x-chart::base component="graph" :option="\WireCharts\Support\Option::graph([
    'nodes' => $nodes, 'links' => $links, 'categories' => $categories,
    'layout' => $layout, 'legend' => $legend,
])" {{ $attributes }} />
