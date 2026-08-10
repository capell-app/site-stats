<?php

declare(strict_types=1);

use Capell\Core\Data\Metrics\MetricSampleData;
use Capell\Core\Data\Metrics\MetricScopeData;
use Capell\Core\Enums\Metrics\MetricBackfillPolicy;
use Capell\Core\Enums\Metrics\MetricCollectionStatus;
use Capell\SiteStats\Health\SiteStatsHealthCheck;
use Capell\SiteStats\Metrics\ContentTotalsMetricsCollector;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

it('reports a healthy collector contract', function (): void {
    expect(SiteStatsHealthCheck::passed())->toBeTrue()
        ->and(SiteStatsHealthCheck::runDiagnostics())->toHaveCount(1);
});

it('publishes current-day-only global metric definitions', function (): void {
    $definitions = resolve(ContentTotalsMetricsCollector::class)->definitions();

    expect($definitions)->toHaveCount(2);

    foreach ($definitions as $definition) {
        expect($definition->semantics->backfillPolicy)->toBe(MetricBackfillPolicy::CurrentDayOnly);
    }
});

it(
    'collects only for the current UTC day and exact global midnight scope',
    function (string $day, MetricScopeData ...$scopes): void {
        CarbonImmutable::setTestNow('2026-07-21 12:00:00 UTC');
        $result = resolve(ContentTotalsMetricsCollector::class)->collect($day, array_values($scopes));

        expect($result->status)->toBe(MetricCollectionStatus::Unsupported)
            ->and($result->samples)->toBe([])
            ->and($result->reason)->toBe('Content totals support the current UTC day and exact global midnight scope only.');
    },
)->with([
    'historical day' => ['2026-07-20', MetricScopeData::global('UTC')],
    'no scopes' => ['2026-07-21'],
    'non-UTC global scope' => ['2026-07-21', MetricScopeData::global('Europe/London')],
    'non-midnight global scope' => ['2026-07-21', MetricScopeData::global('UTC', '04:00:00')],
    'mixed supported and unsupported scopes' => [
        '2026-07-21',
        MetricScopeData::global('UTC'),
        MetricScopeData::global('Europe/London'),
    ],
]);

it('collects global content totals without exposing individual content', function (): void {
    $day = CarbonImmutable::parse('2026-07-21', 'UTC');
    CarbonImmutable::setTestNow($day->addHours(12));
    $languageId = DB::table('languages')->insertGetId([
        'name' => 'English',
        'code' => 'en',
        'locale' => 'en_GB',
        'created_at' => $day->subYear(),
        'updated_at' => $day->subYear(),
    ]);
    $siteBlueprintId = DB::table('blueprints')->insertGetId([
        'name' => 'Site',
        'type' => 'site',
        'key' => 'site',
        'created_at' => $day->subYear(),
        'updated_at' => $day->subYear(),
    ]);
    $themeBlueprintId = DB::table('blueprints')->insertGetId([
        'name' => 'Theme',
        'type' => 'theme',
        'key' => 'theme',
        'created_at' => $day->subYear(),
        'updated_at' => $day->subYear(),
    ]);
    $pageBlueprintId = DB::table('blueprints')->insertGetId([
        'name' => 'Page',
        'type' => 'page',
        'key' => 'page',
        'created_at' => $day->subYear(),
        'updated_at' => $day->subYear(),
    ]);
    $themeId = DB::table('themes')->insertGetId([
        'name' => 'Metrics theme',
        'blueprint_id' => $themeBlueprintId,
        'key' => 'metrics-theme',
        'created_at' => $day->subYear(),
        'updated_at' => $day->subYear(),
    ]);
    $siteId = DB::table('sites')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'name' => 'Metrics Before Cutoff',
        'language_id' => $languageId,
        'blueprint_id' => $siteBlueprintId,
        'theme_id' => $themeId,
        'created_at' => $day->subDay(),
        'updated_at' => $day->subDay(),
        'deleted_at' => $day->addDay(),
    ]);
    DB::table('sites')->insert([
        'uuid' => (string) Str::uuid(),
        'name' => 'Metrics After Cutoff',
        'language_id' => $languageId,
        'blueprint_id' => $siteBlueprintId,
        'theme_id' => $themeId,
        'created_at' => $day->addDay(),
        'updated_at' => $day->addDay(),
    ]);
    DB::table('sites')->insert([
        'uuid' => (string) Str::uuid(),
        'name' => 'Metrics Deleted Before Cutoff',
        'language_id' => $languageId,
        'blueprint_id' => $siteBlueprintId,
        'theme_id' => $themeId,
        'created_at' => $day->subDay(),
        'updated_at' => $day->subDay(),
        'deleted_at' => $day,
    ]);
    $layoutId = DB::table('layouts')->insertGetId([
        'name' => 'Metrics layout',
        'site_id' => $siteId,
        'key' => 'metrics-layout',
        'created_at' => $day->subYear(),
        'updated_at' => $day->subYear(),
    ]);

    foreach ([1, 2, 3, 4, 5] as $position) {
        DB::table('pages')->insert([
            'name' => 'Metrics page ' . $position,
            'blueprint_id' => $pageBlueprintId,
            'layout_id' => $layoutId,
            'site_id' => $siteId,
            'created_at' => $position === 5 ? $day->addDay() : $day->subDay(),
            'updated_at' => $day->subDay(),
            'deleted_at' => match ($position) {
                3 => $day->addDay(),
                4 => $day,
                default => null,
            },
            '_lft' => ($position * 2) - 1,
            '_rgt' => $position * 2,
            'depth' => 0,
        ]);
    }

    $result = resolve(ContentTotalsMetricsCollector::class)->collect(
        $day->toDateString(),
        [MetricScopeData::global('UTC')],
    );

    $samples = collect($result->samples)->keyBy(
        static fn (MetricSampleData $sample): string => $sample->identity->metricKey,
    );
    $pagesSample = $samples->get('content.pages_total');
    $sitesSample = $samples->get('content.sites_total');

    expect($result->status)->toBe(MetricCollectionStatus::Complete);
    expect($pagesSample)->toBeInstanceOf(MetricSampleData::class);
    expect($sitesSample)->toBeInstanceOf(MetricSampleData::class);

    throw_unless($pagesSample instanceof MetricSampleData);
    throw_unless($sitesSample instanceof MetricSampleData);

    expect($pagesSample->value->integer)->toBe(3)
        ->and($sitesSample->value->integer)->toBe(1);
});
