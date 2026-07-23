<?php

declare(strict_types=1);

namespace Capell\SiteStats\Providers;

use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\SiteStats\Metrics\ContentTotalsMetricsCollector;
use Spatie\LaravelPackageTools\Package;

final class SiteStatsServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-site-stats';

    public static string $packageName = 'capell-app/site-stats';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasTranslations();
    }

    protected function bootInstalledPackage(): self
    {
        $this->surface()->metricCollector(ContentTotalsMetricsCollector::class);

        return $this;
    }
}
