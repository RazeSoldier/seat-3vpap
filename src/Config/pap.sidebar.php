<?php

return [
    'pap' => [
        'name' => 'PAP',
        'route_segment' => 'pap',
        'icon' => 'fa-user',
        'label' => 'PAP',
        'permission' => 'srp.request',
        'entries' => [
            [
                'name' => 'Show PAP',
                'label' => 'pap::pap.pap',
                'icon' => 'fa-user',
                'route' => 'pap.home',
                'permission' => 'srp.request',
            ],
            [
                'name' => 'Fleet Stat',
                'label' => 'pap::stat.fleet-stat',
                'icon' => 'fa-key',
                'route' => 'pap.stat',
                'permission' => 'pap.admin',
            ],
        ],
    ],
];
