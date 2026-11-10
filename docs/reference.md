# Site Stats reference

<!-- prettier-ignore-start -->

Developer reference for `capell-app/site-stats`. Start with the [README](../README.md) for what the extension does and how to install it.

## Package Details

Site Stats is an **Available**, **No schema impact** Capell package in the **Capell Essentials** product group. It ships as `capell-app/site-stats` and extends these surfaces: shared.

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/site-stats`
- Namespace: `Capell\SiteStats`
- Theme key: not applicable

## Technical Shape

### Service providers

- `Capell\SiteStats\Providers\SiteStatsServiceProvider`

### Manifest contributions

- `health-check: Capell\SiteStats\Health\SiteStatsHealthCheck`

### Health checks

- `Capell\SiteStats\Health\SiteStatsHealthCheck`

### Cache tags

- `site-stats`


## Data Model

- Required tables: `pages`, `sites`, `site_domains`.
- Migration impact: run host migrations through the package install flow before opening package surfaces.
- Deletion/retention behaviour: Docs gap: migrations and manifest contributions do not prove a cascade, pruning command, or timed retention policy.

<!-- prettier-ignore-end -->
