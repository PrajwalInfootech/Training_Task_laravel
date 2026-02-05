@php
$configData = Helper::appClasses();
$isFlex = true;
@endphp

@extends('layouts.dashboard')

@section('content')

<div class="d-flex">

  <!-- SIDEBAR -->
  <aside class="flex-shrink-0 w-px-250 border-end vh-100">
    <div class="p-4">
      <h5 class="mb-4">Products</h5>

      <ul class="nav nav-pills flex-column gap-2">

        <li class="nav-item">
          <a href="{{ route('dashboard') }}"
             class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-plus me-2"></i> Add Product
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('products') }}"
             class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
            <i class="ti ti-list me-2"></i> View Products
          </a>
        </li>

      </ul>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="flex-grow-1">

    <!-- TOP NAVBAR -->
    <nav class="navbar navbar-expand border-bottom px-4">
      <div class="ms-auto d-flex align-items-center gap-3">

        <span class="fw-medium text-muted">
          {{ auth()->user()->email }}
        </span>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger">
            Logout
          </button>
        </form>

      </div>
    </nav>

    <!-- PAGE CONTENT -->
    <main class="container-p-x container-p-y">
      @yield('page-content')
    </main>

  </div>
</div>

@endsection
