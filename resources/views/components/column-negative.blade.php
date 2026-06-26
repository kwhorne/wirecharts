@props(['series' => [], 'categories' => [], 'legend' => false, 'positiveColor' => '#22c55e', 'negativeColor' => '#ef4444'])

<x-chart::base component="column-negative" :option="\WireCharts\Support\Option::columnNegative([
    'series' => $series, 'categories' => $categories, 'legend' => $legend,
    'positiveColor' => $positiveColor, 'negativeColor' => $negativeColor,
])" {{ $attributes }} />
