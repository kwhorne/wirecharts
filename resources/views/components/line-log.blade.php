@props(['series' => [], 'categories' => [], 'smooth' => false, 'legend' => true, 'logBase' => 10])

<x-chart::base component="line-log" :option="\WireCharts\Support\Option::lineLog([
    'series' => $series, 'categories' => $categories, 'smooth' => $smooth, 'legend' => $legend, 'logBase' => $logBase,
])" {{ $attributes }} />
