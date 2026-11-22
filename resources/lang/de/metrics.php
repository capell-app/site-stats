<?php

declare(strict_types=1);

return [
    'health' => [
        'collection' => [
            'label' => 'Site Stats: Summen werden erfasst',
            'passed' => 'Die heutigen Inhaltssummen wurden innerhalb des letzten Tages erfasst.',
            'failed' => 'In den letzten :hours Stunden wurden keine Inhaltssummen erfasst, daher weisen die Site-Stats-Reihen für diese Tage Lücken auf.',
            'remediation' => 'Stellen Sie sicher, dass der Scheduler jede Minute `php artisan schedule:run` ausführt, und führen Sie dann `php artisan capell:site-stats:collect` aus, um den heutigen Tag zu erfassen. Versäumte Tage können nachträglich nicht mehr ergänzt werden.',
        ],
        'collector' => [
            'label' => 'Site Stats: Statistik-Collector verfügbar',
            'passed' => 'Der datenschutzfreundliche Collector für Inhaltssummen implementiert den Vertrag für tägliche Metriken.',
            'failed' => 'Der Collector für Inhaltssummen implementiert den Vertrag für tägliche Metriken nicht.',
            'remediation' => 'Stellen Sie den Site-Stats-Collector-Vertrag wieder her, bevor Sie die tägliche Metrikerfassung aktivieren.',
        ],
    ],
    'content' => [
        'pages_total' => [
            'label' => 'Seiten',
            'description' => 'Gesamtzahl der nicht gelöschten Seiten.',
        ],
        'sites_total' => [
            'label' => 'Sites',
            'description' => 'Gesamtzahl der nicht gelöschten Sites.',
        ],
        'active_sites_total' => [
            'label' => 'Aktive Sites',
            'description' => 'Aktive, nicht gelöschte Sites.',
        ],
        'active_domains_total' => [
            'label' => 'Aktive Domains',
            'description' => 'Aktive, nicht gelöschte Domains, die aktiven Sites zugeordnet sind.',
        ],
    ],
];
