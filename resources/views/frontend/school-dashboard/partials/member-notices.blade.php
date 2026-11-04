{{--
    Notices after adding or importing school members.
    $role: 'teacher' or 'student'
--}}
@php($pending = session(\App\Http\Controllers\Frontend\Concerns\ManagesSchoolMembers::credentialsKey($role)))

@if (session('new_member_login'))
    @php($login = session('new_member_login'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="fw-bold mb-1"><i class="fa fa-key"></i> {{ __('Login details for :name', ['name' => $login['name']]) }}</div>
        <div>{{ __('Email') }}: <code>{{ $login['email'] }}</code></div>
        <div>{{ __('Password') }}: <code class="user-select-all">{{ $login['password'] }}</code></div>
        <small class="d-block mt-1">{{ __('Share these privately. The password will not be shown again; reset it from the account if it is lost.') }}</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

@if ($pending)
    <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2" role="alert">
        <div>
            <div class="fw-bold"><i class="fa fa-key"></i> {{ __(':count new account(s) need their login details', ['count' => count($pending['rows'])]) }}</div>
            <small>{{ __('Download the list now. It can be downloaded only once, then the passwords are removed from this site.') }}</small>
        </div>
        <a href="{{ route('school.' . $role . 's.login-details') }}" class="btn btn-sm btn-warning">
            <i class="fa fa-download"></i> {{ __('Download login details') }}
        </a>
    </div>
@endif

@if (session('import_problems'))
    <div class="alert alert-light border" role="alert">
        <div class="fw-bold mb-2"><i class="fa fa-exclamation-triangle text-warning"></i> {{ __('Rows that were not imported') }}</div>
        <div class="table-responsive" style="max-height: 260px;">
            <table class="table table-sm mb-0">
                <thead><tr><th>{{ __('CSV line') }}</th><th>{{ __('Email') }}</th><th>{{ __('Reason') }}</th></tr></thead>
                <tbody>
                    @foreach (session('import_problems') as $problem)
                        <tr><td>{{ $problem['line'] }}</td><td>{{ $problem['email'] }}</td><td>{{ $problem['reason'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
