@props([
    'categories' => [],
    'actual' => [],
    'forecast' => [],
    'name' => 'Actual',
    'forecastName' => 'Forecast',
    'smooth' => true,
])

<x-chart::base component="line-forecast" :option="\WireCharts\Support\Option::lineForecast([
    'categories' => $categories, 'actual' => $actual, 'forecast' => $forecast,
    'name' => $name, 'forecastName' => $forecastName, 'smooth' => $smooth,
])" {{ $attributes }} />
