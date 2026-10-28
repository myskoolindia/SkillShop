@extends('frontend.layouts.master')
@section('contents')
<section class="container py-5" style="max-width:800px">
<h1>Connect your school’s RoboQuest</h1>
<p>After confirmed course payment, your school opens its own RoboQuest. Paid courses are synced there for assignment to teachers and students.</p>
@if($errors->any())<div role="alert">{{ $errors->first() }}</div>@endif
@if(session('roboquest_saved'))<p role="status">Connection saved.</p>@endif
<form method="POST" action="{{ route('roboquest.settings.save') }}">@csrf
<label class="d-block my-3">School ID<input class="form-control" name="school_id" required pattern="[A-Za-z0-9_-]{1,80}" value="{{ old('school_id',$connection?->school_id) }}"></label>
<label class="d-block my-3">RoboQuest URL<input class="form-control" name="base_url" type="url" required placeholder="https://roboquest.yourschool.example" value="{{ old('base_url',$connection?->base_url) }}"></label>
<label class="d-block my-3">School connection key<input class="form-control" name="shared_key" type="password" autocomplete="new-password" minlength="32" maxlength="200" {{ $connection ? '' : 'required' }}></label>
<p>Use the same school ID and connection key configured by your installer on the school server. Leave the key blank to keep the existing one.</p>
<button class="btn btn-primary">Save connection</button>
</form>
@if($connection)<a class="btn btn-primary mt-4" href="{{ route('roboquest.launch') }}">Open RoboQuest</a>@endif
</section>
@endsection
