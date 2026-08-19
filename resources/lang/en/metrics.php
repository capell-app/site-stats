<?php

declare(strict_types=1);

return [
    'health' => [
        'collector' => [
            'label' => 'Site Stats collector',
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
