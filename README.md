# Site Stats

<!-- prettier-ignore-start -->

## What This Plugin Adds

Site Stats is an **Available**, **No schema impact** Capell package in the **Capell Foundation** product group. It ships as `capell-app/site-stats` and extends the shared metrics pipeline.

Site Stats registers a typed daily collector for global page and site totals. It does not add a package-owned page, widget, route, or public output; an installed Core metrics consumer decides where the collected series is displayed.

Evidence: [`capell.json`](capell.json), [`src/Metrics/ContentTotalsMetricsCollector.php`](src/Metrics/ContentTotalsMetricsCollector.php), [`src/Providers/SiteStatsServiceProvider.php`](src/Providers/SiteStatsServiceProvider.php), [`src/Health/SiteStatsHealthCheck.php`](src/Health/SiteStatsHealthCheck.php), [`tests/Unit/Metrics/ContentTotalsMetricsCollectorTest.php`](tests/Unit/Metrics/ContentTotalsMetricsCollectorTest.php).

Status details:

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/site-stats`
- Namespace: `Capell\SiteStats`
- Theme key: not applicable

## Why It Matters

**For developers:** Metrics use Core's typed definition, collection, scope, representation, and governance contracts instead of exposing package-specific arrays or dashboard queries.

**For teams:** A consuming metrics dashboard can show page and site growth from a consistent daily history without Site Stats collecting visitor analytics or adding another reporting screen.

## Metrics Contract

The `content_totals` collector provides:

- `content.pages_total`: all page records created by the end of the requested UTC day, including soft-deleted records.
- `content.sites_total`: all site records created by the end of the requested UTC day, including soft-deleted records.

Both are integer gauges at global scope. They are classified as internal, visible to site administrators, sourced from Core content tables, and support backfill. Collection returns `Unsupported` when no global scope is requested.

## Screens And Workflow

Screenshot contract: [`docs/screenshots.json`](docs/screenshots.json).

The prospective screenshot target is an authentic capture of a consuming Core metrics dashboard with Site Stats installed and seeded daily samples available. It is not a Site Stats page or illustrative marketplace artwork. The target remains unfulfilled and is not Marketplace media until that capture exists and has been reviewed.

## Install Impact

- Required packages: `capell-app/core`.
- Admin navigation: none.
- Admin/editor extensions: none.
- Permissions: none declared in `capell.json`.
- Public routes: none.
- Database changes: none.
- Settings: none.
- Queues or schedules: none declared by this package; the host is responsible for running Core metrics collection.
- Health check: confirms the Site Stats collector is registered.

## Common Pitfalls

- Installing Site Stats registers the collector but does not create a dashboard.
- A consuming Core metrics surface needs collected or backfilled samples before it can render a useful trend.
- The collector currently supports only the global scope; do not present its values as site-scoped totals.
- Totals include soft-deleted records because they describe records created by the end of each day, not currently active content.

## Quick Start

1. Install the package: `composer require capell-app/site-stats`.
2. Refresh Capell's package discovery and confirm the Site Stats health check passes.
3. Run the host's Core metrics collection or backfill workflow.
4. Inspect the resulting `content.pages_total` and `content.sites_total` series through a consuming metrics surface.

## Next Steps

- [Operator overview](docs/overview.md)
- [Screenshot contract](docs/screenshots.json)
- Focused tests: `vendor/bin/pest packages/site-stats/tests --configuration=phpunit.xml`.

<!-- prettier-ignore-end -->
