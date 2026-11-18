<?php

declare(strict_types=1);

namespace Capell\SiteStats\Health;

use Capell\Core\Contracts\Extensions\ChecksExtensionHealth;
use Capell\Core\Data\Diagnostics\DoctorCheckResultData;
use Capell\Core\Enums\Metrics\MetricCollectionRunStatus;
use Capell\Core\Models\MetricCollectionRun;
use Capell\Core\Support\Metrics\MetricCollectorRegistry;
use Capell\SiteStats\Metrics\ContentTotalsMetricsCollector;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class SiteStatsHealthCheck implements ChecksExtensionHealth
{
    /** The collection runs hourly; a day without a completed run means nothing is running it. */
    private const int STALE_AFTER_HOURS = 24;

    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }

    /**
     * @return Collection<int, DoctorCheckResultData>
     */
    public static function runDiagnostics(): Collection
    {
        $collectorRegistered = collect(resolve(MetricCollectorRegistry::class)->collectors())
            ->contains(
                static fn (object $collector): bool => $collector instanceof ContentTotalsMetricsCollector,
            );

        $lastCompletedAt = MetricCollectionRun::query()
            ->where('owner_package', ContentTotalsMetricsCollector::OWNER)
            ->where('collector_key', ContentTotalsMetricsCollector::COLLECTOR)
            ->where('status', MetricCollectionRunStatus::Completed)
            ->max('completed_at');
        $collecting = is_string($lastCompletedAt)
            && CarbonImmutable::parse($lastCompletedAt, 'UTC')->greaterThanOrEqualTo(CarbonImmutable::now('UTC')->subHours(self::STALE_AFTER_HOURS));

        return collect([
            new DoctorCheckResultData(
                label: (string) __('capell-site-stats::metrics.health.collection.label'),
                passed: $collecting,
                message: $collecting
                    ? (string) __('capell-site-stats::metrics.health.collection.passed')
                    : (string) __('capell-site-stats::metrics.health.collection.failed', ['hours' => self::STALE_AFTER_HOURS]),
                remediation: $collecting
                    ? null
                    : (string) __('capell-site-stats::metrics.health.collection.remediation'),
                id: 'site.stats.collection',
            ),
            new DoctorCheckResultData(
                label: (string) __('capell-site-stats::metrics.health.collector.label'),
                passed: $collectorRegistered,
                message: $collectorRegistered
                    ? (string) __('capell-site-stats::metrics.health.collector.passed')
                    : (string) __('capell-site-stats::metrics.health.collector.failed'),
                remediation: $collectorRegistered
                    ? null
                    : (string) __('capell-site-stats::metrics.health.collector.remediation'),
                id: 'legacy.site.stats.collector',
            ),
        ]);
    }

    public static function passed(): bool
    {
        return self::runDiagnostics()
            ->every(static fn (DoctorCheckResultData $result): bool => $result->passed);
    }
}
