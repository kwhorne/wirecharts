# WireCharts

Flux-style chart components for Laravel Livewire. 30+ chart types in one elegant
package — drop in `<chart:line />` and ship reactive, accessible charts in minutes.

The **Basics** charts are free and open source. Everything else is part of
**WireCharts Pro**, unlocked with a license key from [wirecharts.io](https://wirecharts.io/pro).

## Installation

```bash
composer require wirecharts/wirecharts
```

Add the scripts directive before the closing `</body>` tag of your layout:

```blade
@wirechartsScripts
```

## Usage

```blade
<chart:line
    :series="[['name' => 'Revenue', 'data' => [120, 180, 150, 260]]]"
    :categories="['Jan', 'Feb', 'Mar', 'Apr']"
    smooth
/>
```

Charts follow your Tailwind/Flux dark mode automatically and can live-bind to a
Livewire property:

```blade
<chart:bar :series="$series" model="series" />
```

## Components

**Basics (free):** `line`, `area`, `column`, `bar`, `pie`, `scatter`

**Pro:** `donut`, `bubble`, `gauge`, `clock`, `radar`, `funnel`, `heatmap`,
`candlestick`, `boxplot`, `tree`, `treemap`, `sunburst`, `sankey`, `graph`,
`bar3d`, `scatter3d`, `surface`, `audio`

See the full reference and live demos at
[wirecharts.io/docs](https://wirecharts.io/docs).

## Activating Pro

After purchasing a license, activate it:

```bash
php artisan wirecharts:activate YOUR-LICENSE-KEY
```

This unlocks every Pro component. Without a license, Pro charts render a tasteful
locked placeholder while the Basics keep working.

## Configuration

Publish the config to customize the asset source, theme and 3D support:

```bash
php artisan vendor:publish --tag=wirecharts-config
```

| Env | Default | Description |
|-----|---------|-------------|
| `WIRECHARTS_ASSETS` | `cdn` | `cdn` or `bundle` asset delivery. |
| `WIRECHARTS_THEME` | `auto` | `auto`, `light` or `dark`. |
| `WIRECHARTS_GL` | `false` | Load the 3D extension for `bar3d`, `scatter3d`, `surface`. |
| `WIRECHARTS_LICENSE_KEY` | – | Your Pro license key. |

## Testing

```bash
composer install
vendor/bin/pest
```

## License

WireCharts is open-source software licensed under the [MIT license](LICENSE).
The Pro components require a commercial license key.
