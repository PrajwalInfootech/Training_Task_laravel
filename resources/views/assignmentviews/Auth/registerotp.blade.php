@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Verify Email - OTP')

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
  'resources/assets/vendor/libs/cleavejs/cleave.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite([
  'resources/assets/js/pages-auth.js',
  'resources/assets/js/pages-auth-two-steps.js'
])

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: 'Verified!',
    text: @json(session('success')),
    confirmButtonText: 'Continue'
  });
</script>
@endif

@if (session('error'))
<script>
  Swal.fire({
    icon: 'error',
    title: 'Verification Failed',
    text: @json(session('error')),
    confirmButtonText: 'Try Again'
  });
</script>
@endif

@if ($errors->any())
<script>
  Swal.fire({
    icon: 'error',
    title: 'Invalid Input',
    text: @json($errors->first()),
    confirmButtonText: 'Fix it'
  });
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
              @include('_partials.macros',['height'=>20,'withbg' => "fill: #fff;"])
            </span>
            <span class="app-brand-text demo text-heading fw-bold">
              {{ config('variables.templateName') }}
            </span>
          </a>
        </div>
        <!-- /Logo -->

        <!-- ✅ UPDATED CONTENT -->
        <h4 class="mb-1">Verify your email 🔐</h4>
        <p class="mb-6">
          Enter the 6-digit OTP sent to your email address.
        </p>

        <form
          id="twoStepsForm"
          action="{{ route('register.verify-otp') }}"
          method="POST"
        >
          @csrf

          <div class="mb-6">
            <div class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
<input type="hidden" name="email" value="{{ session('email') }}">

              @for ($i = 1; $i <= 6; $i++)
                <input
                  type="tel"
                  class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                  maxlength="1"
                  inputmode="numeric"
                  required
                  {{ $i === 1 ? 'autofocus' : '' }}
                >
              @endfor

            </div>

            <!-- Combined OTP value -->
            <input type="hidden" name="otp" />
          </div>

          <button class="btn btn-primary d-grid w-100 mb-6">
            Verify OTP
          </button>

          <div class="text-center">
            Didn’t receive the OTP?
            <a href="{{ route('register.send-otp') }}"
               onclick="event.preventDefault(); document.getElementById('resend-form').submit();">
              Resend
            </a>
          </div>
        </form>

        <!-- Resend form -->
        <form id="resend-form" action="{{ route('register.send-otp') }}" method="POST" class="d-none">
          @csrf
          <input type="hidden" name="email" value="{{ session('email') }}">
        </form>
<script>
document.getElementById('twoStepsForm').addEventListener('submit', function () {
  let otp = '';
  document.querySelectorAll('.numeral-mask').forEach(input => {
    otp += input.value.trim();
  });
  this.querySelector('input[name="otp"]').value = otp;
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('.numeral-mask');
  const OTP_LENGTH = inputs.length;

  inputs.forEach((input, index) => {
    // Typing behavior
    input.addEventListener('input', () => {
      input.value = input.value.replace(/[^0-9]/g, '');
      if (input.value && index < OTP_LENGTH - 1) {
        inputs[index + 1].focus();
      }
    });

    // Backspace behavior
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && index > 0) {
        inputs[index - 1].focus();
      }
    });

    // 📋 Paste behavior (works from ANY cell)
    input.addEventListener('paste', (e) => {
      e.preventDefault();

      const pasted = (e.clipboardData || window.clipboardData)
        .getData('text')
        .trim();

      // ❌ Reject non-numeric paste
      if (!/^\d+$/.test(pasted)) {
        Swal.fire({
          icon: 'error',
          title: 'Invalid OTP',
          text: 'OTP must contain only numbers',
          confirmButtonText: 'OK'
        });
        return;
      }

      // Fill OTP boxes
      const digits = pasted.slice(0, OTP_LENGTH).split('');
      inputs.forEach((inp, i) => {
        inp.value = digits[i] || '';
      });

      // Focus last filled input
      const lastIndex = Math.min(digits.length, OTP_LENGTH) - 1;
      if (lastIndex >= 0) {
        inputs[lastIndex].focus();
      }
    });
  });
});
</script>

      </div>
    </div>

  </div>
</div>
