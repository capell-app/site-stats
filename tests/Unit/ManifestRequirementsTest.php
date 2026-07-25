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

    return $decoded;
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
        ->and(data_get($manifest, 'database.requiredTables'))->toBe(['pages', 'sites'])
        ->and(data_get($manifest, 'contributionTraceability.runtimeIntegrations.metricCollectors'))->toBe([
            ContentTotalsMetricsCollector::class,
        ]);
});

it('declares its shipped health and metrics contracts', function (): void {
    expect(class_implements(SiteStatsHealthCheck::class))->toContain(ChecksExtensionHealth::class)
        ->and(class_implements(ContentTotalsMetricsCollector::class))->toContain(CollectsDailyMetrics::class);
});

it('keeps marketplace screenshot metadata tied to committed evidence', function (): void {
    $manifest = siteStatsJson('capell.json');
    $screenshots = siteStatsJson('docs/screenshots.json');
    $marketplaceScreenshot = data_get($manifest, 'marketplace.screenshots.0');

    expect($marketplaceScreenshot)->toBeArray()
        ->and(data_get($marketplaceScreenshot, 'path'))->toBe('docs/screenshots/site-stats-metrics-dashboard.png')
        ->and(data_get($marketplaceScreenshot, 'alt'))->not->toBeEmpty()
        ->and(data_get($marketplaceScreenshot, 'caption'))->not->toBeEmpty()
        ->and(is_file(dirname(__DIR__, 2) . '/' . data_get($marketplaceScreenshot, 'path')))->toBeTrue()
        ->and(data_get($screenshots, 'entries.0.screenshotPath'))->toBe(
            'packages/site-stats/' . data_get($marketplaceScreenshot, 'path'),
        );
});
