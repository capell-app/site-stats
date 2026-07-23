<?php

declare(strict_types=1);

use Capell\SiteStats\Tests\SiteStatsTestCase;

require_once __DIR__ . '/SiteStatsTestCase.php';

pest()->extend(SiteStatsTestCase::class)->group('site-stats')->in('.');
