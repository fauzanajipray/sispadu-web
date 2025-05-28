@extends(backpack_view('layouts.top_left'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => backpack_url('dashboard'),
        'Struktur Organisasi' => false,
    ];

    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">Struktur Organisasi</span>
            <small class="d-print-none">Data posisi hierarki di desa</small>
            <small><a href="{{ backpack_url('position')}}" class="d-print-none font-sm"><i
                        class="la la-angle-double-left"></i> Back to all <span>positions</span></a></small>
        </h2>
    </section>
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
                                            <div class="user">Ditugaskan: {{ $assignedUsers ?: '-' }}</div>
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
                        <p class="text-muted">Tidak ada data posisi untuk ditampilkan.</p>
                    @endif
                </div>
                <div class="card-footer text-muted d-print-none">
                    <small>Ditampilkan pada: {{ now()->format('d M Y, H:i') }}</small>
                </div>
            </div>
        </div>
    </div>
@endsection
