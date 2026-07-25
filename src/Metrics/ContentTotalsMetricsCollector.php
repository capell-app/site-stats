<?php

declare(strict_types=1);

namespace Capell\SiteStats\Metrics;

use Capell\Core\Contracts\Metrics\CollectsDailyMetrics;
use Capell\Core\Data\Metrics\MetricCollectionResultData;
use Capell\Core\Data\Metrics\MetricDefinitionData;
use Capell\Core\Data\Metrics\MetricGovernanceData;
use Capell\Core\Data\Metrics\MetricIdentityData;
use Capell\Core\Data\Metrics\MetricRepresentationData;
use Capell\Core\Data\Metrics\MetricSampleData;
use Capell\Core\Data\Metrics\MetricScopeData;
use Capell\Core\Data\Metrics\MetricSemanticsData;
use Capell\Core\Data\Metrics\MetricValueData;
use Capell\Core\Enums\Metrics\MetricAggregation;
use Capell\Core\Enums\Metrics\MetricBackfillPolicy;
use Capell\Core\Enums\Metrics\MetricCollectionStatus;
use Capell\Core\Enums\Metrics\MetricGapPolicy;
use Capell\Core\Enums\Metrics\MetricScopeType;
use Capell\Core\Enums\Metrics\MetricSemantic;
use Capell\Core\Enums\Metrics\MetricSensitivity;
use Capell\Core\Enums\Metrics\MetricSource;
use Capell\Core\Enums\Metrics\MetricValueType;
use Capell\Core\Enums\Metrics\MetricVisibility;
use Capell\Core\Enums\MetricUnitEnum;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Carbon\CarbonImmutable;

final class ContentTotalsMetricsCollector implements CollectsDailyMetrics
{
    private const string Owner = 'capell-app/site-stats';

    private const string Collector = 'content_totals';

    /** @return list<MetricDefinitionData> */
    public function definitions(): array
    {
        return [
            $this->definition(
                'content.pages_total',
                __('capell-site-stats::metrics.content.pages_total.label'),
                __('capell-site-stats::metrics.content.pages_total.description'),
            ),
            $this->definition(
                'content.sites_total',
                __('capell-site-stats::metrics.content.sites_total.label'),
                __('capell-site-stats::metrics.content.sites_total.description'),
            ),
        ];
    }

    /**
     * @param  list<MetricScopeData>  $scopes
     */
    public function collect(string $day, array $scopes): MetricCollectionResultData
    {
        $globalScopes = array_values(array_filter(
            $scopes,
            static fn (MetricScopeData $scope): bool => $scope->type === MetricScopeType::Global
                && $scope->timezone === 'UTC'
                && $scope->dayStartsAt === '00:00:00',
        ));

        if ($globalScopes === []
            || count($globalScopes) !== count($scopes)
            || $day !== CarbonImmutable::now('UTC')->toDateString()) {
            return new MetricCollectionResultData(
                MetricCollectionStatus::Unsupported,
                $day,
                [],
                [],
                null,
                null,
                'Content totals support the current UTC day and exact global midnight scope only.',
            );
        }

        $endOfDay = CarbonImmutable::parse($day, 'UTC')->endOfDay();
        $values = [
            'content.pages_total' => Page::query()
                ->withTrashed()
                ->where('created_at', '<=', $endOfDay)
                ->where(static function ($query) use ($endOfDay): void {
                    $query->whereNull('deleted_at')->orWhere('deleted_at', '>', $endOfDay);
                })
                ->count(),
            'content.sites_total' => Site::query()
                ->withTrashed()
                ->where('created_at', '<=', $endOfDay)
                ->where(static function ($query) use ($endOfDay): void {
                    $query->whereNull('deleted_at')->orWhere('deleted_at', '>', $endOfDay);
                })
                ->count(),
        ];
        $definitions = collect($this->definitions())->keyBy(
            static fn (MetricDefinitionData $definition): string => $definition->identity->metricKey,
        );
        $samples = [];

        foreach ($globalScopes as $scope) {
            foreach ($values as $metric => $value) {
                /** @var MetricDefinitionData $definition */
                $definition = $definitions->get($metric);
                $samples[] = new MetricSampleData(
                    identity: $definition->identity,
                    definitionHash: $definition->semanticHash(),
                    day: $day,
                    scope: $scope,
                    representation: $definition->representation,
                    value: MetricValueData::integer($value),
                );
            }
        }

        $sourcePayload = json_encode($values, JSON_THROW_ON_ERROR);

        return new MetricCollectionResultData(
            MetricCollectionStatus::Complete,
            $day,
            $globalScopes,
            $samples,
            'database:created-at:' . $day,
            hash('sha256', $sourcePayload),
            null,
        );
    }

    private function definition(string $metric, string $label, string $description): MetricDefinitionData
    {
        return new MetricDefinitionData(
            identity: new MetricIdentityData(self::Owner, self::Collector, $metric),
            representation: new MetricRepresentationData(MetricUnitEnum::Count, MetricValueType::Integer),
            scopeType: MetricScopeType::Global,
            semantics: new MetricSemanticsData(
                MetricSemantic::Gauge,
                MetricAggregation::Last,
                MetricGapPolicy::Missing,
                MetricBackfillPolicy::CurrentDayOnly,
            ),
            governance: new MetricGovernanceData(
                MetricSource::Database,
                'core.content-tables',
                MetricSensitivity::Internal,
                MetricVisibility::SiteAdmin,
            ),
            labels: ['en' => $label],
            descriptions: ['en' => $description],
        );
    }
}
