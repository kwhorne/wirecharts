@props(['series' => [], 'categories' => [], 'smooth' => false])

<x-chart::base component="line-labels" :option="\WireCharts\Support\Option::lineLabels([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth,
])" {{ $attributes }} />
