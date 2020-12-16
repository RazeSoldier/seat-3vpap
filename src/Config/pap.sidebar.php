<?php

return [
    'pap' => [
        'name' => 'PAP',
        'route_segment' => 'pap',
        'icon' => 'fas fa-user',
        'label' => 'PAP',
        'permission' => 'srp.request',
        'entries' => [
            [
                'name' => 'Show PAP',
                'label' => 'pap::pap.pap',
                'icon' => 'fas fa-user',
                'route' => 'pap.home',
                'permission' => 'srp.request',
            ],
            [
                'name' => 'Fleet Stat',
                'label' => 'pap::stat.fleet-stat',
                'icon' => 'fas fa-key',
                'route' => 'pap.stat',
                'permission' => 'pap.fc',
            ],
        ],
    ],
];
