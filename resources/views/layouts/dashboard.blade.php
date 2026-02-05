@php
$configData = Helper::appClasses();
$isFlex = true;
@endphp

<!DOCTYPE html>
<html lang="en" class="{{ $configData['style'] }}">

<head>
  <title>@yield('title', 'Dashboard')</title>

  <script>
    window.templateName = 'vertical-menu-template';
  </script>

  @include('layouts.sections.styles')
  @yield('vendor-style')
  @yield('page-style')
</head>




<body>

<div class="d-flex">

  <!-- SIDEBAR -->
  <aside class="flex-shrink-0 w-px-250 border-end vh-100 bg-body">
    <div class="p-4">
      <h5 class="mb-4">Products</h5>

      <ul class="nav nav-pills flex-column gap-2">
        <li class="nav-item">
          <a href="{{ route('dashboard') }}"
             class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Add Product
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('products.view') }}"
             class="nav-link {{ request()->routeIs('products.view') ? 'active' : '' }}">
            View Products
          </a>
        </li>
        
      </ul>
    </div>
  </aside>

  <!-- CONTENT -->
  <div class="flex-grow-1">

    <!-- TOP BAR -->
    <nav class="navbar border-bottom px-4">
      <div class="ms-auto d-flex align-items-center gap-3">
        <span class="text-muted">{{ auth()->user()->email }}</span>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-sm btn-outline-danger">Logout</button>
        </form>
      </div>
    </nav>

    <main class="container-p-x container-p-y">
      @yield('content')
    </main>

  </div>
</div>


@include('layouts.sections.scripts')

@yield('vendor-script')
@yield('page-script')
@stack('scripts')

</body>
</html>
