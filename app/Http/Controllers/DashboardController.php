<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $summaryCards = [
            [
                'value' => '236',
                'title' => 'New Clients',
                'badge' => '+18.33%',
                'badgeClass' => 'bg-primary',
                'icon' => 'user-plus',
            ],
            [
                'value' => '$18,306',
                'title' => 'Earnings of Month',
                'badge' => null,
                'badgeClass' => '',
                'icon' => 'dollar-sign',
            ],
            [
                'value' => '1538',
                'title' => 'New Projects',
                'badge' => '-18.33%',
                'badgeClass' => 'bg-danger',
                'icon' => 'file-plus',
            ],
            [
                'value' => '864',
                'title' => 'Projects',
                'badge' => null,
                'badgeClass' => '',
                'icon' => 'globe',
            ],
        ];

        $salesByChannel = [
            ['color' => 'text-primary', 'label' => 'Direct Sales', 'amount' => '$2346'],
            ['color' => 'text-danger', 'label' => 'Referral Sales', 'amount' => '$2108'],
            ['color' => 'text-cyan', 'label' => 'Affiliate Sales', 'amount' => '$1204'],
        ];

        $locationEarnings = [
            ['country' => 'India', 'progressClass' => 'bg-primary', 'progress' => 100, 'value' => '28%'],
            ['country' => 'UK', 'progressClass' => 'bg-danger', 'progress' => 74, 'value' => '21%'],
            ['country' => 'USA', 'progressClass' => 'bg-cyan', 'progress' => 60, 'value' => '18%'],
            ['country' => 'China', 'progressClass' => 'bg-success', 'progress' => 50, 'value' => '12%'],
        ];

        $activities = [
            [
                'buttonClass' => 'btn-info',
                'icon' => 'shopping-cart',
                'title' => 'New Product Sold!',
                'description' => 'John Musa just purchased Cannon 5M Camera.',
                'time' => '10 Minutes Ago',
            ],
            [
                'buttonClass' => 'btn-danger',
                'icon' => 'message-square',
                'title' => 'New Support Ticket',
                'description' => 'Richardson just created support ticket.',
                'time' => '25 Minutes Ago',
            ],
            [
                'buttonClass' => 'btn-cyan',
                'icon' => 'bell',
                'title' => 'Notification Pending Order',
                'description' => 'One pending order from Ryne Doe.',
                'time' => '2 Hours Ago',
            ],
        ];

        $leaders = [
            [
                'name' => 'Hanna Gover',
                'email' => 'hgover@gmail.com',
                'project' => 'Elite Admin',
                'statusClass' => 'text-primary',
                'statusTitle' => 'In Testing',
                'weeks' => 35,
                'budget' => '$96K',
                'avatar' => 'widget-table-pic1.jpg',
            ],
            [
                'name' => 'Daniel Kristeen',
                'email' => 'kristeen@gmail.com',
                'project' => 'Real Homes WP Theme',
                'statusClass' => 'text-success',
                'statusTitle' => 'Done',
                'weeks' => 32,
                'budget' => '$85K',
                'avatar' => 'widget-table-pic2.jpg',
            ],
            [
                'name' => 'Julian Josephs',
                'email' => 'josephs@gmail.com',
                'project' => 'MedicalPro WP Theme',
                'statusClass' => 'text-primary',
                'statusTitle' => 'Done',
                'weeks' => 29,
                'budget' => '$81K',
                'avatar' => 'widget-table-pic3.jpg',
            ],
            [
                'name' => 'Jan Petrovic',
                'email' => 'jan.petrovic@gmail.com',
                'project' => 'Hosting Press HTML',
                'statusClass' => 'text-danger',
                'statusTitle' => 'In Progress',
                'weeks' => 23,
                'budget' => '$80K',
                'avatar' => 'widget-table-pic4.jpg',
            ],
        ];

        $notifications = [
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

        $menuGroups = [
            [
                'title' => 'Main',
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'home', 'url' => route('dashboard')],
                    ['label' => 'Ticket List', 'icon' => 'tag', 'url' => 'javascript:void(0)'],
                    ['label' => 'Chat', 'icon' => 'message-square', 'url' => 'javascript:void(0)'],
                    ['label' => 'Calendar', 'icon' => 'calendar', 'url' => 'javascript:void(0)'],
                ],
            ],
            [
                'title' => 'Reports',
                'items' => [
                    ['label' => 'Sales', 'icon' => 'bar-chart', 'url' => 'javascript:void(0)'],
                    ['label' => 'Projects', 'icon' => 'file-text', 'url' => 'javascript:void(0)'],
                    ['label' => 'Teams', 'icon' => 'users', 'url' => 'javascript:void(0)'],
                ],
            ],
        ];

        return view('dashboard.index', compact(
            'summaryCards',
            'salesByChannel',
            'locationEarnings',
            'activities',
            'leaders',
            'notifications',
            'menuGroups'
        ));
    }
}
