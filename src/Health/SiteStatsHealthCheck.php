<?php

declare(strict_types=1);

namespace Capell\SiteStats\Health;

use Capell\Core\Contracts\Extensions\ChecksExtensionHealth;
use Capell\Core\Data\Diagnostics\DoctorCheckResultData;
use Capell\Core\Support\Metrics\MetricCollectorRegistry;
use Capell\SiteStats\Metrics\ContentTotalsMetricsCollector;
use Illuminate\Support\Collection;

final class SiteStatsHealthCheck implements ChecksExtensionHealth
{
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

        return collect([
            new DoctorCheckResultData(
                label: (string) __('capell-site-stats::metrics.health.collector.label'),
                passed: $collectorRegistered,
                message: $collectorRegistered
                    ? (string) __('capell-site-stats::metrics.health.collector.passed')
                    : (string) __('capell-site-stats::metrics.health.collector.failed'),
                remediation: $collectorRegistered
                    ? null
                    : (string) __('capell-site-stats::metrics.health.collector.remediation'),
            ),
        ]);
    }

    public static function passed(): bool
    {
        return self::runDiagnostics()
            ->every(static fn (DoctorCheckResultData $result): bool => $result->passed);
    }
}
