{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@if(backpack_user()->role == 'superadmin')
    <x-backpack::menu-item title="Users" icon="nav-icon la la-users" :link="backpack_url('user')" />
    <x-backpack::menu-item title="Positions" icon="nav-icon la la-map-marker" :link="backpack_url('position')" />
@endif
<x-backpack::menu-item title="Reports" icon="nav-icon la la-file-text-o" :link="backpack_url('report')" />
