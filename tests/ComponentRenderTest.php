<?php

use Illuminate\Support\Facades\Blade;
use WireCharts\Licensing\License;

// These tests cover rendering, not licensing — unlock everything.
beforeEach(function () {
    app()->instance(License::class, new class extends License {
        public function allows(string $component): bool
        {
            return true;
        }
    });
});

it('rewrites colon syntax and renders a cartesian line chart', function () {
    $html = Blade::render('<chart:line :series="$s" :categories="$c" />', [
        's' => [['name' => 'Sales', 'data' => [1, 2, 3]]],
        'c' => ['Jan', 'Feb', 'Mar'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('x-ref="canvas"')
        ->toContain('wire:ignore')
        ->toContain('\u0022type\u0022:\u0022line\u0022')
        ->toContain('\u0022data\u0022:[1,2,3]');
});

it('builds horizontal bars by swapping axes', function () {
    $html = Blade::render('<chart:bar :series="$s" :categories="$c" />', [
        's' => [['name' => 'Sales', 'data' => [1, 2, 3]]],
        'c' => ['A', 'B', 'C'],
    ]);

    // Horizontal bar => xAxis is value, yAxis is category.
    expect($html)
        ->toContain('\u0022type\u0022:\u0022bar\u0022')
        ->toContain('\u0022xAxis\u0022:{\u0022type\u0022:\u0022value\u0022');
});

it('maps flat values and labels for pie charts', function () {
    $html = Blade::render('<chart:donut :series="$s" :labels="$l" />', [
        's' => [44, 55, 13],
        'l' => ['A', 'B', 'C'],
    ]);

    expect($html)
        ->toContain('\u0022type\u0022:\u0022pie\u0022')
        ->toContain('\u0022value\u0022:44,\u0022name\u0022:\u0022A\u0022');
});

it('emits the ECharts engine and glue via the directive', function () {
    $html = Blade::render('@wirechartsScripts');

    expect($html)
        ->toContain('Alpine.data(')
        ->toContain('echarts.init')
        ->toContain('echarts.min.js')
        ->toContain('reviveFunctions');
});

it('registers the audio sonification engine in the glue', function () {
    $html = Blade::render('@wirechartsScripts');

    expect($html)
        ->toContain("Alpine.data('wireChartAudio'")
        ->toContain('AudioContext')
        ->toContain('createOscillator');
});

it('renders an accessible audio chart with a play control', function () {
    $html = Blade::render('<chart:audio :series="$s" :categories="$c" :duration="3000" instrument="triangle" />', [
        's' => [['name' => 'Temp', 'data' => [10, 14, 9, 18, 22]]],
        'c' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
    ]);

    expect($html)
        ->toContain('wireChartAudio(')
        ->toContain('@click="toggle()"')
        ->toContain('role="img"')
        ->toContain(':aria-label="summary()"')
        ->toContain('\u0022instrument\u0022:\u0022triangle\u0022')
        ->toContain('\u0022duration\u0022:3000');
});

it('opts in to echarts-gl only when configured', function () {
    config()->set('wirecharts.gl', false);
    expect(Blade::render('@wirechartsScripts'))->not->toContain('echarts-gl');

    config()->set('wirecharts.gl', true);
    expect(Blade::render('@wirechartsScripts'))->toContain('echarts-gl.min.js');
});

it('compiles every chart type without error', function (string $tag) {
    $html = Blade::render("<chart:{$tag} />");

    expect($html)
        ->toContain('wireChart(')
        ->toContain('x-ref="canvas"');
})->with([
    // Basics / line / area
    'line', 'area', 'column', 'bar',
    // Scatter & bubble
    'scatter', 'bubble',
    // Pie
    'pie', 'donut', 'funnel',
    // Gauges
    'gauge',
    // Heat maps
    'heatmap',
    // Other cartesian
    'radar', 'candlestick', 'boxplot',
    // Trees & network
    'tree', 'treemap', 'sunburst', 'sankey', 'graph',
    // 3D
    'bar3d', 'scatter3d', 'surface',
]);

it('renders an animated clock gauge', function () {
    $html = Blade::render('<chart:clock height="280" />');

    expect($html)
        ->toContain('wireChartClock(')
        ->toContain('x-ref="canvas"')
        ->toContain('\u0022type\u0022:\u0022gauge\u0022')
        ->toContain('\u0022max\u0022:12');
});

it('renders a spline with per-series symbols', function () {
    $html = Blade::render('<chart:spline :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'A', 'data' => [1, 2, 3]],
            ['name' => 'B', 'data' => [3, 2, 1]],
        ],
        'c' => ['Jan', 'Feb', 'Mar'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022smooth\u0022:true')
        ->toContain('\u0022symbol\u0022:\u0022circle\u0022')
        ->toContain('\u0022symbol\u0022:\u0022rect\u0022');
});

it('renders a spline with inverted axes', function () {
    $html = Blade::render('<chart:spline-inverted :series="$s" :categories="$c" />', [
        's' => [['name' => 'Temp', 'data' => [15, 8, -5, -40]]],
        'c' => ['0 km', '5 km', '10 km', '20 km'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022xAxis\u0022:{\u0022type\u0022:\u0022value\u0022}')
        ->toContain('\u0022yAxis\u0022:{\u0022type\u0022:\u0022category\u0022')
        ->toContain('\u0022smooth\u0022:true');
});

it('renders a line chart with end labels', function () {
    $html = Blade::render('<chart:line-labels :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'Solar', 'data' => [42, 50, 62, 80]],
            ['name' => 'Wind', 'data' => [30, 38, 41, 55]],
        ],
        'c' => ['2019', '2020', '2021', '2022'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022endLabel\u0022')
        ->toContain('params.seriesName')
        ->toContain('\u0022legend\u0022:{\u0022show\u0022:false}');
});

it('renders a line chart with a logarithmic axis', function () {
    $html = Blade::render('<chart:line-log :series="$s" :categories="$c" />', [
        's' => [['name' => 'Users', 'data' => [1, 10, 100, 1000, 10000]]],
        'c' => ['2018', '2019', '2020', '2021', '2022'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022yAxis\u0022:{\u0022type\u0022:\u0022log\u0022')
        ->toContain('\u0022logBase\u0022:10');
});

it('renders an animated line race', function () {
    $html = Blade::render('<chart:line-race :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'USA', 'data' => [10, 20, 35, 50]],
            ['name' => 'China', 'data' => [5, 18, 40, 62]],
        ],
        'c' => ['Q1', 'Q2', 'Q3', 'Q4'],
    ]);

    expect($html)
        ->toContain('wireChartRace(')
        ->toContain('@click="play()"')
        ->toContain('\u0022endLabel\u0022');
});

it('renders a line with a custom entrance animation', function () {
    $html = Blade::render('<chart:line-animated :series="$s" :categories="$c" easing="elasticOut" />', [
        's' => [['name' => 'Visits', 'data' => [120, 200, 150, 280, 300]]],
        'c' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022animationEasing\u0022:\u0022elasticOut\u0022')
        ->toContain('\u0022animationDelay\u0022')
        ->toContain('idx * 60');
});

it('renders a forecast line with solid and dashed segments', function () {
    $html = Blade::render('<chart:line-forecast :categories="$c" :actual="$a" :forecast="$f" />', [
        'c' => ['2020', '2021', '2022', '2023', '2024'],
        'a' => [100, 120, 140],
        'f' => [165, 190],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022type\u0022:\u0022dashed\u0022')
        ->toContain('\u0022markArea\u0022')
        ->toContain('Forecast');
});

it('renders a line with annotations', function () {
    $html = Blade::render('<chart:line-annotated :series="$s" :categories="$c" :annotations="$a" average />', [
        's' => [['name' => 'Sales', 'data' => [120, 300, 180, 90, 260]]],
        'c' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        'a' => [
            ['x' => 'Feb', 'y' => 300, 'text' => 'Peak'],
            ['x' => 'Apr', 'y' => 90, 'text' => 'Dip'],
        ],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022markPoint\u0022')
        ->toContain('Peak')
        ->toContain('\u0022markLine\u0022');
});

it('renders a boost line for large datasets', function () {
    $html = Blade::render('<chart:line-boost :series="$s" />', [
        's' => [['name' => 'Signal', 'data' => range(1, 50)]],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022sampling\u0022:\u0022lttb\u0022')
        ->toContain('\u0022large\u0022:true')
        ->toContain('\u0022dataZoom\u0022')
        ->toContain('\u0022animation\u0022:false');
});

it('renders a time series line', function () {
    $html = Blade::render('<chart:line-time :series="$s" />', [
        's' => [['name' => 'USD/EUR', 'data' => [['2024-01-01', 0.91], ['2024-02-01', 0.93], ['2024-03-01', 0.92]]]],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022xAxis\u0022:{\u0022type\u0022:\u0022time\u0022}')
        ->toContain('\u0022dataZoom\u0022')
        ->toContain('2024-02-01');
});

it('renders a spline over an irregular time axis', function () {
    $html = Blade::render('<chart:spline-time :series="$s" />', [
        's' => [['name' => 'Snow depth', 'data' => [['2024-01-03', 0.45], ['2024-01-19', 0.88], ['2024-02-11', 1.2]]]],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022xAxis\u0022:{\u0022type\u0022:\u0022time\u0022}')
        ->toContain('\u0022smooth\u0022:true')
        ->toContain('\u0022showSymbol\u0022:true');
});

it('renders a spline with horizontal plot bands', function () {
    $html = Blade::render('<chart:spline-bands :series="$s" :categories="$c" :bands="$b" />', [
        's' => [['name' => 'Wind', 'data' => [4, 8, 12, 6, 10]]],
        'c' => ['00', '06', '12', '18', '24'],
        'b' => [
            ['from' => 0, 'to' => 5, 'color' => 'rgba(68,170,213,0.1)', 'label' => 'Light'],
            ['from' => 5, 'to' => 10, 'color' => 'rgba(0,0,0,0.05)', 'label' => 'Moderate'],
        ],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022markArea\u0022')
        ->toContain('Moderate')
        ->toContain('\u0022smooth\u0022:true');
});

it('renders an area chart with gradient fills', function () {
    $html = Blade::render('<chart:area-gradient :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'USA', 'data' => [120, 132, 101, 134]],
            ['name' => 'USSR', 'data' => [220, 182, 191, 234]],
        ],
        'c' => ['1960', '1970', '1980', '1990'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022areaStyle\u0022')
        ->toContain('\u0022colorStops\u0022')
        ->toContain('\u0022smooth\u0022:true');
});

it('renders a stacked area chart', function () {
    $html = Blade::render('<chart:area-stacked :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'Asia', 'data' => [1700, 2300, 3600, 4500]],
            ['name' => 'Africa', 'data' => [230, 360, 640, 1200]],
        ],
        'c' => ['1950', '1970', '1990', '2010'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022stack\u0022:\u0022total\u0022')
        ->toContain('\u0022areaStyle\u0022');
});

it('renders a 100% stacked area and normalises to percentages', function () {
    $html = Blade::render('<chart:area-percent :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'A', 'data' => [10, 20, 30]],
            ['name' => 'B', 'data' => [10, 20, 30]],
        ],
        'c' => ['x', 'y', 'z'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022stack\u0022:\u0022total\u0022')
        ->toContain('\u0022max\u0022:100')
        ->toContain('{value}%')
        ->toContain('[50,50,50]'); // equal series -> 50% each
});

it('renders an area range band from low and high values', function () {
    $html = Blade::render('<chart:area-range :categories="$c" :low="$lo" :high="$hi" />', [
        'c' => ['Mon', 'Tue', 'Wed'],
        'lo' => [10, 12, 9],
        'hi' => [20, 25, 18],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022stack\u0022:\u0022range\u0022')
        ->toContain('[10,13,9]')   // diff = high - low
        ->toContain('[10,12,9]');  // low baseline
});

it('renders an animated area race', function () {
    $html = Blade::render('<chart:area-race :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'Solar', 'data' => [10, 20, 35, 50]],
            ['name' => 'Wind', 'data' => [5, 18, 28, 44]],
        ],
        'c' => ['Q1', 'Q2', 'Q3', 'Q4'],
    ]);

    expect($html)
        ->toContain('wireChartRace(')
        ->toContain('@click="play()"')
        ->toContain('\u0022areaStyle\u0022');
});

it('renders a smooth area spline', function () {
    $html = Blade::render('<chart:areaspline :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'John', 'data' => [3, 4, 3, 5, 4, 10, 12]],
            ['name' => 'Jane', 'data' => [1, 3, 4, 3, 3, 5, 4]],
        ],
        'c' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022smooth\u0022:true')
        ->toContain('\u0022areaStyle\u0022');
});

it('renders an area chart with inverted axes', function () {
    $html = Blade::render('<chart:area-inverted :series="$s" :categories="$c" />', [
        's' => [['name' => 'Depth', 'data' => [5, 12, 20, 9]]],
        'c' => ['0 m', '10 m', '20 m', '30 m'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022xAxis\u0022:{\u0022type\u0022:\u0022value\u0022}')
        ->toContain('\u0022yAxis\u0022:{\u0022type\u0022:\u0022category\u0022')
        ->toContain('\u0022areaStyle\u0022');
});

it('renders an area chart with negative values coloured by sign', function () {
    $html = Blade::render('<chart:area-negative :series="$s" :categories="$c" />', [
        's' => [['name' => 'Net', 'data' => [5, -3, 8, -6, 2]]],
        'c' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022visualMap\u0022')
        ->toContain('\u0022pieces\u0022')
        ->toContain('\u0022areaStyle\u0022');
});

it('renders an area range with a line overlay', function () {
    $html = Blade::render('<chart:area-range-line :categories="$c" :low="$lo" :high="$hi" :line="$ln" />', [
        'c' => ['Mon', 'Tue', 'Wed'],
        'lo' => [10, 12, 9],
        'hi' => [20, 25, 18],
        'ln' => [15, 18, 13],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022stack\u0022:\u0022range\u0022')
        ->toContain('[10,13,9]')   // band diff
        ->toContain('[15,18,13]'); // overlay line
});

it('renders a fan chart with nested confidence bands', function () {
    $html = Blade::render('<chart:area-fan :categories="$c" :line="$ln" :bands="$b" />', [
        'c' => ['Now', '+1', '+2'],
        'ln' => [10, 11, 12],
        'b' => [
            ['low' => [8, 6, 4], 'high' => [12, 16, 20]],
            ['low' => [9, 8, 7], 'high' => [11, 14, 17]],
        ],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('fan0')
        ->toContain('fan1')
        ->toContain('rgba(')
        ->toContain('[10,11,12]'); // median line
});

it('renders a streamgraph via themeRiver', function () {
    $html = Blade::render('<chart:streamgraph :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'A', 'data' => [3, 5, 8]],
            ['name' => 'B', 'data' => [6, 4, 7]],
        ],
        'c' => ['Jan', 'Feb', 'Mar'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022themeRiver\u0022')
        ->toContain('\u0022singleAxis\u0022')
        ->toContain('[\u0022Jan\u0022,3,\u0022A\u0022]');
});

it('renders a stacked area with inverted axes', function () {
    $html = Blade::render('<chart:area-stacked-inverted :series="$s" :categories="$c" />', [
        's' => [
            ['name' => 'Asia', 'data' => [1700, 2300, 3600]],
            ['name' => 'Africa', 'data' => [230, 360, 640]],
        ],
        'c' => ['1950', '1970', '1990'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022stack\u0022:\u0022total\u0022')
        ->toContain('\u0022xAxis\u0022:{\u0022type\u0022:\u0022value\u0022}')
        ->toContain('\u0022yAxis\u0022:{\u0022type\u0022:\u0022category\u0022');
});

it('renders an area chart with missing points', function () {
    $html = Blade::render('<chart:area-missing :series="$s" :categories="$c" />', [
        's' => [['name' => 'Readings', 'data' => [3, null, 5, 8, null, 6]]],
        'c' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022connectNulls\u0022:false')
        ->toContain('[3,null,5,8,null,6]')
        ->toContain('\u0022areaStyle\u0022');
});

it('renders series-based column & bar variants', function (string $tag, string $needle) {
    $html = Blade::render("<chart:{$tag} :series=\"\$s\" :categories=\"\$c\" />", [
        's' => [['name' => 'A', 'data' => [5, -3, 8, 6]], ['name' => 'B', 'data' => [2, 4, 1, 9]]],
        'c' => ['Q1', 'Q2', 'Q3', 'Q4'],
    ]);

    expect($html)->toContain('wireChart(')->toContain($needle);
})->with([
    ['column-stacked', '\u0022stack\u0022:\u0022total\u0022'],
    ['bar-stacked', '\u0022stack\u0022:\u0022total\u0022'],
    ['column-percent', '{value}%'],
    ['bar-percent', '{value}%'],
    ['column-negative', '#ef4444'],
    ['column-rotated', '\u0022rotate\u0022:45'],
    ['lollipop', '\u0022scatter\u0022'],
]);

it('renders range column & bar variants', function (string $tag) {
    $html = Blade::render("<chart:{$tag} :categories=\"\$c\" :low=\"\$lo\" :high=\"\$hi\" />", [
        'c' => ['Mon', 'Tue', 'Wed'],
        'lo' => [10, 12, 9],
        'hi' => [20, 25, 18],
    ]);

    expect($html)->toContain('wireChart(')->toContain('\u0022stack\u0022:\u0022range\u0022')->toContain('[10,13,9]');
})->with(['column-range', 'bar-range']);

it('bins values into a histogram', function () {
    $html = Blade::render('<chart:histogram :data="$d" :bins="4" />', [
        'd' => [1, 2, 2, 3, 3, 3, 4, 5, 8, 13],
    ]);

    expect($html)->toContain('wireChart(')->toContain('\u0022barCategoryGap\u0022');
});

it('sorts a pareto chart with a cumulative line', function () {
    $html = Blade::render('<chart:pareto :categories="$c" :data="$d" />', [
        'c' => ['A', 'B', 'C'],
        'd' => [10, 40, 25],
    ]);

    expect($html)->toContain('wireChart(')->toContain('\u0022Cumulative\u0022')->toContain('\u0022yAxisIndex\u0022:1');
});

it('renders pie variations', function (string $tag, string $needle) {
    $html = Blade::render("<chart:{$tag} :series=\"\$s\" :labels=\"\$l\" />", [
        's' => [40, 25, 20, 15],
        'l' => ['Chrome', 'Safari', 'Edge', 'Firefox'],
    ]);

    expect($html)->toContain('wireChart(')->toContain('\u0022type\u0022:\u0022pie\u0022')->toContain($needle);
})->with([
    ['pie-semi', '\u0022startAngle\u0022:180'],
    ['pie-labels', '\u0022labelLine\u0022'],
    ['pie-monochrome', '\u0022itemStyle\u0022'],
    ['pie-gradient', '\u0022radial\u0022'],
    ['pie-variable', '\u0022roseType\u0022:\u0022radius\u0022'],
    ['pie-rose', '\u0022roseType\u0022:\u0022area\u0022'],
]);

it('renders a scatter with a regression line', function () {
    $html = Blade::render('<chart:scatter-regression :series="$s" />', [
        's' => [['name' => 'Obs', 'data' => [[1, 2], [2, 4], [3, 5], [4, 8], [5, 9]]]],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022scatter\u0022')
        ->toContain('\u0022Trend\u0022')
        ->toContain('\u0022type\u0022:\u0022dashed\u0022');
});

it('renders multi-series scatter with distinct symbols', function () {
    $html = Blade::render('<chart:scatter-symbols :series="$s" />', [
        's' => [
            ['name' => 'A', 'data' => [[1, 2], [3, 4]]],
            ['name' => 'B', 'data' => [[2, 3], [4, 5]]],
        ],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022symbol\u0022:\u0022circle\u0022')
        ->toContain('\u0022symbol\u0022:\u0022rect\u0022');
});

it('renders a packed bubble chart', function () {
    $html = Blade::render('<chart:packed-bubble :nodes="$n" :categories="$c" />', [
        'n' => [
            ['name' => 'PHP', 'value' => 80, 'category' => 'Backend'],
            ['name' => 'Vue', 'value' => 50, 'category' => 'Frontend'],
        ],
        'c' => ['Backend', 'Frontend'],
    ]);

    expect($html)
        ->toContain('wireChart(')
        ->toContain('\u0022type\u0022:\u0022graph\u0022')
        ->toContain('\u0022layout\u0022:\u0022force\u0022')
        ->toContain('\u0022symbolSize\u0022');
});
