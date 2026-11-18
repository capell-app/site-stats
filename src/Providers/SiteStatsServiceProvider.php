<?php

declare(strict_types=1);

namespace Capell\SiteStats\Providers;

use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\SiteStats\Console\Commands\CollectSiteStatsCommand;
use Capell\SiteStats\Metrics\ContentTotalsMetricsCollector;
use Illuminate\Console\Scheduling\Schedule;
use Override;
use Spatie\LaravelPackageTools\Package;

final class SiteStatsServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-site-stats';

    public static string $packageName = 'capell-app/site-stats';

    #[Override]
    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasTranslations()
            ->hasCommands([CollectSiteStatsCommand::class]);
    }

    #[Override]
    protected function bootInstalledRuntime(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', self::$name);
        $this->surface()->metricCollector(ContentTotalsMetricsCollector::class);

        // Totals can only be recorded for the current day, so the schedule is what fills each
        // day; running hourly means one missed run does not lose the day.
        $this->registerSchedule(static function (Schedule $schedule): void {
            $schedule->command('capell:site-stats:collect')
                ->hourly()
                ->withoutOverlapping()
                ->onOneServer();
        });
    }
}
