<?php

declare(strict_types=1);

namespace Capell\SiteStats\Tests;

use Capell\Core\Facades\CapellCore;
use Capell\SiteStats\Providers\SiteStatsServiceProvider;
use Capell\Tests\AbstractTestCase;
use Livewire\LivewireServiceProvider;
use Override;

abstract class SiteStatsTestCase extends AbstractTestCase
{
    protected function getPackageServiceName(): string
    {
        return 'capell-site-stats';
    }

    /** @return class-string[] */
    #[Override]
    protected function getPackageProviders(mixed $app): array
    {
        return [
            ...parent::getPackageProviders($app),
            LivewireServiceProvider::class,
            SiteStatsServiceProvider::class,
        ];
    }

    #[Override]
    protected function getEnvironmentSetUp(mixed $app): void
    {
        parent::getEnvironmentSetUp($app);

        CapellCore::forcePackageInstalled(SiteStatsServiceProvider::$packageName);
    }
}
