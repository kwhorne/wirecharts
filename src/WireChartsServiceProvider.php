<?php

namespace WireCharts;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use WireCharts\Console\ActivateCommand;
use WireCharts\Console\ChangelogCommand;
use WireCharts\Licensing\License;

class WireChartsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wirecharts.php', 'wirecharts');

        $this->app->singleton(License::class, function ($app) {
            return new License(
                key: $app['config']->get('wirecharts.license'),
                host: parse_url((string) $app['config']->get('app.url'), PHP_URL_HOST) ?: null,
            );
        });
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->registerBladeNamespace();
        $this->registerColonSyntaxCompiler();
        $this->registerDirectives();
        $this->registerPublishing();

        if ($this->app->runningInConsole()) {
            $this->commands([ActivateCommand::class, ChangelogCommand::class]);
        }
    }

    /**
     * Load package views under the "wirecharts" view namespace.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'wirecharts');
    }

    /**
     * Register the anonymous component namespace so that
     * <x-chart::line /> resolves to resources/views/components/line.blade.php.
     */
    protected function registerBladeNamespace(): void
    {
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'chart');
    }

    /**
     * Flux-style colon syntax: rewrite <chart:line> into <x-chart::line>
     * (and the matching closing tags) before Blade compiles the view.
     */
    protected function registerColonSyntaxCompiler(): void
    {
        Blade::prepareStringsForCompilationUsing(function (string $value): string {
            // Opening / self-closing tags: <chart:line ...> => <x-chart::line ...>
            $value = preg_replace('/<\s*chart:([a-zA-Z0-9\-\.]+)/', '<x-chart::$1', $value);

            // Closing tags: </chart:line> => </x-chart::line>
            $value = preg_replace('/<\/\s*chart:([a-zA-Z0-9\-\.]+)\s*>/', '</x-chart::$1>', $value);

            return $value;
        });
    }

    /**
     * @wirechartsStyles and @wirechartsScripts directives,
     * mirroring Flux's @fluxStyles / @fluxScripts.
     */
    protected function registerDirectives(): void
    {
        Blade::directive('wirechartsStyles', fn () => "<?php echo \WireCharts\WireCharts::styles(); ?>");
        Blade::directive('wirechartsScripts', fn () => "<?php echo \WireCharts\WireCharts::scripts(); ?>");
    }

    protected function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/wirecharts.php' => config_path('wirecharts.php'),
        ], 'wirecharts-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/wirecharts'),
        ], 'wirecharts-views');

        $this->publishes([
            __DIR__.'/../resources/dist' => public_path('vendor/wirecharts'),
        ], 'wirecharts-assets');
    }
}
