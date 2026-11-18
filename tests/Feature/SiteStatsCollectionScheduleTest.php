<?php

declare(strict_types=1);

use Capell\Core\Enums\Metrics\MetricCollectionRunStatus;
use Capell\Core\Models\MetricCollectionRun;
use Capell\Core\Models\MetricDailyRollup;
use Capell\SiteStats\Health\SiteStatsHealthCheck;
use Carbon\CarbonImmutable;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

it('collects the four content totals for the current UTC day', function (): void {
    CarbonImmutable::setTestNow('2026-07-21 12:00:00 UTC');

    $this->artisan('capell:site-stats:collect')->assertSuccessful();

    $rollups = MetricDailyRollup::query()
        ->where('owner_package', 'capell-app/site-stats')
        ->where('collector_key', 'content_totals')
        ->whereDate('day', '2026-07-21')
        ->pluck('metric_key')
        ->sort()
        ->values()
        ->all();

    expect($rollups)->toBe([
        'content.active_domains_total',
        'content.active_sites_total',
        'content.pages_total',
        'content.sites_total',
    ])->and(MetricCollectionRun::query()->where('owner_package', 'capell-app/site-stats')->sole()->status)
        ->toBe(MetricCollectionRunStatus::Completed);
});

it('collects again later the same day, replacing that day\'s totals', function (): void {
    CarbonImmutable::setTestNow('2026-07-21 09:00:00 UTC');
    $this->artisan('capell:site-stats:collect')->assertSuccessful();
    CarbonImmutable::setTestNow('2026-07-21 23:00:00 UTC');
    $this->artisan('capell:site-stats:collect')->assertSuccessful();

    expect(MetricDailyRollup::query()->where('owner_package', 'capell-app/site-stats')->count())->toBe(4);
});

it('schedules the collection hourly so a normal install records each day', function (): void {
    $events = collect(resolve(Schedule::class)->events())
        ->filter(static fn (Event $event): bool => str_contains((string) $event->command, 'capell:site-stats:collect'));

    expect($events)->toHaveCount(1)
        ->and($events->sole()->expression)->toBe('0 * * * *')
        ->and($events->sole()->withoutOverlapping)->toBeTrue()
        ->and($events->sole()->onOneServer)->toBeTrue();
});

it('fails the health check until the collector has run, and again once it stops running', function (): void {
    CarbonImmutable::setTestNow('2026-07-21 12:00:00 UTC');

    expect(SiteStatsHealthCheck::passed())->toBeFalse();

    $this->artisan('capell:site-stats:collect')->assertSuccessful();

    expect(SiteStatsHealthCheck::passed())->toBeTrue();

    CarbonImmutable::setTestNow('2026-07-22 15:00:00 UTC');

    expect(SiteStatsHealthCheck::passed())->toBeFalse()
        ->and(SiteStatsHealthCheck::runDiagnostics()->firstWhere('passed', false)?->remediation)
        ->toContain('capell:site-stats:collect');
});
