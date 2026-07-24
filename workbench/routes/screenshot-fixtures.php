<?php

declare(strict_types=1);

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View as ViewFacade;

$siteStatsMetricsDashboardData = require dirname(__DIR__) . '/data/metrics-dashboard.php';

Route::get('/screenshot-fixtures/site-stats/metrics-dashboard', static fn (): View => ViewFacade::file(
    dirname(__DIR__) . '/resources/views/metrics-dashboard.blade.php',
    $siteStatsMetricsDashboardData,
))->name('screenshot-fixtures.site-stats.metrics-dashboard');
