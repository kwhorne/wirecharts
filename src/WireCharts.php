<?php

namespace WireCharts;

use Illuminate\Support\Facades\Blade;

/**
 * Entry point for asset rendering. Used by the @wirechartsStyles
 * and @wirechartsScripts Blade directives.
 */
class WireCharts
{
    public static function styles(): string
    {
        // ECharts renders to canvas/SVG and needs no external stylesheet.
        // Reserved for future theme CSS variables.
        return '';
    }

    public static function scripts(): string
    {
        $mode = config('wirecharts.assets', 'cdn');
        $gl = (bool) config('wirecharts.gl', false);

        $scripts = [];

        if ($mode === 'bundle') {
            $scripts[] = '<script src="'.asset('vendor/wirecharts/echarts.min.js').'" defer></script>';
            if ($gl) {
                $scripts[] = '<script src="'.asset('vendor/wirecharts/echarts-gl.min.js').'" defer></script>';
            }
        } else {
            $scripts[] = '<script src="'.e(config('wirecharts.cdn.echarts')).'" defer></script>';
            if ($gl) {
                $scripts[] = '<script src="'.e(config('wirecharts.cdn.echarts_gl')).'" defer></script>';
            }
        }

        $scripts[] = Blade::render('<x-chart::scripts />');

        return implode(PHP_EOL, $scripts);
    }
}
