<?php

declare(strict_types=1);

return [
    'period' => 'Last 7 days',
    'updatedAt' => '24 July 2026 at 02:15 UTC',
    'metrics' => [
        [
            'label' => 'Pages',
            'description' => 'Total non-deleted pages',
            'latest' => 84,
            'change' => '+6 this week',
            'tone' => 'indigo',
            'points' => [72, 74, 76, 78, 79, 82, 84],
        ],
        [
            'label' => 'Sites',
            'description' => 'Total non-deleted sites',
            'latest' => 3,
            'change' => '+1 this week',
            'tone' => 'emerald',
            'points' => [2, 2, 2, 2, 3, 3, 3],
        ],
    ],
    'days' => ['18 Jul', '19 Jul', '20 Jul', '21 Jul', '22 Jul', '23 Jul', '24 Jul'],
];
