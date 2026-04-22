<?php

namespace App\Support;

class DashboardUiData
{
    public static function notifications(): array
    {
        return [
            [
                'iconClass' => 'btn-danger',
                'icon' => 'airplay',
                'title' => 'Launch Admin',
                'message' => 'Just see my new admin!',
                'time' => '9:30 AM',
            ],
            [
                'iconClass' => 'btn-success',
                'icon' => 'calendar',
                'title' => 'Event today',
                'message' => 'Reminder: you have an event today.',
                'time' => '9:10 AM',
            ],
            [
                'iconClass' => 'btn-info',
                'icon' => 'settings',
                'title' => 'Settings',
                'message' => 'Customize this template as needed.',
                'time' => '9:08 AM',
            ],
        ];
    }

    public static function menuGroups(): array
    {
        return [
            [
                'title' => 'Main',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'icon' => 'home',
                        'url' => route('dashboard'),
                        'route' => 'dashboard',
                    ],
                ],
            ],
            [
                'title' => 'Data Master',
                'items' => [
                    [
                        'label' => 'Master Data',
                        'icon' => 'database',
                        'children' => [
                            [
                                'label' => 'Unit Kerja',
                                'url' => route('unit-kerjas.index'),
                                'route' => 'unit-kerjas.*',
                            ],
                            [
                                'label' => 'Golongan',
                                'url' => route('golongans.index'),
                                'route' => 'golongans.*',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
