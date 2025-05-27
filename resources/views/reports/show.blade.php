@extends(backpack_view('layouts.top_left'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => backpack_url('dashboard'),
    $crud->entity_name_plural => url($crud->route),
    'Moderate' => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
  <section class="container-fluid ">
    <a href="javascript: window.print();" class="btn float-right"><i class="la la-print"></i></a>
    <h2>
        <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
        <small class="d-print-none">{!! $crud->getSubheading() ?? 'Add '.$crud->entity_name !!}.</small>

        @if ($crud->hasAccess('list'))
          <small><a href="{{ url($crud->route) }}" class="hidden-print font-sm d-print-none"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
        @endif
    </h2>
  </section>
@endsection


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-print-none">
                <h3 class="card-title">{{ $entry->title }}</h3>
            </div>
            <div class="card-body">
                <p><strong>Created at:</strong> <span class="text-muted">{{ $entry->created_at->format('d M Y, H:i') }}</span></p>
                <p><strong>Updated at:</strong> <span class="text-muted">{{ $entry->updated_at->format('d M Y, H:i') }}</span></p>
                <hr>
                <p><strong>Content:</strong></p>
                <div class="content bg-light p-3 rounded">
                    {{ $entry->content }}
                </div>
                <hr>
                @if ($entry->images->count() > 0)
                    <p><strong>Attached Images:</strong></p>
                    <div class="row">
                        @foreach ($entry->images as $image)
                            <div class="col-md-4 mb-3">
                                <a href="{{ asset('storage/' . $image->path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $image->path) }}" class="img-fluid rounded shadow-sm" alt="Report Image">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p><strong>Attached Images:</strong> <span class="text-muted">No images attached.</span></p>
                @endif
            </div>
            <div class="card-footer text-muted d-print-none">
                <small>Last updated: {{ $entry->updated_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>
</div>
@endsection
