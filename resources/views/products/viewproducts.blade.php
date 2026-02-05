@extends('layouts/layoutMaster')

@section('title', 'DataTables - Advanced Tables')

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

<!-- Vendor Scripts -->

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/jquery/jquery.js',
  'resources/assets/vendor/libs/bootstrap/bootstrap.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/custom_viewproducts.js'])
@endsection

@section('content')
<!-- Ajax Sourced Server-side -->
<div class="card">
  <h5 class="card-header">Products Table</h5>
  <div class="card-datatable text-nowrap">
    <table class="datatables-ajax table">
      <thead>
        <tr>
        <th>#</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Barcode</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Ajax Sourced Server-side -->

@include('_partials._modals.modal-edit-user')

@endsection
