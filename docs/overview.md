# Site Stats

<!-- prettier-ignore-start -->

Once you look after more than a handful of sites, it gets hard to say how many pages you have or how many domains are switched on. Site Stats counts them every hour and keeps one figure for each UTC day, so you can see whether the numbers are going up.

There are four totals. Pages counts every page that hasn't been deleted, drafts included. Sites counts the same way, and active sites are the ones switched on. Active domains are the switched-on domains attached to an active site. Only global administrators can see the figures, so someone who looks after a single site never sees totals for the rest.

The counting runs from your server's Laravel scheduler, which needs to be running. The last count of each day is the one kept. A count can only describe what exists right now, so a day the scheduler missed has no figure and can't be added later. The health check flags that, both before the first count and whenever 24 hours pass without one. Nothing about visitors is read and nothing leaves your server.

When the first count has run, the Site metrics page in the Capell admin shows a card for each total with its latest figure and a 30-day line.

- **Price and availability:** Check current availability and price on the [Capell Marketplace listing](https://capell.app/extensions/site-stats).
- **Licence:** Published under MIT in `composer.json`.
- **Requires:** `PHP ^8.4`, `capell-app/core ^1.0.66`, `laravel/framework ^12.41.1|^13.0`, `spatie/laravel-package-tools ^1.14.1`.
- **Install:** `composer require capell-app/site-stats`.

## Quick Start

1. Install the package: `composer require capell-app/site-stats`.
2. Run `php artisan list` and verify the package command signatures are registered.

## For Developers

The collector uses Core metric definitions, scopes, governance, and daily rollups rather than introducing a package-specific reporting store.

## Configure

- **Commands:** `php artisan capell:site-stats:collect`.

## Limits And Pitfalls

- Install the required Capell packages at the versions in `composer.json`: `capell-app/core ^1.0.66`.
- Keep the host Laravel scheduler running so package-registered schedules can execute: `capell:site-stats:collect (hourly; package registered)`.
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
- Queues or schedules: scheduled commands `capell:site-stats:collect (hourly; package registered)`.
- Cache tags: `site-stats`.
- Commands: `capell:site-stats:collect`.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |
| Background work does not run | Queue worker or declared schedule is not active | Check the jobs and scheduled commands listed in `Technical Shape` | Start the queue worker or host scheduler, then run the focused command or package test |

## Next Steps

- [Package docs index](README.md)
- [Developer troubleshooting](../README.md#troubleshooting)
- [Developer reference](reference.md)
- [Marketplace assets](assets/marketplace/)
- [Capell content language plan](../../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../../docs/erd/capell-and-package-erds.md)

<!-- prettier-ignore-end -->
