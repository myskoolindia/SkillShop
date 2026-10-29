<div class="dashboard__sidebar-wrap">
    <div class="dashboard__sidebar-title mb-20">
        <h6 class="title">{{ userAuth()->school_name ?? userAuth()->name }}</h6>
        <small class="text-muted">{{ __('School Account') }}</small>
    </div>
    <nav class="dashboard__sidebar-menu">
        <ul class="list-wrap">
            <li class="{{ Route::is('school.dashboard') ? 'active' : '' }}">
                <a href="{{ route('school.dashboard') }}">
                    <i class="flaticon-mortarboard"></i>{{ __('Dashboard') }}</a>
            </li>

            <li class="{{ Route::is('school.teachers.*') ? 'active' : '' }}">
                <a href="{{ route('school.teachers.index') }}">
                    <i class="flaticon-mortarboard"></i>{{ __('Teachers') }}</a>
            </li>

            <li class="{{ Route::is('school.students.*') ? 'active' : '' }}">
                <a href="{{ route('school.students.index') }}">
                    <i class="flaticon-mortarboard"></i>{{ __('Students') }}</a>
            </li>

            <li class="{{ Route::is('school.courses.*') ? 'active' : '' }}">
                <a href="{{ route('school.courses.index') }}">
                    <i class="flaticon-mortarboard"></i>{{ __('Courses & Assignments') }}</a>
            </li>

            <li class="{{ Route::is('school.orders.*') ? 'active' : '' }}">
                <a href="{{ route('school.orders.index') }}">
                    <i class="flaticon-mortarboard"></i>{{ __('Order History') }}</a>
            </li>

            <li class="{{ Route::is('roboquest.*') ? 'active' : '' }}">
                <a href="{{ route('roboquest.settings') }}">
                    <i class="flaticon-mortarboard"></i>{{ __('RoboQuest Lab') }}</a>
            </li>
        </ul>
    </nav>
    <div class="dashboard__sidebar-title mt-30 mb-20">
        <h6 class="title">{{ __('Account') }}</h6>
    </div>
    <nav class="dashboard__sidebar-menu">
        <ul class="list-wrap">
            <li class="{{ Route::is('school.profile.*') ? 'active' : '' }}">
                <a href="{{ route('school.profile.index') }}">
                    <i class="flaticon-mortarboard"></i>
                    {{ __('Profile Settings') }}
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); $('#logout-form').trigger('submit');">
                    <i class="flaticon-mortarboard"></i>
                    {{ __('Logout') }}
                </a>
            </li>
        </ul>
    </nav>
</div>

{{-- start admin logout form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
{{-- end admin logout form --}}
