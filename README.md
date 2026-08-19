# Site Stats

<!-- prettier-ignore-start -->

## What This Plugin Adds

Site Stats is an **Available**, **No package-owned schema** Capell package in the **Capell Foundation** product group. It ships as `capell-app/site-stats` and extends these surfaces: shared.

Site Stats registers privacy-safe current-day page, site, active site, and active domain totals with Capell's typed metrics pipeline.

Authorized global administrators can inspect four retained content-inventory trends through a consuming Core metrics surface without collecting visitor analytics. The retained series is stored by Core, not by Site Stats.

Evidence: [`src/Metrics/ContentTotalsMetricsCollector.php`](src/Metrics/ContentTotalsMetricsCollector.php), [`src/Providers/SiteStatsServiceProvider.php`](src/Providers/SiteStatsServiceProvider.php), [`tests/Unit/Metrics/ContentTotalsMetricsCollectorTest.php`](tests/Unit/Metrics/ContentTotalsMetricsCollectorTest.php), [`docs/screenshots/site-stats-metrics-dashboard.png`](docs/screenshots/site-stats-metrics-dashboard.png), [`src/Health/SiteStatsHealthCheck.php`](src/Health/SiteStatsHealthCheck.php), [`tests/Feature/ScreenshotFixtureRouteTest.php`](tests/Feature/ScreenshotFixtureRouteTest.php).

Status details:

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/site-stats`
- Namespace: `Capell\SiteStats`
- Theme key: not applicable

## Why It Matters

**For developers:** The collector uses Core metric definitions, scopes, governance, and daily rollups rather than introducing a package-specific reporting store. Core persists samples in `metric_daily_rollups` and retains them for 365 days by default, bounded to a configured range of 30-3650 days. Core rolls up at 00:20 UTC and prunes expired rollups at 00:50 UTC; the host scheduler must be running for these jobs to execute.

**For teams:** Teams get a simple retained view of content growth without page-view tracking, request logging, or a second analytics dashboard.

Evidence: [`src/Metrics/ContentTotalsMetricsCollector.php`](src/Metrics/ContentTotalsMetricsCollector.php), [`tests/Unit/ManifestRequirementsTest.php`](tests/Unit/ManifestRequirementsTest.php), [`tests/Feature/ScreenshotFixtureRouteTest.php`](tests/Feature/ScreenshotFixtureRouteTest.php).

## Screens And Workflow

Screenshot contract: `docs/screenshots.json`.

![Site Stats content totals in the Core metrics dashboard](docs/screenshots/site-stats-metrics-dashboard.png)

- Site Stats content totals in the Core metrics dashboard (admin, optional diagnostic host-integration capture).

## Technical Shape

- Service providers: `Capell\SiteStats\Providers\SiteStatsServiceProvider`.
- Manifest contributions: `health-check: Capell\SiteStats\Health\SiteStatsHealthCheck`.
- Health checks: `Capell\SiteStats\Health\SiteStatsHealthCheck`.
- Cache tags: `site-stats`.

## Data Model

- Required source tables: `pages`, `sites`, `site_domains`.
- Migration impact: Site Stats declares no migrations. Core owns the `metric_daily_rollups` and metric collection-run tables used for retained samples; run Core migrations through the host install flow before opening package surfaces.
- Deletion/retention behaviour: Site Stats has no deletion or retention command. Core's `capell:metrics:prune` deletes daily rollups and their provenance runs older than the configured `capell.analytics.daily_rollup_retention_days` (365 days by default; accepted range 30-3650). Core registers that command daily at 00:50 UTC, after the 00:20 UTC rollup; a running host scheduler is required.

## Install Impact

- Required packages: `capell-app/core`.
- Admin navigation: no admin page or resource contribution is declared.
- Admin/editor extensions: none declared.
- Permissions: none declared in `capell.json`.
- Public routes: none declared.
- Database changes: no package migrations declared.
- Config: no package config files.
- Settings: no package settings declared.
- Queues or schedules: none declared by Site Stats; Core owns and schedules the shared metrics rollup and retention jobs.
- Cache tags: `site-stats`.
- Commands: none declared.

## Common Pitfalls

- Keep required Capell packages on compatible v4 releases: `capell-app/core`.
- Custom write integrations must preserve invalidation for `site-stats` cache tags.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |

## Quick Start

1. Install the package: `composer require capell-app/site-stats`.
2. No package-specific setup command, migrations, retention command, or schedule is declared; Core supplies the shared persistence and schedule.
3. Open the Site Stats metrics in the Core metrics dashboard and confirm the admin workflow loads.

## Next Steps

- [Package docs](docs/README.md)
- [Overview](docs/overview.md)
- [Troubleshooting](#troubleshooting)
- [Screenshot contract](docs/screenshots.json)
- [Marketplace assets](docs/assets/marketplace/)
- [Capell content language plan](../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../docs/erd/capell-and-package-erds.md)
- Focused tests: `vendor/bin/pest packages/site-stats/tests --configuration=phpunit.xml`.

<!-- prettier-ignore-end -->
