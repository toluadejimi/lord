@extends('layout.main')
@section('content')
@include('partials.profile-styles')

<div class="pc-container prof-app">
    <div class="pc-content prof-page">
        <a href="{{ url('profile') }}" class="prof-back"><i class="ti ti-arrow-left"></i> Back to profile</a>

        <header class="prof-hero prof-hero--compact">
            <h1 class="prof-name mb-0">Change password</h1>
            <p class="prof-email">Hi {{ $displayName }}, set a new password below.</p>
        </header>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm prof-alert">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if(session('message'))
            <div class="alert alert-success border-0 shadow-sm prof-alert">{{ session('message') }}</div>
        @endif

        <div class="prof-menu-card prof-form-card">
            <form method="post" action="{{ url('update-password-now') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">New password</label>
                    <input type="password" name="password" class="form-control prof-input" required minlength="4" autocomplete="new-password">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Confirm password</label>
                    <input type="password" name="password_confirmation" class="form-control prof-input" required minlength="4" autocomplete="new-password">
                </div>
                <button type="submit" class="btn prof-submit w-100">Update password</button>
            </form>
        </div>
    </div>
</div>
@endsection
