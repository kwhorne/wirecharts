# Changelog

All notable changes to WireCharts are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
Each released version below corresponds to a Git tag.

## [1.2.4] - 2026-06-26

Introduces the Column & bar charts family.

### Added
- New `<chart:column-stacked>` and `<chart:bar-stacked>` components — stacked vertical/horizontal bars.
- New `<chart:column-percent>` and `<chart:bar-percent>` components — 100% stacked vertical/horizontal bars.
- New `<chart:column-negative>` component — columns coloured by sign.
- New `<chart:column-rotated>` component — columns with rotated category labels.
- New `<chart:column-range>` and `<chart:bar-range>` components — floating low/high range bars.
- New `<chart:histogram>` component — bins a flat list of values into frequency columns.
- New `<chart:lollipop>` component — a stick-and-marker lollipop chart.
- New `<chart:pareto>` component — sorted columns with a cumulative percentage line.

## [1.2.3] - 2026-06-26

Introduces the Area charts family.

### Added
- New `<chart:area-gradient>` component — an area chart with smooth gradient fills, optionally stacked.
- New `<chart:area-stacked>` component — a stacked area chart showing cumulative totals and composition.
- New `<chart:area-percent>` component — a 100% stacked area chart showing each series as a percentage of the total.
- New `<chart:area-range>` component — a filled band between low and high values per category (e.g. min/max ranges).
- New `<chart:area-race>` component — an animated area race that reveals each filled series progressively, with a replay control.
- New `<chart:areaspline>` component — a smooth area spline with semi-transparent overlapping fills, optionally stacked.
- New `<chart:area-inverted>` component — an area chart with inverted axes (category axis vertical).
- New `<chart:area-negative>` component — an area chart with negative values coloured differently above and below zero.
- New `<chart:area-range-line>` component — a low/high band with a line (e.g. the average) overlaid.
- New `<chart:area-fan>` component — a fan chart with a central line and nested confidence bands for forecasts with uncertainty.
- New `<chart:streamgraph>` component — a flowing streamgraph (themeRiver) for composition over time.
- New `<chart:area-stacked-inverted>` component — a stacked area chart with inverted axes.
- New `<chart:area-missing>` component — an area chart that handles missing (null) points, with optional gap bridging.

## [1.2.2] - 2026-06-26

### Added
- New `<chart:spline-inverted>` component — a spline with inverted axes (category axis vertical), ideal for profiles such as temperature by altitude.
- New `<chart:line-labels>` component — a line chart that labels each series at the end of its line instead of using a legend.
- New `<chart:line-log>` component — a line chart with a logarithmic y-axis for values spanning several orders of magnitude.
- New `<chart:line-race>` component — an animated line race that reveals each series progressively, with a replay control.
- New `<chart:line-animated>` component — a line chart with a custom entrance animation (configurable easing and staggered point delay).
- New `<chart:line-forecast>` component — a line chart that draws actual values solid and forecast values as a dashed line over a shaded region.
- New `<chart:line-annotated>` component — a line chart with labelled annotation pins on chosen points and an optional average line.
- New `<chart:line-boost>` component — a line chart tuned for very large datasets with downsampling and zoom controls.
- New `<chart:line-time>` component — a time series line over a datetime axis with zoom/pan.
- New `<chart:spline-time>` component — a smooth spline over an irregular datetime axis with visible measurement points.
- New `<chart:spline-bands>` component — a spline with coloured horizontal plot bands marking value zones.

### Fixed
- Restored the original release dates for 1.0.0 and 1.1.0 (the 25th).

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
