{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-question"></i> User</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('outlet') }}"><i class="nav-icon la la-question"></i> Outlet</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user-outlet') }}"><i class="nav-icon la la-question"></i> User Outlet</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('position') }}"><i class="nav-icon la la-question"></i> Positions</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('report') }}"><i class="nav-icon la la-question"></i> Reports</a></li>