@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login OTP Verification')

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

@if (session('error'))
<script>
Swal.fire({
  icon: 'error',
  title: 'Verification Failed',
  text: @json(session('error'))
});
</script>
@endif

@if (session('success'))
<script>
Swal.fire({
  icon: 'success',
  title: 'OTP Sent',
  text: @json(session('success'))
});
</script>
@endif
@endsection

@section('content')
<div class="authentication-wrapper authentication-basic px-6">
  <div class="authentication-inner py-6">

    <div class="card">
      <div class="card-body">

        <div class="app-brand justify-content-center mb-6">
          <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
              @include('_partials.macros',['height'=>20,'withbg'=>"fill:#fff"])
            </span>
            <span class="app-brand-text demo text-heading fw-bold">
              {{ config('variables.templateName') }}
            </span>
          </a>
        </div>

        <h4 class="mb-1">Verify Login OTP 🔐</h4>
        <p class="mb-6">
          Enter the 6-digit OTP sent to <strong>{{ $email }}</strong>
        </p>

        <form id="otpForm" action="{{ route('login.otp') }}" method="POST">
          @csrf
          <input type="hidden" name="email" value="{{ $email }}">

          <div class="mb-6">
            <div class="auth-input-wrapper d-flex justify-content-between">
              @for ($i = 1; $i <= 6; $i++)
                <input
                  type="tel"
                  class="form-control auth-input text-center numeral-mask mx-1"
                  maxlength="1"
                  inputmode="numeric"
                  required
                  {{ $i === 1 ? 'autofocus' : '' }}
                >
              @endfor
            </div>
            <input type="hidden" name="otp">
          </div>

          <button class="btn btn-primary d-grid w-100">
            Verify & Login
          </button>
        </form>

<script>
document.getElementById('otpForm').addEventListener('submit', function () {
  let otp = '';
  document.querySelectorAll('.numeral-mask').forEach(i => otp += i.value);
  this.querySelector('input[name="otp"]').value = otp;
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('.numeral-mask');

  inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
      // Allow only digits
      input.value = input.value.replace(/[^0-9]/g, '');

      // Move to next input
      if (input.value && index < inputs.length - 1) {
        inputs[index + 1].focus();
      }
    });

    input.addEventListener('keydown', (e) => {
      // Backspace → move to previous input if empty
      if (e.key === 'Backspace' && !input.value && index > 0) {
        inputs[index - 1].focus();
      }
    });
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('.numeral-mask');
  const OTP_LENGTH = inputs.length;

  // Move focus + type handling
  inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
      input.value = input.value.replace(/[^0-9]/g, '');
      if (input.value && index < OTP_LENGTH - 1) {
        inputs[index + 1].focus();
      }
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && index > 0) {
        inputs[index - 1].focus();
      }
    });
  });

  // 🔥 Paste handling (only on first input)
  inputs[0].addEventListener('paste', (e) => {
    e.preventDefault();

    const pasted = (e.clipboardData || window.clipboardData)
      .getData('text')
      .trim();

    // ❌ Non-numeric content
    if (!/^\d+$/.test(pasted)) {
      Swal.fire({
        icon: 'error',
        title: 'Invalid OTP',
        text: 'Only numbers are allowed in OTP',
      });
      return;
    }

    // Fill inputs
    const digits = pasted.slice(0, OTP_LENGTH).split('');
    inputs.forEach((input, i) => {
      input.value = digits[i] || '';
    });

    // Focus last filled cell
    const lastIndex = Math.min(digits.length, OTP_LENGTH) - 1;
    if (lastIndex >= 0) {
      inputs[lastIndex].focus();
    }
  });
});
</script>

      </div>
    </div>

  </div>
</div>
@endsection
