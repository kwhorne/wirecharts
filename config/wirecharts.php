<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Asset delivery mode
    |--------------------------------------------------------------------------
    |
    | "cdn"    - Load Apache ECharts from a CDN and inline the Alpine glue.
    |            Zero build step, great for getting started.
    | "bundle" - Load the bundled/published assets from public/vendor/wirecharts.
    |            Run `php artisan vendor:publish --tag=wirecharts-assets`.
    |
    */
    'assets' => env('WIRECHARTS_ASSETS', 'cdn'),

    /*
    |--------------------------------------------------------------------------
    | 3D support (echarts-gl)
    |--------------------------------------------------------------------------
    |
    | Loads the echarts-gl extension required by the bar3d, scatter3d and
    | surface components. Adds ~600KB, so it is opt-in.
    |
    */
    'gl' => env('WIRECHARTS_GL', false),

    'cdn' => [
        'echarts' => 'https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js',
        'echarts_gl' => 'https://cdn.jsdelivr.net/npm/echarts-gl@2.0.9/dist/echarts-gl.min.js',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default theme
    |--------------------------------------------------------------------------
    |
    | Applied to every chart unless overridden per-chart. Honors Flux/Tailwind
    | dark mode when set to "auto".
    |
    */
    'theme' => env('WIRECHARTS_THEME', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Pro license
    |--------------------------------------------------------------------------
    |
    | The Basics components are free. Every other component is part of
    | WireCharts Pro and requires a license key purchased at the URL below.
    | Activate with: php artisan wirecharts:activate <key>
    |
    */
    'license' => env('WIRECHARTS_LICENSE_KEY'),

    'purchase_url' => 'https://wirecharts.io/pro',

];
