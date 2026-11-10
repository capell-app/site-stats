# Site Stats

<!-- prettier-ignore-start -->

## What It Does

Site Stats registers privacy-safe current-day page, site, active site, and active domain totals with Capell's typed metrics pipeline.

Authorised global administrators can inspect four retained content-inventory trends through a consuming Core metrics surface without collecting visitor analytics.

- **Price and availability:** Check current availability and price on the [Capell Marketplace listing](https://capell.app/extensions/site-stats).
- **Licence:** Published under MIT in `composer.json`.
- **Requires:** `PHP ^8.4`, `capell-app/core ^1.0.66`, `laravel/framework ^12.41.1|^13.0`, `spatie/laravel-package-tools ^1.14.1`.
- **Install:** `composer require capell-app/site-stats`.

## Who It Is For

**For developers:** The collector uses Core metric definitions, scopes, governance, and daily rollups rather than introducing a package-specific reporting store.

**For teams:** Teams get a simple retained view of content growth without page-view tracking, request logging, or a second analytics dashboard.

## Quick Start

1. Install the package: `composer require capell-app/site-stats`.
2. Verify the package provider and manifest contributions are registered in the host app.

## Configure

This extension works without configuration. There is nothing to set after install.

## Limits And Pitfalls

- Install the required Capell packages at the versions in `composer.json`: `capell-app/core ^1.0.66`.
- Custom write integrations must preserve invalidation for `site-stats` cache tags.

## Screens And Workflow

- Site Stats content totals in the Core metrics dashboard (admin).
- Site Stats content totals in the Core metrics dashboard with admin sidebar menu open (admin).

## Install Impact

- Required packages: `capell-app/core`.
- Admin navigation: no admin page or resource contribution is declared.
- Admin/editor extensions: none declared.
- Permissions: no package permission declarations or Shield gates detected; host access rules still apply.
- Public routes: none declared.
- Database changes: no package migrations declared.
- Config: no package config files.
- Settings: no package settings declared.
- Queues or schedules: none declared.
- Cache tags: `site-stats`.
- Commands: none declared.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |

## Next Steps

- [Package docs index](README.md)
- [Developer troubleshooting](../README.md#troubleshooting)
- [Developer reference](reference.md)
- [Marketplace assets](assets/marketplace/)
- [Capell content language plan](../../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../../docs/erd/capell-and-package-erds.md)

<!-- prettier-ignore-end -->
