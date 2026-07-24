<?php

declare(strict_types=1);

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
