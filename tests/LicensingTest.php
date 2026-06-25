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
