@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Set Password - Pages')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

{{-- 🔹 EXACT SAME SWEETALERT SETUP AS OTP PAGE --}}
@section('page-script')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if ($errors->any())
<script>
Swal.fire({
  icon: 'error',
  title: 'Validation Error',
  html: `{!! implode('<br>', $errors->all()) !!}`,
  confirmButtonText: 'Fix it'
});
</script>
@endif

@if (session('error'))
<script>
Swal.fire({
  icon: 'error',
  title: 'Error',
  text: @json(session('error')),
  confirmButtonText: 'OK'
});
</script>
@endif

@if (session('success'))
<script>
Swal.fire({
  icon: 'success',
  title: 'Success',
  text: @json(session('success')),
})
</script>
@endif

@endsection

@section('content')
<div class="authentication-wrapper authentication-basic px-6">
  <div class="authentication-inner py-6">

    <div class="card">
      <div class="card-body">

        <!-- Logo -->
        <div class="app-brand justify-content-center mb-6">
          <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
              @include('_partials.macros',['height'=>20,'withbg'=>"fill:#fff;"])
            </span>
            <span class="app-brand-text demo text-heading fw-bold">
              {{ config('variables.templateName') }}
            </span>
          </a>
        </div>
        <!-- /Logo -->

        <h4 class="mb-1">Set your password 🔐</h4>
        <p class="mb-6">
          Create a password for <strong>{{ $email }}</strong>
        </p>

        {{-- Safety check --}}
        @if (!$email)
          <div class="alert alert-danger">
            Session expired. Please restart the registration process.
          </div>
        @else

        <form action="{{ route('register.setpassword') }}" method="POST">
          @csrf

          <input type="hidden" name="email" value="{{ $email }}">

          <!-- Password -->
          <div class="mb-6 form-password-toggle">
            <label class="form-label">Password</label>
            <div class="input-group input-group-merge">
              <input
                type="password"
                name="password"
                class="form-control"
                placeholder="••••••••"
                required
              >
              <span class="input-group-text cursor-pointer">
                <i class="ti ti-eye-off"></i>
              </span>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="mb-6 form-password-toggle">
            <label class="form-label">Confirm Password</label>
            <div class="input-group input-group-merge">
              <input
                type="password"
                name="password_confirmation"
                class="form-control"
                placeholder="Confirm password"
                required
              >
              <span class="input-group-text cursor-pointer">
                <i class="ti ti-eye-off"></i>
              </span>
            </div>
          </div>

          <button class="btn btn-primary d-grid w-100">
            Set Password
          </button>
        </form>

        @endif

      </div>
    </div>

  </div>
</div>
@endsection
