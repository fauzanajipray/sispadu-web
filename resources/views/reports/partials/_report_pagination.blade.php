<div class="table-footer row mt-2 d-print-none align-items-center">
    <div class="col-sm-12 col-md-4">
        <div class="dataTables_length">
            <label>
                <select name="crudTable_length" class="form-select form-select-sm" onchange="changePageSize(this)">
                    <option value="1" {{ $reports->perPage() == 1 ? 'selected' : '' }}>1</option>
                    <option value="10" {{ $reports->perPage() == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $reports->perPage() == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $reports->perPage() == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $reports->perPage() == 100 ? 'selected' : '' }}>100</option>
                    <option value="-1" {{ $reports->perPage() == -1 ? 'selected' : '' }}>{{__('base.all')}}</option>
                </select>
                {{-- masukan per halaman --}}
                {{ __('base.per_page') }}
            </label>
        </div>
    </div>

    <div class="col-sm-0 col-md-4 text-center"></div>

    <div class="col-sm-12 col-md-4">
        <div class="dataTables_paginate paging_simple_numbers">
            <ul class="pagination mb-0">
                {{-- Prev --}}
                <li class="paginate_button page-item {{ $reports->onFirstPage() ? 'disabled' : '' }}">
                    <a href="#" class="page-link" onclick="loadProjects({{ $reports->currentPage() - 1 }}, currentPerPage)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M15 5l-5 5l5 5"></path>
                        </svg>
                    </a>
                </li>

                {{-- Numbered pages --}}
                @php
                    $start = max(1, $reports->currentPage() - 2);
                    $end = min($reports->lastPage(), $reports->currentPage() + 2);
                @endphp

                @if ($start > 1)
                    <li class="paginate_button page-item">
                        <a href="#" class="page-link" onclick="loadProjects(1, currentPerPage)">1</a>
                    </li>
                    <li class="paginate_button page-item disabled"><span class="page-link">...</span></li>
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    <li class="paginate_button page-item {{ $reports->currentPage() == $i ? 'active' : '' }}">
                        <a href="#" class="page-link" onclick="loadProjects({{ $i }}, currentPerPage)">{{ $i }}</a>
                    </li>
                @endfor

                @if ($end < $reports->lastPage())
                    <li class="paginate_button page-item disabled"><span class="page-link">...</span></li>
                    <li class="paginate_button page-item">
                        <a href="#" class="page-link" onclick="loadProjects({{ $reports->lastPage() }}, currentPerPage)">{{ $reports->lastPage() }}</a>
                    </li>
                @endif

                {{-- Next --}}
                <li class="paginate_button page-item {{ !$reports->hasMorePages() ? 'disabled' : '' }}">
                    <a href="#" class="page-link" onclick="loadProjects({{ $reports->currentPage() + 1 }}, currentPerPage)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M8 5l5 5l-5 5"></path>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
