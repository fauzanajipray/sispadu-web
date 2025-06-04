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
        <div class="col-md-12 mb-2">
            {{-- Data Complaint Table --}}
            <hr>
            <h4 class="card-header mb-2">Daftar Laporan</h4>

            <div id="crudTable_wrapper" class="dataTables_wrapper dt-bootstrap5">
                <div class="table-content row">
                    <div class="col-sm-12">
                        <table
                            class="table table-striped table-hover nowrap rounded card-table table-vcenter card d-table shadow-xs border-xs dataTable dtr-none collapsed has-hidden-columns"
                            id="projectTable">
                            <thead>
                                <tr class="">
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Status</th>
									<th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="project-body">
                                <tr>
                                    <td colspan="3">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="project-pagination" class="table-footer row mt-2 d-print-none align-items-center">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
    {{-- DATA TABLES --}}
    @basset('https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css')
    @basset('https://cdn.datatables.net/fixedheader/3.3.1/css/fixedHeader.dataTables.min.css')
    @basset('https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css')

    {{-- CRUD LIST CONTENT - crud_list_styles stack --}}
    @stack('crud_list_styles')
@endsection

@section('after_scripts')
    <script>
		currentPerPage = 10;
        function loadProjects(page = 1, perPage = 10) {
            $.ajax({
                url: '{{ route('webapi.position.reports', $entry->id) }}?page=' + page + '&perPage=' + perPage,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#project-body').html(response.rows);
                    $('#project-pagination').html(response.pagination);
                },
                error: function() {
                    $('#project-body').html('<tr><td colspan="3">Gagal memuat data</td></tr>');
                }
            });
        }

        function changePageSize(selectEl) {
            currentPerPage = selectEl.value;
            loadProjects(1, currentPerPage);
        }

        $(document).ready(function() {
            loadProjects();
        });
    </script>
@endsection
