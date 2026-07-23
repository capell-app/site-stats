<?php

declare(strict_types=1);

use Capell\Core\Data\Metrics\MetricSampleData;
use Capell\Core\Data\Metrics\MetricScopeData;
use Capell\Core\Enums\Metrics\MetricCollectionStatus;
use Capell\SiteStats\Metrics\ContentTotalsMetricsCollector;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

it('collects global content totals without exposing individual content', function (): void {
    $day = CarbonImmutable::parse('2026-07-21', 'UTC');
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
        'name' => 'Metrics Before Cutoff',
        'language_id' => $languageId,
        'blueprint_id' => $siteBlueprintId,
        'theme_id' => $themeId,
        'created_at' => $day->subDay(),
        'updated_at' => $day->subDay(),
    ]);
    DB::table('sites')->insert([
        'name' => 'Metrics After Cutoff',
        'language_id' => $languageId,
        'blueprint_id' => $siteBlueprintId,
        'theme_id' => $themeId,
        'created_at' => $day->addDay(),
        'updated_at' => $day->addDay(),
    ]);
    $layoutId = DB::table('layouts')->insertGetId([
        'name' => 'Metrics layout',
        'site_id' => $siteId,
        'key' => 'metrics-layout',
        'created_at' => $day->subYear(),
        'updated_at' => $day->subYear(),
    ]);

    foreach ([1, 2, 3, 4] as $position) {
        DB::table('pages')->insert([
            'name' => 'Metrics page ' . $position,
            'blueprint_id' => $pageBlueprintId,
            'layout_id' => $layoutId,
            'site_id' => $siteId,
            'created_at' => $position === 4 ? $day->addDay() : $day->subDay(),
            'updated_at' => $day->subDay(),
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
