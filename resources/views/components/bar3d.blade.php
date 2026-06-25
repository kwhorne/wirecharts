@props(['series' => [], 'categories' => [], 'rows' => [], 'max' => null, 'rotate' => false])

<x-chart::base component="bar3d" :option="\WireCharts\Support\Option::bar3d(array_filter([
    'series' => $series, 'categories' => $categories, 'rows' => $rows,
    'rotate' => $rotate,
    'visualMap' => $max ? ['max' => $max, 'calculable' => true, 'right' => 0, 'top' => 'center'] : null,
], fn ($v) => $v !== null))" {{ $attributes }} />
