# Changelog

All notable changes to WireCharts are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
Each released version below corresponds to a Git tag.

## [1.2.1] - 2026-06-26

### Fixed
- Corrected the release date shown for 1.2.0.

## [1.2.0] - 2026-06-26

Introduces the Line charts family.

### Added
- New `<chart:spline>` component — a smooth line where each series is marked with a distinct point symbol (circle, square, triangle, diamond, ...).

## [1.1.0] - 2026-06-25

Adds an animated clock gauge to the Pro tier.

### Added
- New `<chart:clock>` component — an animated analog clock built from gauges, with hands that advance every second.

### Fixed
- Charts now wait for the rendering engine to finish loading before initialising, fixing blank charts when scripts load out of order.

## [1.0.0] - 2026-06-25

First public release — the full chart gallery, with a free Basics tier and a licensed Pro tier.

### Added
- Flux-style colon syntax (`<chart:line />`) and the `@wirechartsScripts` directive.
- Basics (free): line, area, column, bar, pie and scatter charts.
- Pro: donut, bubble, gauge, radar, funnel, heatmap, candlestick, box plot, tree, treemap, sunburst, sankey, network graph and 3D charts.
- Accessible audio charts (`<chart:audio>`) with Web Audio sonification.
- Live reactivity through Livewire model binding, plus automatic dark-mode support.
- Offline license verification and the `wirecharts:activate` Artisan command.
