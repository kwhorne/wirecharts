<?php

namespace WireCharts\Support;

/**
 * Builds chart option arrays from friendly component props.
 * Each method returns a PHP array that is JSON-encoded into the chart.
 */
class Option
{
    /**
     * Cartesian charts: line, area, column (vertical bar), bar (horizontal).
     */
    public static function cartesian(string $type, array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $stack = $args['stack'] ?? false;
        $smooth = $args['smooth'] ?? false;
        $step = $args['step'] ?? false;
        $horizontal = $args['horizontal'] ?? false;
        $legend = $args['legend'] ?? true;

        $isArea = $type === 'area';
        $seriesType = $isArea ? 'line' : $type;

        $seriesOption = array_map(function ($s) use ($seriesType, $isArea, $smooth, $step, $stack) {
            $item = array_merge(['type' => $seriesType], $s);
            if ($isArea) {
                $item['areaStyle'] = (object) [];
            }
            if ($smooth) {
                $item['smooth'] = true;
            }
            if ($step !== false) {
                $item['step'] = is_string($step) ? $step : 'start';
            }
            if ($stack !== false) {
                $item['stack'] = is_string($stack) ? $stack : 'total';
            }

            return $item;
        }, $series);

        $catAxis = [
            'type' => 'category',
            'data' => $categories,
            'boundaryGap' => $type === 'column' || $type === 'bar',
        ];
        $valAxis = ['type' => 'value'];

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => $horizontal ? $valAxis : $catAxis,
            'yAxis' => $horizontal ? $catAxis : $valAxis,
            'series' => $seriesOption,
        ];
    }

    /**
     * Spline with symbols: a smooth line where each series is marked with a
     * distinct point symbol (circle, square, triangle, diamond, ...).
     */
    public static function spline(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $legend = $args['legend'] ?? true;
        $size = $args['symbolSize'] ?? 8;
        $symbols = ['circle', 'rect', 'triangle', 'diamond', 'roundRect', 'pin', 'arrow'];

        $seriesOption = [];
        foreach (array_values($series) as $i => $s) {
            $seriesOption[] = array_merge([
                'type' => 'line',
                'smooth' => true,
                'symbol' => $symbols[$i % count($symbols)],
                'symbolSize' => $size,
                'showSymbol' => true,
                'lineStyle' => ['width' => 3],
            ], $s);
        }

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Spline with inverted axes: a smooth line drawn with the category axis
     * running vertically and the value axis horizontally — ideal for profiles
     * such as temperature by altitude.
     */
    public static function splineInverted(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $legend = $args['legend'] ?? true;
        $size = $args['symbolSize'] ?? 6;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => true,
            'showSymbol' => true,
            'symbolSize' => $size,
            'lineStyle' => ['width' => 3],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'value'],
            'yAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'series' => $seriesOption,
        ];
    }

    /**
     * Line with end labels: each series is named at the end of its line instead
     * of using a legend — ideal for comparing several labelled trends.
     */
    public static function lineLabels(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? false;

        $formatter = '@@function (params) { return params.seriesName; }@@';

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => false,
            'lineStyle' => ['width' => 3],
            'emphasis' => ['focus' => 'series'],
            'endLabel' => [
                'show' => true,
                'formatter' => $formatter,
                'fontWeight' => 'bold',
            ],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => false],
            'grid' => ['left' => '3%', 'right' => 90, 'bottom' => '3%', 'top' => 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Line with a logarithmic y-axis — ideal when values span several orders
     * of magnitude.
     */
    public static function lineLog(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? false;
        $legend = $args['legend'] ?? true;
        $logBase = $args['logBase'] ?? 10;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => true,
            'symbolSize' => 6,
            'lineStyle' => ['width' => 3],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'log', 'logBase' => $logBase],
            'series' => $seriesOption,
        ];
    }

    /**
     * Line race: a multi-series line whose points are revealed progressively
     * (animated client-side), with each line labelled at its leading end.
     */
    public static function lineRace(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? true;

        $formatter = '@@function (params) { return params.seriesName; }@@';

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => false,
            'lineStyle' => ['width' => 3],
            'emphasis' => ['focus' => 'series'],
            'endLabel' => ['show' => true, 'formatter' => $formatter, 'fontWeight' => 'bold'],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => false],
            'grid' => ['left' => '3%', 'right' => 90, 'bottom' => '3%', 'top' => 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Line with a custom entrance animation: the line draws in with a chosen
     * easing and a staggered per-point delay.
     */
    public static function lineAnimated(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? true;
        $legend = $args['legend'] ?? true;
        $duration = $args['duration'] ?? 1200;
        $easing = $args['easing'] ?? 'cubicOut';

        $delay = '@@function (idx) { return idx * 60; }@@';

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => true,
            'symbolSize' => 7,
            'lineStyle' => ['width' => 3],
            'animationDuration' => $duration,
            'animationEasing' => $easing,
            'animationDelay' => $delay,
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'animationDuration' => $duration,
            'animationEasing' => $easing,
            'series' => $seriesOption,
        ];
    }

    /**
     * Line with a forecast: actual values are drawn solid, forecast values
     * continue as a dashed line over a lightly shaded region.
     */
    public static function lineForecast(array $args): array
    {
        $categories = $args['categories'] ?? [];
        $actual = array_values($args['actual'] ?? []);
        $forecast = array_values($args['forecast'] ?? []);
        $name = $args['name'] ?? 'Actual';
        $forecastName = $args['forecastName'] ?? 'Forecast';
        $smooth = $args['smooth'] ?? true;

        $actualCount = count($actual);

        $actualData = array_merge($actual, array_fill(0, count($forecast), null));

        $forecastData = array_merge(
            array_fill(0, max(0, $actualCount - 1), null),
            $actualCount ? [end($actual)] : [],
            $forecast,
        );

        $forecastSeries = [
            'name' => $forecastName,
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => false,
            'data' => $forecastData,
            'lineStyle' => ['width' => 3, 'type' => 'dashed'],
        ];

        if ($actualCount > 0 && ! empty($categories)) {
            $forecastSeries['markArea'] = [
                'silent' => true,
                'itemStyle' => ['color' => 'rgba(127,127,127,0.08)'],
                'data' => [[
                    ['xAxis' => $categories[$actualCount - 1] ?? null],
                    ['xAxis' => $categories[count($categories) - 1] ?? null],
                ]],
            ];
        }

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => true],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 40, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => [
                [
                    'name' => $name,
                    'type' => 'line',
                    'smooth' => $smooth,
                    'showSymbol' => false,
                    'data' => $actualData,
                    'lineStyle' => ['width' => 3],
                ],
                $forecastSeries,
            ],
        ];
    }

    /**
     * Line with annotations: labelled marker pins on chosen points, plus an
     * optional average reference line.
     */
    public static function lineAnnotated(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? true;
        $legend = $args['legend'] ?? true;
        $annotations = $args['annotations'] ?? [];
        $average = $args['average'] ?? false;

        $markPointData = array_map(fn ($a) => [
            'coord' => [$a['x'] ?? null, $a['y'] ?? null],
            'value' => $a['text'] ?? '',
        ], array_values($annotations));

        $seriesOption = [];
        foreach (array_values($series) as $i => $s) {
            $item = array_merge([
                'type' => 'line',
                'smooth' => $smooth,
                'showSymbol' => true,
                'symbolSize' => 6,
                'lineStyle' => ['width' => 3],
            ], $s);

            if ($i === 0 && $markPointData) {
                $item['markPoint'] = [
                    'symbol' => 'pin',
                    'symbolSize' => 48,
                    'label' => ['fontSize' => 11, 'color' => '#fff'],
                    'data' => $markPointData,
                ];
            }

            if ($i === 0 && $average) {
                $item['markLine'] = [
                    'data' => [['type' => 'average', 'name' => 'Average']],
                ];
            }

            $seriesOption[] = $item;
        }

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Line tuned for very large datasets: LTTB downsampling, large-mode
     * rendering, animation disabled, and zoom/pan controls.
     */
    public static function lineBoost(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $sampling = $args['sampling'] ?? 'lttb';
        $zoom = $args['zoom'] ?? true;
        $legend = $args['legend'] ?? true;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'showSymbol' => false,
            'sampling' => $sampling,
            'large' => true,
            'largeThreshold' => 2000,
            'lineStyle' => ['width' => 1.5],
        ], $s), array_values($series));

        $option = [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'animation' => false,
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => $zoom ? 60 : '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => empty($categories)
                ? ['type' => 'value', 'boundaryGap' => false]
                : ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];

        if ($zoom) {
            $option['dataZoom'] = [
                ['type' => 'inside'],
                ['type' => 'slider'],
            ];
        }

        return $option;
    }

    /**
     * Time series: a line over a datetime axis. Data points are [date, value]
     * pairs, with optional zoom/pan.
     */
    public static function lineTime(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $smooth = $args['smooth'] ?? false;
        $zoom = $args['zoom'] ?? true;
        $legend = $args['legend'] ?? true;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => false,
            'lineStyle' => ['width' => 2.5],
        ], $s), array_values($series));

        $option = [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => $zoom ? 60 : '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'time'],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];

        if ($zoom) {
            $option['dataZoom'] = [
                ['type' => 'inside'],
                ['type' => 'slider'],
            ];
        }

        return $option;
    }

    /**
     * Spline over a datetime axis with irregular intervals: points are placed
     * by their actual time and marked with symbols.
     */
    public static function splineTime(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $zoom = $args['zoom'] ?? true;
        $legend = $args['legend'] ?? true;
        $size = $args['symbolSize'] ?? 6;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => true,
            'showSymbol' => true,
            'symbolSize' => $size,
            'lineStyle' => ['width' => 3],
        ], $s), array_values($series));

        $option = [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => $zoom ? 60 : '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'time'],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];

        if ($zoom) {
            $option['dataZoom'] = [
                ['type' => 'inside'],
                ['type' => 'slider'],
            ];
        }

        return $option;
    }

    /**
     * Spline with horizontal plot bands: coloured background zones across the
     * y-axis (e.g. value ranges), each optionally labelled.
     */
    public static function splineBands(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? true;
        $legend = $args['legend'] ?? true;
        $bands = $args['bands'] ?? [];

        $bandData = array_map(function ($b) {
            $start = ['yAxis' => $b['from'] ?? 0];

            if (! empty($b['color'])) {
                $start['itemStyle'] = ['color' => $b['color']];
            }
            if (! empty($b['label'])) {
                $start['label'] = [
                    'show' => true,
                    'formatter' => $b['label'],
                    'position' => 'insideStartTop',
                    'color' => '#888',
                    'fontSize' => 11,
                ];
            }

            return [$start, ['yAxis' => $b['to'] ?? 0]];
        }, array_values($bands));

        $seriesOption = [];
        foreach (array_values($series) as $i => $s) {
            $item = array_merge([
                'type' => 'line',
                'smooth' => $smooth,
                'showSymbol' => false,
                'lineStyle' => ['width' => 3],
            ], $s);

            if ($i === 0 && $bandData) {
                $item['markArea'] = ['silent' => true, 'data' => $bandData];
            }

            $seriesOption[] = $item;
        }

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Area chart with smooth gradient fills — each series fades from its colour
     * to transparent. Optionally stacked.
     */
    public static function areaGradient(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? true;
        $stack = $args['stack'] ?? false;
        $legend = $args['legend'] ?? true;

        $palette = [[249, 115, 22], [14, 165, 233], [34, 197, 94], [168, 85, 247], [239, 68, 68], [234, 179, 8]];

        $seriesOption = [];
        foreach (array_values($series) as $i => $s) {
            [$r, $g, $b] = $palette[$i % count($palette)];

            $item = array_merge([
                'type' => 'line',
                'smooth' => $smooth,
                'showSymbol' => false,
                'lineStyle' => ['width' => 2, 'color' => "rgb({$r},{$g},{$b})"],
                'itemStyle' => ['color' => "rgb({$r},{$g},{$b})"],
                'areaStyle' => [
                    'opacity' => 1,
                    'color' => [
                        'type' => 'linear', 'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                        'colorStops' => [
                            ['offset' => 0, 'color' => "rgba({$r},{$g},{$b},0.45)"],
                            ['offset' => 1, 'color' => "rgba({$r},{$g},{$b},0.03)"],
                        ],
                    ],
                ],
            ], $s);

            if ($stack !== false) {
                $item['stack'] = is_string($stack) ? $stack : 'total';
            }

            $seriesOption[] = $item;
        }

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Stacked area chart: series are filled and stacked on top of one another
     * to show cumulative totals and composition.
     */
    public static function areaStacked(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? false;
        $legend = $args['legend'] ?? true;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'stack' => 'total',
            'smooth' => $smooth,
            'showSymbol' => false,
            'lineStyle' => ['width' => 1],
            'areaStyle' => ['opacity' => 0.8],
            'emphasis' => ['focus' => 'series'],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * 100% stacked area chart: each series is shown as its percentage share of
     * the total for every category.
     */
    public static function areaPercent(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? false;
        $legend = $args['legend'] ?? true;

        $values = array_map(fn ($s) => array_values($s['data'] ?? []), array_values($series));
        $count = $values ? max(array_map('count', $values)) : 0;

        for ($j = 0; $j < $count; $j++) {
            $sum = 0;
            foreach ($values as $row) {
                $sum += $row[$j] ?? 0;
            }
            foreach ($values as $i => $row) {
                $v = $row[$j] ?? 0;
                $values[$i][$j] = $sum > 0 ? round($v / $sum * 100, 2) : 0;
            }
        }

        $seriesOption = [];
        foreach (array_values($series) as $i => $s) {
            $seriesOption[] = [
                'name' => $s['name'] ?? '',
                'type' => 'line',
                'stack' => 'total',
                'smooth' => $smooth,
                'showSymbol' => false,
                'lineStyle' => ['width' => 1],
                'areaStyle' => ['opacity' => 0.8],
                'emphasis' => ['focus' => 'series'],
                'data' => $values[$i],
            ];
        }

        return [
            'tooltip' => ['trigger' => 'axis', 'valueFormatter' => '@@function (v) { return v + "%"; }@@'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value', 'max' => 100, 'axisLabel' => ['formatter' => '{value}%']],
            'series' => $seriesOption,
        ];
    }

    /**
     * Area range: a filled band between a low and high value per category
     * (e.g. min/max temperature). Accepts low/high arrays or [low, high] pairs.
     */
    public static function areaRange(array $args): array
    {
        $categories = $args['categories'] ?? [];
        $low = array_values($args['low'] ?? []);
        $high = array_values($args['high'] ?? []);
        $name = $args['name'] ?? 'Range';
        $smooth = $args['smooth'] ?? true;

        if (! $low && ! $high && ! empty($args['data'])) {
            foreach ($args['data'] as $pair) {
                $low[] = $pair[0] ?? null;
                $high[] = $pair[1] ?? null;
            }
        }

        $diff = [];
        foreach ($low as $i => $l) {
            $h = $high[$i] ?? $l;
            $diff[] = ($l === null || $h === null) ? null : $h - $l;
        }

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => false],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => [
                [
                    'name' => 'low',
                    'type' => 'line',
                    'stack' => 'range',
                    'smooth' => $smooth,
                    'showSymbol' => false,
                    'silent' => true,
                    'lineStyle' => ['opacity' => 0],
                    'areaStyle' => ['opacity' => 0],
                    'data' => $low,
                ],
                [
                    'name' => $name,
                    'type' => 'line',
                    'stack' => 'range',
                    'smooth' => $smooth,
                    'showSymbol' => false,
                    'lineStyle' => ['opacity' => 0],
                    'areaStyle' => ['opacity' => 0.4],
                    'data' => $diff,
                ],
            ],
        ];
    }

    /**
     * Area race: a multi-series filled area whose points are revealed
     * progressively (animated client-side), each labelled at its leading end.
     */
    public static function areaRace(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? true;

        $formatter = '@@function (params) { return params.seriesName; }@@';

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => false,
            'lineStyle' => ['width' => 2],
            'areaStyle' => ['opacity' => 0.4],
            'emphasis' => ['focus' => 'series'],
            'endLabel' => ['show' => true, 'formatter' => $formatter, 'fontWeight' => 'bold'],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => false],
            'grid' => ['left' => '3%', 'right' => 90, 'bottom' => '3%', 'top' => 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Area spline: a smooth filled area chart with semi-transparent overlapping
     * series. Optionally stacked.
     */
    public static function areaspline(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $stack = $args['stack'] ?? false;
        $legend = $args['legend'] ?? true;

        $seriesOption = array_map(function ($s) use ($stack) {
            $item = array_merge([
                'type' => 'line',
                'smooth' => true,
                'showSymbol' => false,
                'lineStyle' => ['width' => 2],
                'areaStyle' => ['opacity' => 0.4],
            ], $s);

            if ($stack !== false) {
                $item['stack'] = is_string($stack) ? $stack : 'total';
            }

            return $item;
        }, array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Area chart with inverted axes: filled areas drawn with the category axis
     * running vertically and the value axis horizontally.
     */
    public static function areaInverted(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? true;
        $legend = $args['legend'] ?? true;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => false,
            'lineStyle' => ['width' => 2],
            'areaStyle' => ['opacity' => 0.4],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'value'],
            'yAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'series' => $seriesOption,
        ];
    }

    /**
     * Area chart with negative values: the line and fill are coloured
     * differently above and below the zero line.
     */
    public static function areaNegative(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? true;
        $legend = $args['legend'] ?? false;
        $positive = $args['positiveColor'] ?? '#22c55e';
        $negative = $args['negativeColor'] ?? '#ef4444';

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => false,
            'lineStyle' => ['width' => 2],
            'areaStyle' => ['opacity' => 0.3],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'visualMap' => [
                'show' => false,
                'dimension' => 1,
                'pieces' => [
                    ['lte' => 0, 'color' => $negative],
                    ['gt' => 0, 'color' => $positive],
                ],
            ],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Area range with a line overlay: a filled low/high band plus a line
     * (e.g. the average) drawn on top.
     */
    public static function areaRangeLine(array $args): array
    {
        $categories = $args['categories'] ?? [];
        $low = array_values($args['low'] ?? []);
        $high = array_values($args['high'] ?? []);
        $line = array_values($args['line'] ?? []);
        $rangeName = $args['rangeName'] ?? 'Range';
        $lineName = $args['lineName'] ?? 'Average';
        $smooth = $args['smooth'] ?? true;

        $diff = [];
        foreach ($low as $i => $l) {
            $h = $high[$i] ?? $l;
            $diff[] = ($l === null || $h === null) ? null : $h - $l;
        }

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => true],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 40, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => [
                [
                    'name' => 'low',
                    'type' => 'line',
                    'stack' => 'range',
                    'smooth' => $smooth,
                    'showSymbol' => false,
                    'silent' => true,
                    'lineStyle' => ['opacity' => 0],
                    'areaStyle' => ['opacity' => 0],
                    'data' => $low,
                ],
                [
                    'name' => $rangeName,
                    'type' => 'line',
                    'stack' => 'range',
                    'smooth' => $smooth,
                    'showSymbol' => false,
                    'lineStyle' => ['opacity' => 0],
                    'areaStyle' => ['opacity' => 0.3],
                    'data' => $diff,
                ],
                [
                    'name' => $lineName,
                    'type' => 'line',
                    'smooth' => $smooth,
                    'showSymbol' => false,
                    'lineStyle' => ['width' => 3],
                    'data' => $line,
                ],
            ],
        ];
    }

    /**
     * Fan chart: a central line surrounded by nested confidence bands that fan
     * out with growing uncertainty. Each band has its own low/high arrays.
     */
    public static function areaFan(array $args): array
    {
        $categories = $args['categories'] ?? [];
        $line = array_values($args['line'] ?? []);
        $bands = $args['bands'] ?? [];
        $lineName = $args['lineName'] ?? 'Median';
        $smooth = $args['smooth'] ?? true;
        [$r, $g, $b] = $args['color'] ?? [249, 115, 22];

        $series = [];

        foreach (array_values($bands) as $k => $band) {
            $low = array_values($band['low'] ?? []);
            $high = array_values($band['high'] ?? []);
            $opacity = $band['opacity'] ?? (0.12 + $k * 0.12);

            $diff = [];
            foreach ($low as $i => $l) {
                $h = $high[$i] ?? $l;
                $diff[] = ($l === null || $h === null) ? null : $h - $l;
            }

            $stack = "fan{$k}";

            $series[] = [
                'name' => "fan-low-{$k}",
                'type' => 'line',
                'stack' => $stack,
                'smooth' => $smooth,
                'showSymbol' => false,
                'silent' => true,
                'lineStyle' => ['opacity' => 0],
                'areaStyle' => ['opacity' => 0],
                'data' => $low,
            ];
            $series[] = [
                'name' => $band['name'] ?? ('Band '.($k + 1)),
                'type' => 'line',
                'stack' => $stack,
                'smooth' => $smooth,
                'showSymbol' => false,
                'lineStyle' => ['opacity' => 0],
                'areaStyle' => ['color' => "rgba({$r},{$g},{$b},{$opacity})"],
                'data' => $diff,
            ];
        }

        $series[] = [
            'name' => $lineName,
            'type' => 'line',
            'smooth' => $smooth,
            'showSymbol' => false,
            'lineStyle' => ['width' => 2, 'color' => "rgb({$r},{$g},{$b})"],
            'data' => $line,
        ];

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => false],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $series,
        ];
    }

    /**
     * Streamgraph: a flowing stacked area displaced around a central baseline,
     * rendered with the themeRiver series.
     */
    public static function streamgraph(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $legend = $args['legend'] ?? true;

        $names = [];
        $data = [];
        foreach (array_values($series) as $s) {
            $name = $s['name'] ?? '';
            $names[] = $name;
            foreach (array_values($s['data'] ?? []) as $i => $v) {
                $data[] = [$categories[$i] ?? $i, $v, $name];
            }
        }

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'line']],
            'legend' => ['show' => $legend, 'data' => $names],
            'singleAxis' => [
                'type' => 'category',
                'data' => $categories,
                'boundaryGap' => false,
                'top' => 40,
                'bottom' => 30,
            ],
            'series' => [[
                'type' => 'themeRiver',
                'emphasis' => ['focus' => 'self'],
                'label' => ['show' => false],
                'data' => $data,
            ]],
        ];
    }

    /**
     * Stacked area chart with inverted axes: filled, stacked series drawn with
     * the category axis running vertically.
     */
    public static function areaStackedInverted(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? false;
        $legend = $args['legend'] ?? true;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'stack' => 'total',
            'smooth' => $smooth,
            'showSymbol' => false,
            'lineStyle' => ['width' => 1],
            'areaStyle' => ['opacity' => 0.8],
            'emphasis' => ['focus' => 'series'],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'value'],
            'yAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'series' => $seriesOption,
        ];
    }

    /**
     * Area chart with missing points: null values leave a gap, or are bridged
     * when connectNulls is enabled.
     */
    public static function areaMissing(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $smooth = $args['smooth'] ?? true;
        $connectNulls = $args['connectNulls'] ?? false;
        $legend = $args['legend'] ?? true;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'line',
            'smooth' => $smooth,
            'connectNulls' => $connectNulls,
            'showSymbol' => true,
            'symbolSize' => 5,
            'lineStyle' => ['width' => 2],
            'areaStyle' => ['opacity' => 0.4],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'boundaryGap' => false],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    // ---- Combinations -----------------------------------------------------

    /**
     * Convert a combo series item (with a friendly 'type') into an ECharts series.
     */
    protected static function comboItem(array $s): array
    {
        $type = $s['type'] ?? 'bar';
        unset($s['type'], $s['axis']);

        $item = array_merge(['type' => $type === 'area' ? 'line' : $type], $s);

        if ($type === 'area') {
            $item['areaStyle'] = (object) [];
            $item['smooth'] = true;
        }
        if ($type === 'line') {
            $item['smooth'] = $item['smooth'] ?? true;
            $item['symbolSize'] = 7;
            $item['lineStyle'] = ['width' => 3];
            $item['z'] = 3;
        }

        return $item;
    }

    /**
     * Combined line & column chart on a single axis.
     */
    public static function comboLineColumn(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $legend = $args['legend'] ?? true;

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'cross']],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 40, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories],
            'yAxis' => ['type' => 'value'],
            'series' => array_map(fn ($s) => static::comboItem($s), array_values($series)),
        ];
    }

    /**
     * Dual-axis combination: each series targets the left (0) or right (1) axis.
     */
    public static function comboDualAxis(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $legend = $args['legend'] ?? true;
        $axisNames = $args['axisNames'] ?? ['', ''];

        $seriesOption = array_map(function ($s) {
            $axis = $s['axis'] ?? 0;
            $item = static::comboItem($s);
            $item['yAxisIndex'] = $axis;

            return $item;
        }, array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'cross']],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 40, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories],
            'yAxis' => [
                ['type' => 'value', 'name' => $axisNames[0] ?? ''],
                ['type' => 'value', 'name' => $axisNames[1] ?? '', 'splitLine' => ['show' => false]],
            ],
            'series' => $seriesOption,
        ];
    }

    /**
     * Multiple-axis combination: each series targets one of several y-axes.
     */
    public static function comboMultiAxis(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $legend = $args['legend'] ?? true;
        $axes = $args['axes'] ?? ['', '', ''];

        $yAxis = [];
        foreach (array_values($axes) as $i => $name) {
            $yAxis[] = [
                'type' => 'value',
                'name' => $name,
                'position' => $i === 0 ? 'left' : 'right',
                'offset' => $i >= 2 ? ($i - 1) * 55 : 0,
                'splitLine' => ['show' => $i === 0],
            ];
        }

        $seriesOption = array_map(function ($s) {
            $axis = $s['axis'] ?? 0;
            $item = static::comboItem($s);
            $item['yAxisIndex'] = $axis;

            return $item;
        }, array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'cross']],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '5%', 'right' => 70, 'bottom' => '3%', 'top' => 40, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories],
            'yAxis' => $yAxis,
            'series' => $seriesOption,
        ];
    }

    // ---- Scatter & bubble -------------------------------------------------

    /**
     * Scatter plot with a least-squares linear regression (trend) line.
     */
    public static function scatterRegression(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $points = $series[0]['data'] ?? [];
        $name = $series[0]['name'] ?? 'Data';

        $n = count($points);
        $sx = $sy = $sxy = $sxx = 0;
        $xmin = INF;
        $xmax = -INF;
        foreach ($points as $p) {
            $x = $p[0] ?? 0;
            $y = $p[1] ?? 0;
            $sx += $x;
            $sy += $y;
            $sxy += $x * $y;
            $sxx += $x * $x;
            $xmin = min($xmin, $x);
            $xmax = max($xmax, $x);
        }

        $line = [];
        $denom = $n * $sxx - $sx * $sx;
        if ($n > 1 && $denom != 0) {
            $slope = ($n * $sxy - $sx * $sy) / $denom;
            $intercept = ($sy - $slope * $sx) / $n;
            $line = [
                [$xmin, round($intercept + $slope * $xmin, 2)],
                [$xmax, round($intercept + $slope * $xmax, 2)],
            ];
        }

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => $args['legend'] ?? true],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 40, 'containLabel' => true],
            'xAxis' => ['type' => 'value', 'scale' => true],
            'yAxis' => ['type' => 'value', 'scale' => true],
            'series' => [
                ['name' => $name, 'type' => 'scatter', 'symbolSize' => 10, 'data' => $points],
                ['name' => 'Trend', 'type' => 'line', 'showSymbol' => false, 'data' => $line, 'lineStyle' => ['width' => 2, 'type' => 'dashed']],
            ],
        ];
    }

    /**
     * Multi-series scatter where each series uses a distinct point symbol.
     */
    public static function scatterSymbols(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $legend = $args['legend'] ?? true;
        $symbols = ['circle', 'rect', 'triangle', 'diamond', 'pin', 'arrow'];

        $seriesOption = [];
        foreach (array_values($series) as $i => $s) {
            $seriesOption[] = array_merge([
                'type' => 'scatter',
                'symbol' => $symbols[$i % count($symbols)],
                'symbolSize' => 12,
            ], $s);
        }

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 40, 'containLabel' => true],
            'xAxis' => ['type' => 'value', 'scale' => true],
            'yAxis' => ['type' => 'value', 'scale' => true],
            'series' => $seriesOption,
        ];
    }

    /**
     * Packed bubble chart: force-packed bubbles sized by value and grouped by
     * category, with no links.
     */
    public static function packedBubble(array $args): array
    {
        $nodes = $args['nodes'] ?? [];
        $categories = $args['categories'] ?? [];

        $maxVal = 0;
        foreach ($nodes as $node) {
            $maxVal = max($maxVal, $node['value'] ?? 0);
        }
        $scale = $maxVal > 0 ? 60 / sqrt($maxVal) : 1;

        $data = array_map(function ($node) use ($scale) {
            $value = $node['value'] ?? 0;

            return array_filter([
                'name' => $node['name'] ?? '',
                'value' => $value,
                'symbolSize' => max(12, sqrt($value) * $scale),
                'category' => $node['category'] ?? null,
            ], fn ($v) => $v !== null);
        }, $nodes);

        return [
            'tooltip' => ['trigger' => 'item', 'formatter' => '{b}: {c}'],
            'legend' => ['show' => ! empty($categories), 'data' => $categories],
            'series' => [[
                'type' => 'graph',
                'layout' => 'force',
                'roam' => true,
                'draggable' => true,
                'data' => $data,
                'categories' => array_map(fn ($c) => ['name' => $c], $categories),
                'force' => ['repulsion' => 80, 'gravity' => 0.2, 'edgeLength' => 10],
                'label' => ['show' => true, 'position' => 'inside', 'fontSize' => 10],
                'emphasis' => ['focus' => 'self'],
            ]],
        ];
    }

    // ---- Pie variations ---------------------------------------------------

    /**
     * Semi-circle donut: slices fill the top half of a ring.
     */
    public static function pieSemi(array $args): array
    {
        $data = static::normalizePie($args['series'] ?? [], $args['labels'] ?? []);
        $legend = $args['legend'] ?? true;

        $sum = array_sum(array_map(fn ($d) => is_array($d) ? ($d['value'] ?? 0) : $d, $data));
        $data[] = [
            'value' => $sum,
            'name' => '',
            'itemStyle' => ['color' => 'transparent'],
            'label' => ['show' => false],
            'tooltip' => ['show' => false],
            'emphasis' => ['disabled' => true],
        ];

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => $legend, 'bottom' => 0],
            'series' => [[
                'type' => 'pie',
                'radius' => ['45%', '75%'],
                'center' => ['50%', '70%'],
                'startAngle' => 180,
                'label' => ['show' => true, 'formatter' => '{b}'],
                'data' => $data,
            ]],
        ];
    }

    /**
     * Pie with external data labels and leader lines.
     */
    public static function pieLabels(array $args): array
    {
        $data = static::normalizePie($args['series'] ?? [], $args['labels'] ?? []);

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => false],
            'series' => [[
                'type' => 'pie',
                'radius' => '55%',
                'center' => ['50%', '50%'],
                'label' => ['show' => true, 'position' => 'outside', 'formatter' => '{b}: {d}%'],
                'labelLine' => ['show' => true, 'length' => 16, 'length2' => 12],
                'data' => $data,
            ]],
        ];
    }

    /**
     * Monochrome pie: slices in shades of a single hue.
     */
    public static function pieMonochrome(array $args): array
    {
        $data = static::normalizePie($args['series'] ?? [], $args['labels'] ?? []);
        [$r, $g, $b] = $args['color'] ?? [249, 115, 22];
        $legend = $args['legend'] ?? true;
        $n = count($data);

        foreach ($data as $i => $d) {
            $t = $n > 1 ? ($i / ($n - 1)) * 0.8 : 0;
            $rr = (int) round($r + $t * (255 - $r));
            $gg = (int) round($g + $t * (255 - $g));
            $bb = (int) round($b + $t * (255 - $b));
            $data[$i] = array_merge(is_array($d) ? $d : ['value' => $d], ['itemStyle' => ['color' => "rgb({$rr},{$gg},{$bb})"]]);
        }

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => $legend, 'bottom' => 0],
            'series' => [[
                'type' => 'pie',
                'radius' => '70%',
                'data' => $data,
            ]],
        ];
    }

    /**
     * Gradient pie: each slice filled with a radial gradient.
     */
    public static function pieGradient(array $args): array
    {
        $data = static::normalizePie($args['series'] ?? [], $args['labels'] ?? []);
        $legend = $args['legend'] ?? true;
        $donut = $args['donut'] ?? true;
        $palette = [[249, 115, 22], [14, 165, 233], [34, 197, 94], [168, 85, 247], [239, 68, 68], [234, 179, 8]];

        foreach ($data as $i => $d) {
            [$r, $g, $b] = $palette[$i % count($palette)];
            $data[$i] = array_merge(is_array($d) ? $d : ['value' => $d], [
                'itemStyle' => ['color' => [
                    'type' => 'radial', 'x' => 0.5, 'y' => 0.5, 'r' => 0.75,
                    'colorStops' => [
                        ['offset' => 0, 'color' => "rgba({$r},{$g},{$b},0.55)"],
                        ['offset' => 1, 'color' => "rgb({$r},{$g},{$b})"],
                    ],
                ]],
            ]);
        }

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => $legend, 'bottom' => 0],
            'series' => [[
                'type' => 'pie',
                'radius' => $donut ? ['45%', '72%'] : '72%',
                'itemStyle' => ['borderRadius' => 6, 'borderColor' => 'transparent', 'borderWidth' => 2],
                'data' => $data,
            ]],
        ];
    }

    /**
     * Variable radius pie (roseType 'radius'): each slice's radius encodes value.
     */
    public static function pieVariable(array $args): array
    {
        return static::roseChart($args, 'radius');
    }

    /**
     * Nightingale rose (roseType 'area'): slice area encodes value.
     */
    public static function pieRose(array $args): array
    {
        return static::roseChart($args, 'area');
    }

    protected static function roseChart(array $args, string $roseType): array
    {
        $data = static::normalizePie($args['series'] ?? [], $args['labels'] ?? []);
        $legend = $args['legend'] ?? true;

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => $legend, 'bottom' => 0],
            'series' => [[
                'type' => 'pie',
                'radius' => ['20%', '75%'],
                'roseType' => $roseType,
                'itemStyle' => ['borderRadius' => 5],
                'data' => $data,
            ]],
        ];
    }

    // ---- Column & bar -----------------------------------------------------

    /**
     * Shared builder for stacked column/bar charts, optionally normalised to 100%.
     */
    protected static function stackedColumns(array $args, bool $horizontal, bool $percent): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $legend = $args['legend'] ?? true;

        if ($percent) {
            $values = array_map(fn ($s) => array_values($s['data'] ?? []), array_values($series));
            $count = $values ? max(array_map('count', $values)) : 0;
            for ($j = 0; $j < $count; $j++) {
                $sum = 0;
                foreach ($values as $row) {
                    $sum += $row[$j] ?? 0;
                }
                foreach ($values as $i => $row) {
                    $values[$i][$j] = $sum > 0 ? round(($row[$j] ?? 0) / $sum * 100, 2) : 0;
                }
            }
            $series = array_values($series);
            foreach ($series as $i => $s) {
                $series[$i]['data'] = $values[$i];
            }
        }

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'bar',
            'stack' => 'total',
            'emphasis' => ['focus' => 'series'],
        ], $s), array_values($series));

        $cat = ['type' => 'category', 'data' => $categories];
        $val = $percent
            ? ['type' => 'value', 'max' => 100, 'axisLabel' => ['formatter' => '{value}%']]
            : ['type' => 'value'];

        $tooltip = ['trigger' => 'axis', 'axisPointer' => ['type' => 'shadow']];
        if ($percent) {
            $tooltip['valueFormatter'] = '@@function (v) { return v + "%"; }@@';
        }

        return [
            'tooltip' => $tooltip,
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => $horizontal ? $val : $cat,
            'yAxis' => $horizontal ? $cat : $val,
            'series' => $seriesOption,
        ];
    }

    public static function columnStacked(array $args): array
    {
        return static::stackedColumns($args, false, false);
    }

    public static function barStacked(array $args): array
    {
        return static::stackedColumns($args, true, false);
    }

    public static function columnPercent(array $args): array
    {
        return static::stackedColumns($args, false, true);
    }

    public static function barPercent(array $args): array
    {
        return static::stackedColumns($args, true, true);
    }

    /**
     * Column chart with positive/negative bars coloured by sign.
     */
    public static function columnNegative(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $legend = $args['legend'] ?? false;
        $positive = $args['positiveColor'] ?? '#22c55e';
        $negative = $args['negativeColor'] ?? '#ef4444';

        $color = '@@function (p) { return p.value < 0 ? \''.$negative.'\' : \''.$positive.'\'; }@@';

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'bar',
            'itemStyle' => ['color' => $color, 'borderRadius' => [3, 3, 0, 0]],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'shadow']],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Column chart with rotated category labels — useful for many or long labels.
     */
    public static function columnRotated(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $legend = $args['legend'] ?? true;
        $rotate = $args['rotate'] ?? 45;

        $seriesOption = array_map(fn ($s) => array_merge([
            'type' => 'bar',
            'emphasis' => ['focus' => 'series'],
        ], $s), array_values($series));

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'shadow']],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories, 'axisLabel' => ['rotate' => $rotate, 'interval' => 0]],
            'yAxis' => ['type' => 'value'],
            'series' => $seriesOption,
        ];
    }

    /**
     * Floating range bars from a low to a high value (vertical or horizontal).
     */
    protected static function rangeColumns(array $args, bool $horizontal): array
    {
        $categories = $args['categories'] ?? [];
        $low = array_values($args['low'] ?? []);
        $high = array_values($args['high'] ?? []);
        $name = $args['name'] ?? 'Range';

        $diff = [];
        foreach ($low as $i => $l) {
            $h = $high[$i] ?? $l;
            $diff[] = ($l === null || $h === null) ? null : $h - $l;
        }

        $cat = ['type' => 'category', 'data' => $categories];
        $val = ['type' => 'value'];

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'shadow']],
            'legend' => ['show' => false],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 16, 'containLabel' => true],
            'xAxis' => $horizontal ? $val : $cat,
            'yAxis' => $horizontal ? $cat : $val,
            'series' => [
                [
                    'name' => 'low',
                    'type' => 'bar',
                    'stack' => 'range',
                    'silent' => true,
                    'itemStyle' => ['color' => 'transparent'],
                    'data' => $low,
                ],
                [
                    'name' => $name,
                    'type' => 'bar',
                    'stack' => 'range',
                    'itemStyle' => ['borderRadius' => 4],
                    'data' => $diff,
                ],
            ],
        ];
    }

    public static function columnRange(array $args): array
    {
        return static::rangeColumns($args, false);
    }

    public static function barRange(array $args): array
    {
        return static::rangeColumns($args, true);
    }

    /**
     * Histogram: bins a flat list of numeric values into frequency columns.
     */
    public static function histogram(array $args): array
    {
        $data = array_values(array_filter($args['data'] ?? [], 'is_numeric'));
        $bins = max(1, (int) ($args['bins'] ?? 10));

        $labels = [];
        $counts = array_fill(0, $bins, 0);

        if ($data) {
            $min = min($data);
            $max = max($data);
            $width = ($max - $min) ?: 1;
            $step = $width / $bins;

            foreach ($data as $v) {
                $idx = (int) floor(($v - $min) / $step);
                $idx = max(0, min($bins - 1, $idx));
                $counts[$idx]++;
            }
            for ($i = 0; $i < $bins; $i++) {
                $lo = round($min + $i * $step, 1);
                $hi = round($min + ($i + 1) * $step, 1);
                $labels[] = "{$lo}\u{2013}{$hi}";
            }
        }

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'shadow']],
            'legend' => ['show' => false],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $labels],
            'yAxis' => ['type' => 'value'],
            'series' => [[
                'type' => 'bar',
                'barCategoryGap' => '1%',
                'data' => $counts,
            ]],
        ];
    }

    /**
     * Lollipop chart: a thin stick topped with a circular marker per value.
     */
    public static function lollipop(array $args): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $categories = $args['categories'] ?? [];
        $values = $series[0]['data'] ?? [];

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'shadow']],
            'legend' => ['show' => false],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 16, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $categories],
            'yAxis' => ['type' => 'value'],
            'series' => [
                ['type' => 'bar', 'barWidth' => 2, 'silent' => true, 'data' => $values],
                ['type' => 'scatter', 'symbolSize' => 16, 'data' => $values],
            ],
        ];
    }

    /**
     * Pareto chart: columns sorted descending with a cumulative percentage line.
     */
    public static function pareto(array $args): array
    {
        $categories = $args['categories'] ?? [];
        $data = array_values($args['data'] ?? []);

        $pairs = [];
        foreach ($data as $i => $v) {
            $pairs[] = [$categories[$i] ?? $i, $v];
        }
        usort($pairs, fn ($a, $b) => $b[1] <=> $a[1]);

        $cats = array_column($pairs, 0);
        $vals = array_column($pairs, 1);
        $total = array_sum($vals) ?: 1;

        $cumulative = [];
        $running = 0;
        foreach ($vals as $v) {
            $running += $v;
            $cumulative[] = round($running / $total * 100, 1);
        }

        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'shadow']],
            'legend' => ['show' => true],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => 40, 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $cats],
            'yAxis' => [
                ['type' => 'value'],
                ['type' => 'value', 'max' => 100, 'axisLabel' => ['formatter' => '{value}%'], 'splitLine' => ['show' => false]],
            ],
            'series' => [
                ['name' => $args['name'] ?? 'Count', 'type' => 'bar', 'data' => $vals],
                ['name' => 'Cumulative', 'type' => 'line', 'yAxisIndex' => 1, 'smooth' => false, 'symbolSize' => 6, 'data' => $cumulative],
            ],
        ];
    }

    /**
     * Scatter / bubble. Data points are [x, y] or [x, y, size] for bubbles.
     */
    public static function scatter(array $args, bool $bubble = false): array
    {
        $series = static::normalizeSeries($args['series'] ?? []);
        $legend = $args['legend'] ?? true;

        $seriesOption = array_map(function ($s) use ($bubble) {
            $item = array_merge(['type' => 'scatter'], $s);
            if ($bubble) {
                $item['symbolSize'] = "@@function (d) { return Math.sqrt(d[2] || 1) * 4; }@@";
            }

            return $item;
        }, $series);

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => $legend],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'top' => $legend ? 40 : 16, 'containLabel' => true],
            'xAxis' => ['type' => 'value', 'scale' => true],
            'yAxis' => ['type' => 'value', 'scale' => true],
            'series' => $seriesOption,
        ];
    }

    /**
     * Pie / donut. Data is [['name' => , 'value' => ], ...] or value=>name map.
     */
    public static function pie(array $args, bool $donut = false): array
    {
        $data = static::normalizePie($args['series'] ?? [], $args['labels'] ?? []);
        $legend = $args['legend'] ?? true;
        $roseType = $args['rose'] ?? false;

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => $legend, 'bottom' => 0],
            'series' => [[
                'type' => 'pie',
                'radius' => $donut ? ['45%', '70%'] : ($roseType ? ['20%', '70%'] : '70%'),
                'roseType' => $roseType ? 'area' : false,
                'avoidLabelOverlap' => true,
                'itemStyle' => ['borderRadius' => $donut ? 6 : 0],
                'data' => $data,
            ]],
        ];
    }

    /**
     * Gauge. value 0..max, optional needle or progress ring.
     */
    public static function gauge(array $args): array
    {
        $value = $args['value'] ?? 0;
        $max = $args['max'] ?? 100;
        $min = $args['min'] ?? 0;
        $name = $args['name'] ?? '';
        $progress = $args['progress'] ?? false;

        return [
            'tooltip' => ['formatter' => '{a} <br/>{b} : {c}'],
            'series' => [[
                'type' => 'gauge',
                'min' => $min,
                'max' => $max,
                'progress' => ['show' => (bool) $progress, 'width' => 12],
                'pointer' => ['show' => ! $progress],
                'axisLine' => ['lineStyle' => ['width' => $progress ? 12 : 18]],
                'data' => [['value' => $value, 'name' => $name]],
            ]],
        ];
    }

    /**
     * An analog clock built from three gauge series (hour, minute, second).
     * The hands are advanced client-side every second.
     */
    public static function clock(array $args): array
    {
        $accent = $args['color'] ?? '#f97316';
        $hand = '#71717a';

        $hideZero = '@@function (value) { return value === 0 ? \'\' : value; }@@';

        $base = [
            'animationDurationUpdate' => 300,
            'animationEasingUpdate' => 'linear',
            'series' => [
                [
                    'name' => 'hour',
                    'type' => 'gauge',
                    'startAngle' => 90,
                    'endAngle' => -270,
                    'min' => 0,
                    'max' => 12,
                    'splitNumber' => 12,
                    'clockwise' => true,
                    'axisLine' => ['lineStyle' => ['width' => 6, 'color' => [[1, 'rgba(127,127,127,0.25)']]]],
                    'splitLine' => ['lineStyle' => ['width' => 3, 'color' => '#9ca3af']],
                    'axisTick' => ['splitNumber' => 5, 'lineStyle' => ['width' => 1, 'color' => '#c0c4cc']],
                    'axisLabel' => ['distance' => 20, 'fontSize' => 14, 'formatter' => $hideZero],
                    'pointer' => ['width' => 8, 'length' => '52%', 'itemStyle' => ['color' => $hand]],
                    'anchor' => ['show' => true, 'size' => 16, 'itemStyle' => ['color' => $hand]],
                    'detail' => ['show' => false],
                    'title' => ['show' => false],
                    'data' => [['value' => 0]],
                ],
                [
                    'name' => 'minute',
                    'type' => 'gauge',
                    'startAngle' => 90,
                    'endAngle' => -270,
                    'min' => 0,
                    'max' => 60,
                    'clockwise' => true,
                    'axisLine' => ['show' => false],
                    'splitLine' => ['show' => false],
                    'axisTick' => ['show' => false],
                    'axisLabel' => ['show' => false],
                    'pointer' => ['width' => 5, 'length' => '72%', 'itemStyle' => ['color' => $hand]],
                    'anchor' => ['show' => true, 'size' => 12, 'itemStyle' => ['color' => $hand]],
                    'detail' => ['show' => false],
                    'title' => ['show' => false],
                    'data' => [['value' => 0]],
                ],
                [
                    'name' => 'second',
                    'type' => 'gauge',
                    'startAngle' => 90,
                    'endAngle' => -270,
                    'min' => 0,
                    'max' => 60,
                    'clockwise' => true,
                    'axisLine' => ['show' => false],
                    'splitLine' => ['show' => false],
                    'axisTick' => ['show' => false],
                    'axisLabel' => ['show' => false],
                    'pointer' => ['width' => 2, 'length' => '85%', 'itemStyle' => ['color' => $accent]],
                    'anchor' => ['show' => true, 'size' => 7, 'showAbove' => true, 'itemStyle' => ['color' => $accent]],
                    'detail' => ['show' => false],
                    'title' => ['show' => false],
                    'data' => [['value' => 0]],
                ],
            ],
        ];

        return array_replace_recursive($base, $args['options'] ?? []);
    }

    public static function radar(array $args): array
    {
        $indicators = $args['indicators'] ?? [];
        $series = static::normalizeSeries($args['series'] ?? []);
        $legend = $args['legend'] ?? true;

        return [
            'tooltip' => ['trigger' => 'item'],
            'legend' => ['show' => $legend, 'bottom' => 0],
            'radar' => ['indicator' => $indicators],
            'series' => [[
                'type' => 'radar',
                'data' => array_map(fn ($s) => [
                    'name' => $s['name'] ?? '',
                    'value' => $s['data'] ?? $s['value'] ?? [],
                ], $series),
            ]],
        ];
    }

    public static function funnel(array $args): array
    {
        return [
            'tooltip' => ['trigger' => 'item', 'formatter' => '{b} : {c}'],
            'legend' => ['show' => $args['legend'] ?? true, 'bottom' => 0],
            'series' => [[
                'type' => 'funnel',
                'left' => '10%',
                'width' => '80%',
                'label' => ['show' => true, 'position' => 'inside'],
                'data' => static::normalizePie($args['series'] ?? [], $args['labels'] ?? []),
            ]],
        ];
    }

    public static function heatmap(array $args): array
    {
        $x = $args['categories'] ?? [];
        $y = $args['rows'] ?? [];
        $data = $args['series'] ?? [];
        $max = $args['max'] ?? 10;

        return [
            'tooltip' => ['position' => 'top'],
            'grid' => ['height' => '70%', 'top' => '5%', 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $x, 'splitArea' => ['show' => true]],
            'yAxis' => ['type' => 'category', 'data' => $y, 'splitArea' => ['show' => true]],
            'visualMap' => ['min' => 0, 'max' => $max, 'calculable' => true, 'orient' => 'horizontal', 'left' => 'center', 'bottom' => 0],
            'series' => [[
                'type' => 'heatmap',
                'data' => $data,
                'label' => ['show' => false],
                'emphasis' => ['itemStyle' => ['shadowBlur' => 10]],
            ]],
        ];
    }

    public static function candlestick(array $args): array
    {
        return [
            'tooltip' => ['trigger' => 'axis', 'axisPointer' => ['type' => 'cross']],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $args['categories'] ?? []],
            'yAxis' => ['type' => 'value', 'scale' => true],
            'series' => [['type' => 'candlestick', 'data' => $args['series'] ?? []]],
        ];
    }

    public static function boxplot(array $args): array
    {
        return [
            'tooltip' => ['trigger' => 'item'],
            'grid' => ['left' => '3%', 'right' => '4%', 'bottom' => '3%', 'containLabel' => true],
            'xAxis' => ['type' => 'category', 'data' => $args['categories'] ?? []],
            'yAxis' => ['type' => 'value'],
            'series' => [['type' => 'boxplot', 'data' => $args['series'] ?? []]],
        ];
    }

    // ---- Trees & network -------------------------------------------------

    public static function tree(array $args): array
    {
        return [
            'tooltip' => ['trigger' => 'item', 'triggerOn' => 'mousemove'],
            'series' => [[
                'type' => 'tree',
                'data' => [$args['data'] ?? []],
                'top' => '2%',
                'left' => '10%',
                'bottom' => '2%',
                'right' => '20%',
                'symbolSize' => 8,
                'expandAndCollapse' => true,
                'label' => ['position' => 'left', 'align' => 'right'],
                'leaves' => ['label' => ['position' => 'right', 'align' => 'left']],
            ]],
        ];
    }

    public static function treemap(array $args): array
    {
        return [
            'tooltip' => ['trigger' => 'item'],
            'series' => [[
                'type' => 'treemap',
                'data' => $args['data'] ?? $args['series'] ?? [],
                'roam' => false,
                'label' => ['show' => true],
            ]],
        ];
    }

    public static function sunburst(array $args): array
    {
        return [
            'tooltip' => ['trigger' => 'item'],
            'series' => [[
                'type' => 'sunburst',
                'data' => $args['data'] ?? $args['series'] ?? [],
                'radius' => [0, '90%'],
                'label' => ['rotate' => 'radial'],
            ]],
        ];
    }

    public static function sankey(array $args): array
    {
        return [
            'tooltip' => ['trigger' => 'item', 'triggerOn' => 'mousemove'],
            'series' => [[
                'type' => 'sankey',
                'data' => $args['nodes'] ?? [],
                'links' => $args['links'] ?? [],
                'emphasis' => ['focus' => 'adjacency'],
                'lineStyle' => ['color' => 'gradient', 'curveness' => 0.5],
            ]],
        ];
    }

    public static function graph(array $args): array
    {
        return [
            'tooltip' => [],
            'legend' => ['show' => $args['legend'] ?? false],
            'series' => [[
                'type' => 'graph',
                'layout' => $args['layout'] ?? 'force',
                'roam' => true,
                'draggable' => true,
                'data' => $args['nodes'] ?? [],
                'links' => $args['links'] ?? [],
                'categories' => $args['categories'] ?? [],
                'force' => ['repulsion' => 120, 'edgeLength' => 80],
                'label' => ['show' => true, 'position' => 'right'],
                'emphasis' => ['focus' => 'adjacency'],
            ]],
        ];
    }

    // ---- 3D (requires the GL extension) ---------------------------------

    public static function bar3d(array $args): array
    {
        return static::grid3d('bar3D', $args);
    }

    public static function scatter3d(array $args): array
    {
        return static::grid3d('scatter3D', $args);
    }

    public static function surface(array $args): array
    {
        return [
            'tooltip' => [],
            'xAxis3D' => ['type' => 'value'],
            'yAxis3D' => ['type' => 'value'],
            'zAxis3D' => ['type' => 'value'],
            'grid3D' => ['viewControl' => ['autoRotate' => $args['rotate'] ?? false]],
            'series' => [[
                'type' => 'surface',
                'data' => $args['series'] ?? $args['data'] ?? [],
            ]],
        ];
    }

    protected static function grid3d(string $type, array $args): array
    {
        $xAxis = ['type' => $args['xType'] ?? 'category'];
        if (isset($args['categories'])) {
            $xAxis['data'] = $args['categories'];
        }
        $yAxis = ['type' => $args['yType'] ?? 'category'];
        if (isset($args['rows'])) {
            $yAxis['data'] = $args['rows'];
        }

        $option = [
            'tooltip' => [],
            'xAxis3D' => $xAxis,
            'yAxis3D' => $yAxis,
            'zAxis3D' => ['type' => 'value'],
            'grid3D' => ['viewControl' => ['autoRotate' => $args['rotate'] ?? false]],
            'series' => [[
                'type' => $type,
                'data' => $args['series'] ?? $args['data'] ?? [],
                'shading' => 'lambert',
            ]],
        ];

        if (isset($args['visualMap'])) {
            $option['visualMap'] = $args['visualMap'];
        }

        return $option;
    }

    // ---- Normalizers -----------------------------------------------------

    protected static function normalizeSeries(array $series): array
    {
        if ($series === []) {
            return [];
        }

        $first = $series[array_key_first($series)];

        // Flat list of scalars -> single unnamed series.
        if (! is_array($first)) {
            return [['data' => array_values($series)]];
        }

        // Already a list of series objects.
        if (isset($first['data']) || isset($first['name']) || isset($first['value'])) {
            return array_values($series);
        }

        // List of points ([x,y], ...) -> single series.
        return [['data' => array_values($series)]];
    }

    protected static function normalizePie(array $series, array $labels): array
    {
        if ($series === []) {
            return [];
        }

        $first = $series[array_key_first($series)];

        // Already [['name'=>,'value'=>], ...]
        if (is_array($first)) {
            return array_values($series);
        }

        // Flat values + matching labels.
        return array_map(
            fn ($value, $i) => ['value' => $value, 'name' => $labels[$i] ?? (string) $i],
            array_values($series),
            array_keys(array_values($series)),
        );
    }
}
