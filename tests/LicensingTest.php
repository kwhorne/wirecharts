<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use WireCharts\Licensing\Catalog;
use WireCharts\Licensing\License;

// A real Pro key signed by the licensing server (no expiry, any domain).
const VALID_KEY = 'eyJwbGFuIjoicHJvIiwibmFtZSI6IkFjbWUgSW5jIiwiZW1haWwiOiJkZXZAYWNtZS5jb20iLCJpc3N1ZWQiOjE3ODIzOTUxNTd9.5qrLCLMHXPi_pFLkJffBs2JnFwxMPyETa2PysbYn0cgMJpwwSJMdF5y9yxjaSvVVfG7Rpzlf-WXfpFfSQAzZDw';

function withLicense(?string $key): void
{
    app()->instance(License::class, new License($key, null));
}

it('verifies a genuine key and rejects garbage', function () {
    expect((new License(VALID_KEY, null))->active())->toBeTrue()
        ->and((new License('not-a-key', null))->active())->toBeFalse()
        ->and((new License(null, null))->active())->toBeFalse();
});

it('always allows Basics but gates Pro without a license', function () {
    $free = new License(null, null);

    foreach (Catalog::FREE as $component) {
        expect($free->allows($component))->toBeTrue("{$component} should be free");
    }
    foreach (Catalog::PRO as $component) {
        expect($free->allows($component))->toBeFalse("{$component} should be gated");
    }
});

it('renders Basics charts for everyone', function () {
    withLicense(null);

    expect(Blade::render('<chart:line :series="$s" />', ['s' => [[1, 2, 3]]]))
        ->toContain('wireChart(')
        ->not->toContain('wirecharts-locked');
});

it('renders a locked placeholder for Pro charts without a license', function () {
    withLicense(null);

    $html = Blade::render('<chart:sankey />');

    expect($html)
        ->toContain('wirecharts-locked')
        ->toContain('WireCharts Pro')
        ->toContain('https://wirecharts.io/pro')
        ->not->toContain('wireChart(');
});

it('locks the audio chart without a license', function () {
    withLicense(null);

    expect(Blade::render('<chart:audio :series="$s" />', ['s' => [[1, 2, 3]]]))
        ->toContain('wirecharts-locked')
        ->not->toContain('wireChartAudio(');
});

it('unlocks every Pro chart with a valid license', function () {
    withLicense(VALID_KEY);

    expect(Blade::render('<chart:sankey :nodes="$n" :links="$l" />', ['n' => [], 'l' => []]))
        ->toContain('wireChart(')
        ->not->toContain('wirecharts-locked');

    expect(Blade::render('<chart:audio :series="$s" />', ['s' => [[1, 2, 3]]]))
        ->toContain('wireChartAudio(')
        ->not->toContain('wirecharts-locked');
});

it('rejects an invalid key via the activate command', function () {
    Artisan::call('wirecharts:activate', ['key' => 'bogus.key']);

    expect(Artisan::output())->toContain('invalid or expired');
});

it('activates a valid key via the activate command', function () {
    $envPath = app()->environmentFilePath();
    @file_put_contents($envPath, "APP_KEY=base64:test\n");

    Artisan::call('wirecharts:activate', ['key' => VALID_KEY]);

    expect(Artisan::output())->toContain('activated for Acme Inc');
    expect(file_get_contents($envPath))->toContain('WIRECHARTS_LICENSE_KEY="'.VALID_KEY.'"');

    @unlink($envPath);
});

it('locks the clock gauge without a license', function () {
    withLicense(null);

    expect(Blade::render('<chart:clock />'))
        ->toContain('wirecharts-locked')
        ->not->toContain('wireChartClock(');
});

it('unlocks the clock gauge with a valid license', function () {
    withLicense(VALID_KEY);

    expect(Blade::render('<chart:clock />'))
        ->toContain('wireChartClock(')
        ->not->toContain('wirecharts-locked');
});

it('verifies the changelog covers every tag', function () {
    $this->artisan('wirecharts:changelog')
        ->assertSuccessful()
        ->expectsOutputToContain('1.1.0');
});

it('gates the spline component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:spline />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:spline />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the inverted spline without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:spline-inverted />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:spline-inverted />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the line-labels component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:line-labels />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:line-labels />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the line-log component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:line-log />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:line-log />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the line-race component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:line-race />'))->toContain('wirecharts-locked')->not->toContain('wireChartRace(');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:line-race />'))->toContain('wireChartRace(')->not->toContain('wirecharts-locked');
});

it('gates the line-animated component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:line-animated />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:line-animated />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the line-forecast component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:line-forecast />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:line-forecast />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the line-annotated component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:line-annotated />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:line-annotated />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the line-boost component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:line-boost />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:line-boost />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the line-time component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:line-time />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:line-time />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the spline-time component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:spline-time />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:spline-time />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the spline-bands component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:spline-bands />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:spline-bands />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-gradient component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-gradient />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-gradient />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-stacked component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-stacked />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-stacked />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-percent component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-percent />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-percent />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-range component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-range />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-range />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-race component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-race />'))->toContain('wirecharts-locked')->not->toContain('wireChartRace(');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-race />'))->toContain('wireChartRace(')->not->toContain('wirecharts-locked');
});

it('gates the areaspline component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:areaspline />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:areaspline />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-inverted component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-inverted />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-inverted />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-negative component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-negative />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-negative />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-range-line component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-range-line />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-range-line />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-fan component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-fan />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-fan />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the streamgraph component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:streamgraph />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:streamgraph />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-stacked-inverted component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-stacked-inverted />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-stacked-inverted />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the area-missing component without a license', function () {
    withLicense(null);
    expect(Blade::render('<chart:area-missing />'))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render('<chart:area-missing />'))->toContain('wireChart(')->not->toContain('wirecharts-locked');
});

it('gates the new column & bar components without a license', function (string $tag) {
    withLicense(null);
    expect(Blade::render("<chart:{$tag} />"))->toContain('wirecharts-locked');

    withLicense(VALID_KEY);
    expect(Blade::render("<chart:{$tag} />"))->toContain('wireChart(')->not->toContain('wirecharts-locked');
})->with(['column-stacked', 'bar-stacked', 'column-percent', 'bar-percent', 'column-negative', 'column-rotated', 'column-range', 'bar-range', 'histogram', 'lollipop', 'pareto']);
