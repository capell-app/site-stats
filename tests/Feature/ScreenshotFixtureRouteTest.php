<?php

declare(strict_types=1);
use Capell\Admin\Filament\Pages\SiteAdminMetricsPage;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

it('starts the web session before the local metrics fixture logs in its administrator', function (): void {
    require dirname(__DIR__, 2) . '/workbench/routes/screenshot-fixtures.php';
    Route::getRoutes()->refreshNameLookups();

    $route = Route::getRoutes()->getByName('screenshot-fixtures.site-stats.metrics-dashboard');
    expect($route)->not->toBeNull()
        ->and($route?->gatherMiddleware())->toContain('web');
});

it('rejects non-loopback requests before preparing screenshot metrics', function (): void {
    require dirname(__DIR__, 2) . '/workbench/routes/screenshot-fixtures.php';

    $originalEnvironment = app()->environment();
    app()->detectEnvironment(static fn (): string => 'local');

    try {
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
            ->get('/screenshot-fixtures/site-stats/metrics-dashboard')
            ->assertNotFound();
    } finally {
        app()->detectEnvironment(static fn (): string => $originalEnvironment);
    }
});

it('rejects loopback requests outside the local environment', function (): void {
    require dirname(__DIR__, 2) . '/workbench/routes/screenshot-fixtures.php';

    $originalEnvironment = app()->environment();
    app()->detectEnvironment(static fn (): string => 'testing');

    try {
        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->get('/screenshot-fixtures/site-stats/metrics-dashboard')
            ->assertNotFound();
    } finally {
        app()->detectEnvironment(static fn (): string => $originalEnvironment);
    }
});

it('repairs fixture domains before creating pages and remains repeatable', function (): void {
    // The package test harness does not mount the consuming application's panel.
    Route::get('/admin/site-admin-metrics', SiteAdminMetricsPage::class)
        ->name('filament.admin.pages.site-admin-metrics');
    Route::getRoutes()->refreshNameLookups();
    require dirname(__DIR__, 2) . '/workbench/routes/screenshot-fixtures.php';
    $originalEnvironment = app()->environment();
    app()->detectEnvironment(static fn (): string => 'local');
    $userClass = config('auth.providers.users.model');
    if (! is_string($userClass) || ! is_a($userClass, Model::class, true)) {
        throw new RuntimeException('The workbench requires an authenticatable user model.');
    }

    $userClass::query()->firstOrCreate(['email' => 'admin@example.com'], ['name' => 'Screenshot admin', 'password' => bcrypt('screenshot-only')]);
    try {
        foreach (range(1, 2) as $attempt) {
            $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
                ->get('/screenshot-fixtures/site-stats/metrics-dashboard')
                ->assertRedirect('/admin/site-admin-metrics');
        }

        expect(Site::query()->where('name', 'like', 'Site Stats screenshot site %')->count())->toBe(3)
            ->and(Page::query()->where('name', 'like', 'Site Stats screenshot page %')->count())->toBe(12);
    } finally {
        app()->detectEnvironment(static fn (): string => $originalEnvironment);
    }
});
