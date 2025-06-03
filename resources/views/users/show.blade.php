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
    <div class="container-fluid d-flex flex-wrap justify-content-between my-3">
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
        {{-- Dropdown button --}}
        {{-- <div class="btn-group btn-group-vertical float-end">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Settings
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:void(0);" onclick="showNoty()">Update Jabatan</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);" onclick="showModalForm()">Update Other</a></li>
            </ul>
        </div> --}}
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="{{ $crud->getShowContentClass() }}">

            {{-- Default box --}}
            <div>
                @if ($crud->model->translationEnabled())
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            {{-- Change translation button group --}}
                            <div class="btn-group float-right">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
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

    <!-- Modal (tetap ada di DOM sejak awal) -->
    <div class="modal fade" id="crudModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Jabatan</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalFormContent">
                    <form action="{{ route('user.update-position') }}" method="POST">
                        {!! csrf_field() !!}

                        <input type="hidden" name="user_id" id="modal_user_id" value="">

                        <div class="form-group">
                            <label for="position_id">Jabatan</label>
                            <select class="form-control select2-position" name="position_id" id="position_id"
                                style="width: 100%">
                                {{-- biarin kosong, karena nanti isi-nya lewat ajax --}}
                            </select>
                        </div>

                        {{-- Submit --}}
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/noty/lib/noty.min.js"></script> --}}

    <script>
        function showNoty(type, message) {
            new Noty({
                type: type, // "success", "warning", "error", "info"
                text: message, // "Berhasil disimpan!"
                timeout: 3000
            }).show();
        }

        function showModalForm(userId) {
            // Set user_id ke input hidden
            document.getElementById('modal_user_id').value = userId;

            fetch("{{ url('webapi/user') }}/" + userId)
                .then(response => response.json())
                .then(user => {
                    // Tambahkan opsi ke Select2 jika belum ada
                    const positionId = user.position_id || '';
                    const positionText = user.position_name || 'Unknown Position'; 

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
                placeholder: 'Cari jabatan...',
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
                console.log(data);
                console.log('Data yang dikirim:', Object.fromEntries(formData.entries()));
                // Cek apakah ada error
                console.log('Response data:', data);
                console.log('Response status:', data.success);
                console.log('Response message:', data.message);
                
                if (data.success) {
                    showNoty('success', data.message || 'Posisi berhasil diperbarui!');
                    $('#crudModal').modal('hide'); // Tutup modal
                    window.location.reload(); // Reload halaman
                } else {
                    showNoty('error', '2 . Terjadi kesalahan saat memperbarui posisissssss.');
                }
            })
            .catch(error => {
                showNoty('error', '1 . Terjadi kesalahan saat memperbarui posisi.');
            });
        });
    </script>
@endpush
