<?php

declare(strict_types=1);

return [
    'health' => [
        'collector' => [
            'label' => 'Metric Trends collector',
            'passed' => 'The privacy-safe content totals collector implements the daily metrics contract.',
            'failed' => 'The content totals collector does not implement the daily metrics contract.',
            'remediation' => 'Restore the Metric Trends collector contract before enabling daily metric collection.',
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
    ],
];
