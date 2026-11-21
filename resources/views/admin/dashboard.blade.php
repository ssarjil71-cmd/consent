@extends('admin.layout.master')

@section('title', 'Dashboard')

@section('content')
@php
    $admin = auth('admin')->user();

    $cards = [
        [
            'permission' => 'admin.consents.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => "Today's Consents",
            'value' => $data['todayConsent'],
            'subtitle' => 'Updated just now',
            'icon' => 'fas fa-calendar-day',
            'badge' => 'bg-primary bg-opacity-10 text-primary',
            'route' => ['name' => 'admin.consents.index', 'params' => ['filter' => 'today']],
        ],
        [
            'permission' => 'admin.consents.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => 'Total Consents',
            'value' => $data['totalConsent'],
            'subtitle' => 'All time',
            'icon' => 'fas fa-file-contract',
            'badge' => 'bg-success bg-opacity-10 text-success',
            'route' => ['name' => 'admin.consents.index'],
        ],
        [
            'permission' => 'admin.consents.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => 'Pending',
            'value' => $data['pendingConsent'],
            'subtitle' => 'Awaiting approval',
            'icon' => 'fas fa-clock',
            'badge' => 'bg-warning bg-opacity-10 text-warning',
            'route' => ['name' => 'admin.consents.index', 'params' => ['filter' => 'pending']],
        ],
        [
            'permission' => 'admin.consents.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => 'Approved',
            'value' => $data['approvedConsent'],
            'subtitle' => 'Successfully processed',
            'icon' => 'fas fa-check-circle',
            'badge' => 'bg-success bg-opacity-10 text-success',
            'route' => ['name' => 'admin.consents.index', 'params' => ['filter' => 'approved']],
        ],
        [
            'permission' => 'admin.consents.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => 'Rejected',
            'value' => $data['rejectedConsent'],
            'subtitle' => 'Not approved',
            'icon' => 'fas fa-times-circle',
            'badge' => 'bg-danger bg-opacity-10 text-danger',
            'route' => ['name' => 'admin.consents.index', 'params' => ['filter' => 'rejected']],
        ],
        [
            'permission' => 'admin.organizations.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => 'Organizations',
            'value' => $data['totalOrganizations'],
            'subtitle' => 'Active organizations',
            'icon' => 'fas fa-building',
            'badge' => 'bg-info bg-opacity-10 text-info',
            'route' => ['name' => 'admin.organizations.index'],
        ],
        [
            'permission' => 'admin.transactions.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => "Today's Transactions",
            'value' => $data['todayTransaction'],
            'subtitle' => 'Total today',
            'icon' => 'fas fa-rupee-sign',
            'badge' => 'bg-primary bg-opacity-10 text-primary',
            'route' => ['name' => 'admin.transactions.index', 'params' => ['filter' => 'today']],
        ],
        [
            'permission' => 'admin.transactions.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => 'Total Transactions',
            'value' => $data['totalTransaction'],
            'subtitle' => 'All time',
            'icon' => 'fas fa-money-bill-wave',
            'badge' => 'bg-success bg-opacity-10 text-success',
            'route' => ['name' => 'admin.transactions.index'],
        ],
        [
            'permission' => 'admin.team-members.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => 'Team Members',
            'value' => $data['teamMembers'],
            'subtitle' => 'Active admins',
            'icon' => 'fas fa-users',
            'badge' => 'bg-info bg-opacity-10 text-info',
        ],
        [
            'permission' => 'admin.organizations.view',
            'col' => 'col-md-6 col-lg-3',
            'title' => 'New Organizations',
            'value' => $data['newOrganizations'],
            'subtitle' => 'Last 7 days',
            'icon' => 'fas fa-chart-line',
            'badge' => 'bg-warning bg-opacity-10 text-warning',
        ],
    ];
@endphp

<div class="row g-4">
    @foreach ($cards as $card)
        @continue(!$admin || !$admin->hasPermission($card['permission']))

        <div class="{{ $card['col'] }}">
            @php
                $url = null;

                if (!empty($card['route'])) {
                    $url = route($card['route']['name'], $card['route']['params'] ?? []);
                }
            @endphp

            @if ($url)
                <a href="{{ $url }}" class="text-decoration-none text-reset">
            @endif
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title mb-0">{{ $card['title'] }}</h6>
                                <span class="badge {{ $card['badge'] }} p-2">
                                    <i class="{{ $card['icon'] }}"></i>
                                </span>
                            </div>
                            <h2 class="mb-1">{{ $card['value'] }}</h2>
                            <div class="small text-muted">{{ $card['subtitle'] }}</div>
                        </div>
                    </div>
            @if ($url)
                </a>
            @endif
        </div>
    @endforeach
</div>

<!-- Add some spacing at the bottom -->
<div class="mt-4"></div>
</div>
@endsection
