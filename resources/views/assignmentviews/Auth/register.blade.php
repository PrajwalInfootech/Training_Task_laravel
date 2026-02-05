@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Register - Email Verification')

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


{{-- ✅ CONTENT ONLY --}}
@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">

      <div class="card">
        <div class="card-body">

          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              <span class="app-brand-logo demo">
                @include('_partials.macros',['height'=>20,'withbg' => "fill: #fff;"])
              </span>
              <span class="app-brand-text demo text-heading fw-bold">
                {{ config('variables.templateName') }}
              </span>
            </a>
          </div>

          <h4 class="mb-1">Let’s get started 🚀</h4>
          <p class="mb-6">Enter your email address to receive a one-time password (OTP).</p>

          <form action="{{ route('register.send-otp') }}" method="POST" class="mb-6">
            @csrf

            <div class="mb-6">
              <label class="form-label">Email address</label>
              <input
                type="email"
                name="email"
                class="form-control"
                required
                autofocus
              >
            </div>

            <button
              class="btn btn-primary d-grid w-100"
              onclick="this.disabled=true; this.innerText='Sending OTP…'; this.form.submit();"
            >
              Send OTP
            </button>
          </form>
<p class="text-center">
            <span>Already have an Account?</span>
            <a href="{{ route('login') }}">
              <span>Proceed to Login</span>
            </a>
          </p>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection


{{-- ✅ SCRIPTS ONLY --}}
@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: 'OTP Sent!',
    text: @json(session('success')),
    confirmButtonText: 'OK'
  });
</script>
@endif

@if ($errors->any())
<script>
  Swal.fire({
    icon: 'error',
    title: 'Oops!',
    text: @json($errors->first()),
    confirmButtonText: 'Try Again'
  });
</script>
@endif
@endsection
