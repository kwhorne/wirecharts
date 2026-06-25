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
