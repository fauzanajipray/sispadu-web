@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        $crud->entity_name_plural => url($crud->route),
        trans('backpack::crud.list') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
        bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</h1>
        <p class="ms-2 ml-2 mb-0" id="datatable_info_stack" bp-section="page-subheading">{!! $crud->getSubheading() ?? '' !!}</p>
    </section>
@endsection

@section('content')
    {{-- Default box --}}
    <div class="row" bp-section="crud-operation-list">

        {{-- THE ACTUAL CONTENT --}}
        <div class="{{ $crud->getListContentClass() }}">

            <div class="row mb-2 align-items-center">
                <div class="col-sm-9">
                    @if ($crud->buttons()->where('stack', 'top')->count() || $crud->exportButtons())
                        <div class="d-print-none {{ $crud->hasAccess('create') ? 'with-border' : '' }}">

                            @include('crud::inc.button_stack', ['stack' => 'top'])

                        </div>
                    @endif
                </div>
                @if ($crud->getOperationSetting('searchableTable'))
                    <div class="col-sm-3">
                        <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                        <path d="M21 21l-6 -6"></path>
                                    </svg>
                                </span>
                                <input type="search" class="form-control"
                                    placeholder="{{ trans('backpack::crud.search') }}..." />
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Backpack List Filters --}}
            @if ($crud->filtersEnabled())
                @include('crud::inc.filters_navbar')
            @endif

            <div class="{{ backpack_theme_config('classes.tableWrapper') }}">
                <table id="crudTable"
                    class="{{ backpack_theme_config('classes.table') ?? 'table table-striped table-hover nowrap rounded card-table table-vcenter card d-table shadow-xs border-xs' }}"
                    data-responsive-table="{{ (int) $crud->getOperationSetting('responsiveTable') }}"
                    data-has-details-row="{{ (int) $crud->getOperationSetting('detailsRow') }}"
                    data-has-bulk-actions="{{ (int) $crud->getOperationSetting('bulkActions') }}"
                    data-has-line-buttons-as-dropdown="{{ (int) $crud->getOperationSetting('lineButtonsAsDropdown') }}"
                    data-line-buttons-as-dropdown-minimum="{{ (int) $crud->getOperationSetting('lineButtonsAsDropdownMinimum') }}"
                    data-line-buttons-as-dropdown-show-before-dropdown="{{ (int) $crud->getOperationSetting('lineButtonsAsDropdownShowBefore') }}"
                    cellspacing="0">
                    <thead>
                        <tr>
                            {{-- Table columns --}}
                            @foreach ($crud->columns() as $column)
                                @php
                                    $exportOnlyColumn = $column['exportOnlyColumn'] ?? false;
                                    $visibleInTable = $column['visibleInTable'] ?? ($exportOnlyColumn ? false : true);
                                    $visibleInModal = $column['visibleInModal'] ?? ($exportOnlyColumn ? false : true);
                                    $visibleInExport = $column['visibleInExport'] ?? true;
                                    $forceExport =
                                        $column['forceExport'] ?? (isset($column['exportOnlyColumn']) ? true : false);
                                @endphp
                                <th data-orderable="{{ var_export($column['orderable'], true) }}"
                                    data-priority="{{ $column['priority'] }}" data-column-name="{{ $column['name'] }}"
                                    {{--
                    data-visible-in-table => if developer forced column to be in the table with 'visibleInTable => true'
                    data-visible => regular visibility of the column
                    data-can-be-visible-in-table => prevents the column to be visible into the table (export-only)
                    data-visible-in-modal => if column appears on responsive modal
                    data-visible-in-export => if this column is exportable
                    data-force-export => force export even if columns are hidden
                    --}}
                                    data-visible="{{ $exportOnlyColumn ? 'false' : var_export($visibleInTable) }}"
                                    data-visible-in-table="{{ var_export($visibleInTable) }}"
                                    data-can-be-visible-in-table="{{ $exportOnlyColumn ? 'false' : 'true' }}"
                                    data-visible-in-modal="{{ var_export($visibleInModal) }}"
                                    data-visible-in-export="{{ $exportOnlyColumn ? 'true' : ($visibleInExport ? 'true' : 'false') }}"
                                    data-force-export="{{ var_export($forceExport) }}">
                                    {{-- Bulk checkbox --}}
                                    @if ($loop->first && $crud->getOperationSetting('bulkActions'))
                                        {!! View::make('crud::columns.inc.bulk_actions_checkbox')->render() !!}
                                    @endif
                                    {!! $column['label'] !!}
                                </th>
                            @endforeach

                            @if ($crud->buttons()->where('stack', 'line')->count())
                                <th data-orderable="false" data-priority="{{ $crud->getActionsColumnPriority() }}"
                                    data-visible-in-export="false" data-action-column="true">
                                    {{ trans('backpack::crud.actions') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            {{-- Table columns --}}
                            @foreach ($crud->columns() as $column)
                                <th>
                                    {{-- Bulk checkbox --}}
                                    @if ($loop->first && $crud->getOperationSetting('bulkActions'))
                                        {!! View::make('crud::columns.inc.bulk_actions_checkbox')->render() !!}
                                    @endif
                                    {!! $column['label'] !!}
                                </th>
                            @endforeach

                            @if ($crud->buttons()->where('stack', 'line')->count())
                                <th>{{ trans('backpack::crud.actions') }}</th>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($crud->buttons()->where('stack', 'bottom')->count())
                <div id="bottom_buttons" class="d-print-none text-sm-left">
                    @include('crud::inc.button_stack', ['stack' => 'bottom'])
                    <div id="datatable_button_stack" class="float-right float-end text-right hidden-xs"></div>
                </div>
            @endif

        </div>

    </div>

    <div class="modal fade" id="crudModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalFormContent">
                    <form action="{{ route('user.update-position') }}" method="POST">
                        {!! csrf_field() !!}

                        <input type="hidden" name="user_id" id="modal_user_id" value="">

                        <div class="form-group">
                            <label for="position_id">{{ __('base.position') }}</label>
                            <select class="form-control select2-position" name="position_id" id="position_id"
                                style="width: 100%">
                                {{-- biarin kosong, karena nanti isi-nya lewat ajax --}}
                            </select>
                        </div>

                        {{-- Submit --}}
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('base.cancel')}}</button>
                            <button type="submit" class="btn btn-primary">{{__('base.save')}}</button>
                        </div>
                    </form>
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
    @include('crud::inc.datatables_logic')

    {{-- CRUD LIST CONTENT - crud_list_scripts stack --}}
    @stack('crud_list_scripts')

    {{-- CUSTOM SCRIPT --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function showNoty(type, message) {
            new Noty({
                type: type, // "success", "warning", "error", "info"
                text: message, // "Berhasil disimpan!"
                timeout: 3000
            }).show();
        }

        function showModalForm(userId) {
            document.getElementById('modal_user_id').value = userId;

            fetch("{{ url('webapi/user') }}/" + userId)
                .then(response => response.json())
                .then(user => {
                    // Tambahkan opsi ke Select2 jika belum ada
                    const positionId = user.position_id || '';
                    const positionText = user.position_name || 'Unknown Position'; // Pastikan server mengembalikan nama posisi

                    if (positionId) {
                        const option = new Option(positionText, positionId, true, true);
                        $('.select2-position').append(option).trigger('change');
                    }

                    // Set nilai Select2
                    $('.select2-position').val(positionId).trigger('change');

                    // Tampilkan modal
                    $('#crudModal').modal('show');
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                });

            // Update URL untuk Select2 dengan user_id
            $('.select2-position').select2({
                placeholder: '{{ __('base.select_position') }}',
                allowClear: true,
                ajax: {
                    url: '{{ url('webapi/position/list-without-user') }}/' + userId,
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
        }
        
        document.querySelector('#modalFormContent form').addEventListener('submit', function (event) {
            event.preventDefault(); // Mencegah form dari reload halaman

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNoty('success', data.message || '{{ __('base.success_saved') }}');
                    $('#crudModal').modal('hide'); // Tutup modal
                    window.location.reload(); // Reload halaman
                } else {
                    showNoty('error', data.message || '{{ __('base.error_occurred') }}'); 
                }
            })
            .catch(error => {
                showNoty('error', data.message || '{{ __('base.error_occurred') }}');  
            });
        });
    </script>
@endsection
