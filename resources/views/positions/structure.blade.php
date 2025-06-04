@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => backpack_url('dashboard'),
        'Struktur Organisasi' => false,
    ];

    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp


@section('header')
    <div class="container-fluid d-flex justify-content-between my-3">
        <section class="header-operation animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
            <h1 class="text-capitalize mb-0" bp-section="page-heading">Struktur Organisasi</h1>
            <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">Data posisi hierarki di desa</p>
            {{-- @if ($crud->hasAccess('list')) --}}
                <p class="ms-2 ml-2 mb-0" bp-section="page-subheading-back-button">
                    <small><a href="{{ backpack_url('position')}}" class="font-sm"><i class="la la-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span>position</span></a></small>
                </p>
            {{-- @endif --}}
        </section>
        {{-- <a href="javascript: window.print();" class="btn float-end float-right"><i class="la la-print"></i></a> --}}
    </div>
@endsection

@section('before_styles')
    <style>
        .tree {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            flex-direction: column;
            padding-left: 20px;
        }

        .tree ul {
            padding-left: 20px;
            list-style-type: none;
            position: relative;
        }

        .tree ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            border-left: 2px solid #ccc;
            bottom: 0;
        }

        .tree li {
            margin: 0 0 1rem 0;
            padding-left: 20px;
            position: relative;
        }

        .tree li::before {
            content: '';
            position: absolute;
            top: 1.2rem;
            left: 0;
            width: 20px;
            height: 2px;
            background: #ccc;
        }

        .card-position {
            background: #fff;
            border: 1px solid #ddd;
            border-left: 4px solid #007bff;
            padding: 10px;
            border-radius: 6px;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
            min-width: 220px;
        }

        .card-position .name {
            font-weight: bold;
        }

        .card-position .user {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if ($positions->count())
                        <div class="tree">
                            <ul>
                                @foreach ($positions as $position)
                                    <li>
                                        <div class="card-position">
                                            <div class="name">{{ $position->name }}</div>
                                            @php
                                                $assignedUsers = $position->user->name;
                                            @endphp
                                            <div class="user">{{ __('base.assigned_to') }}: {{ $assignedUsers ?: '-' }}</div>
                                        </div>

                                        @if ($position->childrenRecursive->count())
                                            @include('positions.structure-child', [
                                                'children' => $position->childrenRecursive,
                                            ])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-muted">{{ __('base.no_data_found') }}</p>
                    @endif
                </div>
                <div class="card-footer text-muted d-print-none">
                    <small>{{ __('base.show_on') }}: {{ now()->format('d M Y, H:i') }}</small>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
