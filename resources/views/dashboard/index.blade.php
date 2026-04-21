@extends('layouts.dashboard')

@section('title', 'Dashboard Proyeksi Jabatan')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Good Morning Admin!</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-5 align-self-center">
                <div class="customize-input float-end">
                    <select
                        class="custom-select custom-select-set form-control bg-white border-0 custom-shadow custom-radius">
                        <option selected>Aug 23</option>
                        <option value="1">July 23</option>
                        <option value="2">Jun 23</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            @foreach ($summaryCards as $card)
                <div class="col-sm-6 col-lg-3">
                    <div class="card {{ !$loop->last ? 'border-end' : '' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="d-inline-flex align-items-center">
                                        <h2 class="text-dark mb-1 font-weight-medium">{{ $card['value'] }}</h2>
                                        @if ($card['badge'])
                                            <span
                                                class="badge {{ $card['badgeClass'] }} font-12 text-white font-weight-medium rounded-pill ms-2 d-lg-block d-md-none">
                                                {{ $card['badge'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">{{ $card['title'] }}
                                    </h6>
                                </div>
                                <div class="ms-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="{{ $card['icon'] }}"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Total Sales</h4>
                        <div id="campaign-v2" class="mt-2" style="height:283px; width:100%;"></div>
                        <ul class="list-style-none mb-0">
                            @foreach ($salesByChannel as $item)
                                <li class="{{ !$loop->first ? 'mt-3' : '' }}">
                                    <i class="fas fa-circle {{ $item['color'] }} font-10 me-2"></i>
                                    <span class="text-muted">{{ $item['label'] }}</span>
                                    <span class="text-dark float-end font-weight-medium">{{ $item['amount'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Net Income</h4>
                        <div class="net-income mt-4 position-relative" style="height:294px;"></div>
                        <ul class="list-inline text-center mt-5 mb-2">
                            <li class="list-inline-item text-muted fst-italic">Sales for this month</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Earning by Location</h4>
                        <div style="height:180px">
                            <div id="visitbylocate" style="height:100%"></div>
                        </div>

                        @foreach ($locationEarnings as $item)
                            <div class="row mb-3 align-items-center {{ $loop->first ? 'mt-1 mt-5' : '' }}">
                                <div class="col-4 text-end">
                                    <span class="text-muted font-14">{{ $item['country'] }}</span>
                                </div>
                                <div class="col-5">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar {{ $item['progressClass'] }}" role="progressbar"
                                            style="width: {{ $item['progress'] }}%"
                                            aria-valuenow="{{ $item['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 text-end">
                                    <span class="mb-0 font-14 text-dark font-weight-medium">{{ $item['value'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <h4 class="card-title mb-0">Earning Statistics</h4>
                            <div class="ms-auto">
                                <div class="dropdown sub-dropdown">
                                    <button class="btn btn-link text-muted dropdown-toggle" type="button" id="statsMenu"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="statsMenu">
                                        <a class="dropdown-item" href="#">Insert</a>
                                        <a class="dropdown-item" href="#">Update</a>
                                        <a class="dropdown-item" href="#">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pl-4 mb-5">
                            <div class="stats ct-charts position-relative" style="height: 315px;"></div>
                        </div>
                        <ul class="list-inline text-center mt-4 mb-0">
                            <li class="list-inline-item text-muted fst-italic">Earnings for this month</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Recent Activity</h4>
                        <div class="mt-4 activity">
                            @foreach ($activities as $activity)
                                <div
                                    class="d-flex align-items-start {{ !$loop->last ? 'border-left-line pb-3' : 'border-left-line' }}">
                                    <div>
                                        <a href="javascript:void(0)"
                                            class="btn {{ $activity['buttonClass'] }} btn-circle mb-2 btn-item">
                                            <i data-feather="{{ $activity['icon'] }}"></i>
                                        </a>
                                    </div>
                                    <div class="ms-3 mt-2">
                                        <h5 class="text-dark font-weight-medium mb-2">{{ $activity['title'] }}</h5>
                                        <p class="font-14 mb-2 text-muted">{{ $activity['description'] }}</p>
                                        <span
                                            class="font-weight-light font-14 mb-1 d-block text-muted">{{ $activity['time'] }}</span>
                                        @if ($loop->last)
                                            <a href="javascript:void(0)"
                                                class="font-14 border-bottom pb-1 border-info">Load More</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title">Top Leaders</h4>
                            <div class="ms-auto">
                                <div class="dropdown sub-dropdown">
                                    <button class="btn btn-link text-muted dropdown-toggle" type="button"
                                        id="leadersMenu" data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i data-feather="more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="leadersMenu">
                                        <a class="dropdown-item" href="#">Insert</a>
                                        <a class="dropdown-item" href="#">Update</a>
                                        <a class="dropdown-item" href="#">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead>
                                    <tr class="border-0">
                                        <th class="border-0 font-14 font-weight-medium text-muted">Team Lead</th>
                                        <th class="border-0 font-14 font-weight-medium text-muted px-2">Project</th>
                                        <th class="border-0 font-14 font-weight-medium text-muted">Team</th>
                                        <th class="border-0 font-14 font-weight-medium text-muted text-center">Status</th>
                                        <th class="border-0 font-14 font-weight-medium text-muted text-center">Weeks</th>
                                        <th class="border-0 font-14 font-weight-medium text-muted">Budget</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leaders as $leader)
                                        <tr>
                                            <td class="{{ $loop->last ? 'border-bottom-0' : 'border-top-0' }} px-2 py-4">
                                                <div class="d-flex no-block align-items-center">
                                                    <div class="me-3">
                                                        <img src="{{ asset('assets/images/users/' . $leader['avatar']) }}"
                                                            alt="user" class="rounded-circle" width="45"
                                                            height="45" />
                                                    </div>
                                                    <div>
                                                        <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                            {{ $leader['name'] }}</h5>
                                                        <span class="text-muted font-14">{{ $leader['email'] }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="{{ $loop->last ? 'border-bottom-0' : 'border-top-0' }} text-muted px-2 py-4 font-14">
                                                {{ $leader['project'] }}
                                            </td>
                                            <td class="{{ $loop->last ? 'border-bottom-0' : 'border-top-0' }} px-2 py-4">
                                                <div class="popover-icon">
                                                    <a class="btn btn-primary rounded-circle btn-circle font-12"
                                                        href="javascript:void(0)">DS</a>
                                                    <a class="btn btn-danger rounded-circle btn-circle font-12 popover-item"
                                                        href="javascript:void(0)">SS</a>
                                                    <a class="btn btn-cyan rounded-circle btn-circle font-12 popover-item"
                                                        href="javascript:void(0)">RP</a>
                                                </div>
                                            </td>
                                            <td
                                                class="{{ $loop->last ? 'border-bottom-0' : 'border-top-0' }} text-center px-2 py-4">
                                                <i class="fa fa-circle {{ $leader['statusClass'] }} font-12"
                                                    data-bs-toggle="tooltip" data-placement="top"
                                                    title="{{ $leader['statusTitle'] }}"></i>
                                            </td>
                                            <td
                                                class="{{ $loop->last ? 'border-bottom-0' : 'border-top-0' }} text-center text-muted font-weight-medium px-2 py-4">
                                                {{ $leader['weeks'] }}
                                            </td>
                                            <td
                                                class="{{ $loop->last ? 'border-bottom-0' : 'border-top-0' }} font-weight-medium text-dark px-2 py-4">
                                                {{ $leader['budget'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
