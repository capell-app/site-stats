<?php

declare(strict_types=1);

namespace Capell\SiteStats\Providers;

use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\SiteStats\Metrics\ContentTotalsMetricsCollector;
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
            ->hasTranslations();
    }

    #[Override]
    protected function bootInstalledRuntime(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', self::$name);
        $this->surface()->metricCollector(ContentTotalsMetricsCollector::class);
    }
}
