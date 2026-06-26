<?php

namespace WireCharts\Licensing;

/**
 * Classifies chart components into the free "Basics" tier and the
 * commercial "Pro" tier. Basics render for everyone; Pro components
 * require an active license.
 */
class Catalog
{
    /**
     * Free, always-available components (Highcharts "Basics").
     */
    public const FREE = [
        'line',
        'area',
        'column',
        'bar',
        'pie',
        'scatter',
    ];

    /**
     * Commercial components unlocked by a Pro license.
     */
    public const PRO = [
        'spline',
        'spline-inverted',
        'line-labels',
        'line-log',
        'line-race',
        'line-animated',
        'line-forecast',
        'line-annotated',
        'line-boost',
        'line-time',
        'spline-time',
        'spline-bands',
        'donut',
        'bubble',
        'gauge',
        'clock',
        'radar',
        'funnel',
        'heatmap',
        'candlestick',
        'boxplot',
        'tree',
        'treemap',
        'sunburst',
        'sankey',
        'graph',
        'bar3d',
        'scatter3d',
        'surface',
        'audio',
    ];

    public static function isFree(string $component): bool
    {
        return in_array($component, self::FREE, true);
    }

    public static function isPro(string $component): bool
    {
        return in_array($component, self::PRO, true);
    }

    public static function all(): array
    {
        return [...self::FREE, ...self::PRO];
    }
}
