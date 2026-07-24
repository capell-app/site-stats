<?php

declare(strict_types=1);

use Capell\Core\Actions\Metrics\RollupDailyMetricsAction;
use Capell\Core\Data\Metrics\MetricScopeData;
use Capell\Core\Models\MetricCollectionRun;
use Capell\Core\Models\MetricDailyRollup;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::get('/screenshot-fixtures/site-stats/metrics-dashboard', static function (): RedirectResponse {
    abort_unless(
        app()->isLocal() && in_array(request()->ip(), ['127.0.0.1', '::1'], true),
        404,
    );

    $firstDay = CarbonImmutable::now('UTC')->startOfDay()->subDays(6);
    $siteDays = [0, 3, 5];
    $sites = [];

    foreach ($siteDays as $index => $dayOffset) {
        $createdAt = $firstDay->addDays($dayOffset)->addHours(9);
        $name = sprintf('Site Stats screenshot site %d', $index + 1);
        $site = Site::query()->where('name', $name)->first();

        if (! $site instanceof Site) {
            $site = Site::factory()->createOne([
                'name' => $name,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        } else {
            $site->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->saveQuietly();
        }

        $sites[] = $site;
    }

    $pageDays = [0, 1, 1, 2, 3, 3, 4, 5, 5, 6, 6, 6];

    foreach ($pageDays as $index => $dayOffset) {
        $createdAt = $firstDay->addDays($dayOffset)->addHours(10);
        $name = sprintf('Site Stats screenshot page %02d', $index + 1);
        $site = $sites[$index % count($sites)];
        $page = Page::query()->where('name', $name)->first();

        if (! $page instanceof Page) {
            Page::factory()
                ->site($site)
                ->createOne([
                    'name' => $name,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

            continue;
        }

        $page->forceFill([
            'site_id' => $site->getKey(),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->saveQuietly();
    }

    $scope = MetricScopeData::global('UTC');
    $rollup = app(RollupDailyMetricsAction::class);

    MetricDailyRollup::query()
        ->where('owner_package', 'capell-app/site-stats')
        ->where('collector_key', 'content_totals')
        ->delete();
    MetricCollectionRun::query()
        ->where('owner_package', 'capell-app/site-stats')
        ->where('collector_key', 'content_totals')
        ->delete();

    for ($dayOffset = 0; $dayOffset <= 6; $dayOffset++) {
        $rollup->execute($firstDay->addDays($dayOffset)->toDateString(), [$scope]);
    }

    $userModel = (string) config('auth.providers.users.model');
    $email = (string) env('CAPELL_SCREENSHOT_ADMIN_EMAIL', 'admin@example.com');
    /** @var Authenticatable $user */
    $user = $userModel::query()->where('email', $email)->firstOrFail();

    auth()->login($user);

    if (request()->hasSession()) {
        request()->session()->regenerate();
        request()->session()->put(
            'password_hash_' . ((string) config('auth.defaults.guard', 'web')),
            $user->getAuthPassword(),
        );
    }

    return redirect()->route('filament.admin.pages.site-admin-metrics');
})
    ->name('screenshot-fixtures.site-stats.metrics-dashboard');
