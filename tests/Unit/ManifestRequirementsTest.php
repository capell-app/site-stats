<?php

declare(strict_types=1);

use Capell\Core\Contracts\Extensions\ChecksExtensionHealth;
use Capell\Core\Contracts\Metrics\CollectsDailyMetrics;
use Capell\Core\Support\Manifest\ManifestValidator;
use Capell\SiteStats\Health\SiteStatsHealthCheck;
use Capell\SiteStats\Metrics\ContentTotalsMetricsCollector;

/**
 * @return array<string, mixed>
 */
function siteStatsJson(string $path): array
{
    $decoded = json_decode(
        (string) file_get_contents(dirname(__DIR__, 2) . '/' . $path),
        associative: true,
        flags: JSON_THROW_ON_ERROR,
    );

    throw_unless(is_array($decoded), RuntimeException::class);

    $json = [];

    foreach ($decoded as $key => $value) {
        throw_unless(is_string($key), RuntimeException::class);

        $json[$key] = $value;
    }

    return $json;
}

it('passes the Capell manifest validator', function (): void {
    $manifest = siteStatsJson('capell.json');

    (new ManifestValidator)->validate(
        data: $manifest,
        composerJson: siteStatsJson('composer.json'),
        packageName: 'capell-app/site-stats',
        discoverySource: 'packages/site-stats/capell.json',
    );

    expect(data_get($manifest, 'version'))->toBe('1.0.0')
        ->and(data_get($manifest, 'product.tier'))->toBe('free')
        ->and(data_get($manifest, 'commercial.requestedCertification'))->toBe('first-party')
        ->and(data_get($manifest, 'database.requiredTables'))->toBe(['pages', 'sites', 'site_domains'])
        ->and(data_get($manifest, 'contributionTraceability.runtimeIntegrations.metricCollectors'))->toBe([
            ContentTotalsMetricsCollector::class,
        ]);
});

it('declares its shipped health and metrics contracts', function (): void {
    expect(class_implements(SiteStatsHealthCheck::class))->toContain(ChecksExtensionHealth::class)
        ->and(class_implements(ContentTotalsMetricsCollector::class))->toContain(CollectsDailyMetrics::class);
});

it('does not promote its host-owned diagnostics as package screenshots', function (): void {
    $manifest = siteStatsJson('capell.json');
    $screenshots = siteStatsJson('docs/screenshots.json');

    expect(data_get($manifest, 'surfaces'))->toBe(['shared'])
        ->and(data_get($manifest, 'marketplace.screenshots'))->toBe([])
        ->and(data_get($screenshots, 'entries.0.required'))->toBeFalse()
        ->and(data_get($screenshots, 'entries.0.fixtureKind'))->toBe('host-integration-diagnostic');
});

it('keeps Core-owned persistence and scheduling out of the package manifest', function (): void {
    $manifest = siteStatsJson('capell.json');
    $overview = file_get_contents(dirname(__DIR__, 2) . '/docs/overview.md');

    expect(data_get($manifest, 'database.migrations'))->toBeFalse()
        ->and(data_get($manifest, 'commands.setup'))->toBeNull()
        ->and(data_get($manifest, 'contributionTraceability.runtimeIntegrations.metricCollectors'))
        ->toContain(ContentTotalsMetricsCollector::class)
        ->and($overview)->toContain('Core persists samples in `metric_daily_rollups`')
        ->and($overview)->toContain('Core owns and schedules the shared metrics rollup and retention jobs')
        ->and($overview)->toContain('optional diagnostic host-integration capture');
});
