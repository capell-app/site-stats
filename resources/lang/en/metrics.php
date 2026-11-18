<?php

declare(strict_types=1);

return [
    'health' => [
        'collection' => [
            'label' => 'Site Stats: totals are being recorded',
            'passed' => 'Today\'s content totals were recorded within the last day.',
            'failed' => 'No content totals have been recorded in the last :hours hours, so the Site Stats series have gaps for those days.',
            'remediation' => 'Make sure the scheduler runs `php artisan schedule:run` every minute, then run `php artisan capell:site-stats:collect` to record today. Days that were missed cannot be filled in later.',
        ],
        'collector' => [
            'label' => 'Site Stats: statistics collector available',
            'passed' => 'The privacy-safe content totals collector implements the daily metrics contract.',
            'failed' => 'The content totals collector does not implement the daily metrics contract.',
            'remediation' => 'Restore the Site Stats collector contract before enabling daily metric collection.',
        ],
    ],
    'content' => [
        'pages_total' => [
            'label' => 'Pages',
            'description' => 'Total non-deleted pages.',
        ],
        'sites_total' => [
            'label' => 'Sites',
            'description' => 'Total non-deleted sites.',
        ],
        'active_sites_total' => [
            'label' => 'Active sites',
            'description' => 'Active, non-deleted sites.',
        ],
        'active_domains_total' => [
            'label' => 'Active domains',
            'description' => 'Active, non-deleted domains attached to active sites.',
        ],
    ],
];
