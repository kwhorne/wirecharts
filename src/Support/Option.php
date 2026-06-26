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
