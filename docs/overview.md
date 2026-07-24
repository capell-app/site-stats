# Site Stats

<!-- prettier-ignore-start -->

## What it does

Site Stats supplies Capell's shared metrics pipeline with two daily content totals: pages created and sites created. The values form a historical series that another Core metrics or dashboard surface can present.

The package collects content inventory, not visitor analytics. It does not track people, requests, referrers, devices, or page views.

## Where it appears

Site Stats has no package-owned screen, navigation item, setting, or public output. Its figures appear only when a consuming Core metrics dashboard or another installed package chooses to display the registered series.

The prospective Marketplace evidence target is Site Stats data inside a consuming Core metrics dashboard. No such screenshot is currently promoted. If captured, it will be evidence of the integration, not a claim that Site Stats ships that dashboard.

## How the totals work

- **Pages total** counts page records created on or before the end of the selected UTC day.
- **Sites total** counts site records created on or before the end of the selected UTC day.
- Soft-deleted records remain in both totals.
- Both figures are global across the installation, not filtered to one site.

Backfilled days use each record's creation date, so the series can show when the content inventory grew. A missing day remains missing rather than being silently treated as zero.

## Operations and access

The host must run Core's metrics collection or backfill workflow and retain the resulting samples. Site Stats does not register its own schedule.

The metrics are classified as internal and site-administrator visible. Limit access to the consuming dashboard accordingly. Use the package health check to confirm the collector is registered if expected series are absent.

---

For developers: see the [README](../README.md).

<!-- prettier-ignore-end -->
