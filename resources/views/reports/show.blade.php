@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        $crud->entity_name_plural => url($crud->route),
        trans('backpack::crud.preview') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <div class="container-fluid d-flex justify-content-between my-3">
        <section class="header-operation animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
            bp-section="page-header">
            <h1 class="text-capitalize mb-0" bp-section="page-heading">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</h1>
            <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">{!! $crud->getSubheading() ?? mb_ucfirst(trans('backpack::crud.preview')) . ' ' . $crud->entity_name !!}</p>
            @if ($crud->hasAccess('list'))
                <p class="ms-2 ml-2 mb-0" bp-section="page-subheading-back-button">
                    <small><a href="{{ url($crud->route) }}" class="font-sm"><i class="la la-angle-double-left"></i>
                            {{ trans('backpack::crud.back_to_all') }}
                            <span>{{ $crud->entity_name_plural }}</span></a></small>
                </p>
            @endif
        </section>
        <a href="javascript: window.print();" class="btn float-end float-right"><i class="la la-print"></i></a>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12 mb-2">
            <div class="row" bp-section="crud-operation-show">
                <div class="{{ $crud->getShowContentClass() }}">

                    {{-- Default box --}}
                    <div class="">
                        @if ($crud->model->translationEnabled())
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    {{-- Change translation button group --}}
                                    <div class="btn-group float-right">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                            data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ trans('backpack::crud.language') }}:
                                            {{ $crud->model->getAvailableLocales()[request()->input('_locale') ? request()->input('_locale') : App::getLocale()] }}
                                            &nbsp; <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach ($crud->model->getAvailableLocales() as $key => $locale)
                                                <a class="dropdown-item"
                                                    href="{{ url($crud->route . '/' . $entry->getKey() . '/show') }}?_locale={{ $key }}">{{ $locale }}</a>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($crud->tabsEnabled() && count($crud->getUniqueTabNames('columns')))
                            @include('crud::inc.show_tabbed_table')
                        @else
                            <div class="card no-padding no-border mb-0">
                                @include('crud::inc.show_table', ['columns' => $crud->columns()])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        @if (!$isDone || backpack_user()->role == 'superadmin')
            <div class="col-md-12 mb-2">
                <div class="card">
                    <div class="card-header">
                        Konfirmasi Laporan
                    </div>
                    <div class="card-body">
                        <form id="confirmationForm" action="{{ route('webapi.report.confirm-report') }}" method="POST">
                            {!! csrf_field() !!}
                            <input type="hidden" name="report_id" id="modal_report_id" value="{{ $entry->id }}">

                            <div class="form-group">
                                <label for="action">Pilih Tindakan</label>
                                <select class="form-control" name="action" id="action" required>
                                    <option value="completed">Selesai</option>
                                    <option value="rejected">Ditolak</option>
                                    <option value="disposition">Disposisikan</option>
                                </select>
                            </div>

                            <div class="form-group disposition-group d-none">
                                <label for="position_id">Disposisi ke:</label>
                                <select class="form-control select2-position" name="position_id" id="position_id"
                                    style="width: 100%">
                                    {{-- Isi melalui AJAX --}}
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="note">Catatan</label>
                                <textarea class="form-control" name="note" id="note" rows="3" placeholder="Tambahkan catatan" required></textarea>
                            </div>

                            <button type="button" class="btn btn-secondary">Batal</button>
                            <button type="submit" class="btn btn-primary" form="confirmationForm">Konfirmasi</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-12 mb-2">
            <div class="card">
                <div class="card-header">
                    Histori Laporan
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Tindakan</th>
                                        <th>Disposisi</th>
                                        <th>Catatan</th>
                                        <th>Oleh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reportHistories as $history)
                                        {{-- {{ dd($history) }} --}}
                                        <tr>
                                            <td>{{ $history->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                @if ($history->from_status != null && $history->to_status != null)
                                                    {{ $history->from_status }} -> {{ $history->to_status }}
                                                @elseif ($history->from_status != null)
                                                    {{ $history->from_status }}
                                                @elseif ($history->to_status != null)
                                                    {{ $history->to_status }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($history->disposition_id != null)
                                                    {{$history->disposition->toPosition->name }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $history->note }}</td>
                                            <td>{{ $history->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="col-md-12 mb-2">
            <div class="card">
                <div class="card-header">
                    Histori Laporan
                </div>
                <div class="card-body p-3">
                    <ul class="timeline list-unstyled ps-2 mb-0">
                        @foreach ($reportHistories as $index => $history)
                            <li class="timeline-item mb-3 {{ $loop->last ? 'last' : '' }}">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="timeline-icon mt-1"></div>
                                    <div>
                                        <div class="small text-muted">{{ $history->created_at->format('d-m-Y H:i') }}</div>
                                        <div class="fw-semibold">Report created with status {{ $history->status }}</div>
                                        <div class="text-muted small">Oleh: {{ $history->user->name }}</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div> --}}
    </div>

@endsection

@section('after_scripts')
    <style>
        .timeline {
            position: relative;
            padding-left: 20px;
            margin: 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-left: 20px;
        }

        .timeline-icon {
            position: relative;
            width: 12px;
            height: 12px;
            background-color: #6c757d;
            border-radius: 50%;
            z-index: 1;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-item.last .timeline-icon {
            background-color: #0d6efd;
            /* biru */
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function showNoty(type, message) {
            new Noty({
                type: type, // "success", "warning", "error", "info"
                text: message, // "Berhasil disimpan!"
                timeout: 3000
            }).show();
        }

        document.getElementById('action').addEventListener('change', function() {
            const dispositionGroup = document.querySelector('.disposition-group');
            const noteInput = document.getElementById('note');

            if (this.value === 'disposition') {
                dispositionGroup.classList.remove('d-none');

                $('.select2-position').select2({
                    placeholder: '{{ __('base.select_position') }}',
                    allowClear: true,
                    ajax: {
                        url: '{{ url('webapi/position/list') }}',
                        dataType: 'json',
                        delay: 250,
                        type: 'GET',
                        data: function(params) {
                            return {
                                q: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            let results = data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            });

                            return {
                                results: results,
                                pagination: {
                                    more: data.next_page_url !== null
                                }
                            };
                        },
                        cache: true
                    }
                });
            } else {
                dispositionGroup.classList.add('d-none');
            }

            // Fix the condition to use OR (||) instead of AND (&&)
            if (this.value === 'completed' || this.value === 'rejected') {
                noteInput.required = true;
            } else {
                noteInput.required = false;
            }
        });

        document.getElementById('confirmationForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form dari reload halaman
            const formData = new FormData(this);
            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNoty('success', data.message || 'Laporan berhasil diperbarui.');
                        window.location.reload();
                    } else {
                        showNoty('error', data.message || 'Gagal memperbarui laporan.');
                    }
                })
                .catch(error => {
                    showNoty('error', 'Terjadi kesalahan. Silakan coba lagi.');
                    console.error('Error:', error);
                });
        });
    </script>
@endsection
