<?php

declare(strict_types=1);

namespace Capell\SiteStats\Console\Commands;

use Capell\Core\Actions\Metrics\RollupDailyMetricsAction;
use Capell\Core\Data\Metrics\MetricScopeData;
use Capell\Core\Enums\Metrics\MetricCollectionRunStatus;
use Capell\Core\Models\MetricCollectionRun;
use Capell\SiteStats\Metrics\ContentTotalsMetricsCollector;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

/**
 * Records today's content totals. The totals describe the content as it is now, so only the
 * current UTC day can be collected; a day the schedule misses stays empty and cannot be
 * filled in later. Running it again on the same day replaces that day's totals.
 */
final class CollectSiteStatsCommand extends Command
{
    protected $signature = 'capell:site-stats:collect';

    protected $description = 'Record today\'s page, site, active site and active domain totals.';

    public function handle(RollupDailyMetricsAction $rollup): int
    {
        $day = CarbonImmutable::now('UTC')->toDateString();

        $written = $rollup->execute(
            day: $day,
            scopes: [MetricScopeData::global('UTC')],
            collectorKeys: [ContentTotalsMetricsCollector::OWNER . ':' . ContentTotalsMetricsCollector::COLLECTOR],
        );

        $run = MetricCollectionRun::query()
            ->where('owner_package', ContentTotalsMetricsCollector::OWNER)
            ->where('collector_key', ContentTotalsMetricsCollector::COLLECTOR)
            ->whereDate('day', $day)
            ->latest('id')
            ->first();

        if (! $run instanceof MetricCollectionRun || $run->status !== MetricCollectionRunStatus::Completed) {
            $this->components->error(sprintf(
                'Site Stats could not record today\'s totals: %s',
                $run instanceof MetricCollectionRun && $run->error_summary !== null ? $run->error_summary : 'the collector did not run.',
            ));

            return self::FAILURE;
        }

        $this->components->info(sprintf('Recorded %d site stats total(s) for %s.', $written, $day));

        return self::SUCCESS;
    }
}
