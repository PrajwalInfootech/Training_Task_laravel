@extends('layouts/layoutMaster')

@section('title', 'eCommerce Dashboard - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    'resources/assets/vendor/libs/chartjs/chartjs.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
  ])
@endsection

@section('page-script')
@vite(['resources/assets/js/custom_analysisdashboard.js'])
@endsection

@section('content')
<div class="row g-6">
  <!-- View sales -->
  <div class="col-xl-4">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-7">
          <div class="card-body text-wrap">
<h5 class="card-title mb-0">
  Greetings ,{{ auth()->user()->email }}! 🎉
</h5>
          </div>
        </div>
        <div class="col-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img src="{{ asset('assets/img/illustrations/card-advance-sale.png')}}" height="140" alt="view sales">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- View sales -->

  <!-- Statistics -->
  <div class="col-xl-8 col-md-12">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">Statistics</h5>
      </div>
      <div class="card-body d-flex align-items-end">
        <div class="w-100">
          <div class="row gy-3">
       <div class="col-md-3 col-6">
  <div class="d-flex align-items-center">
    <div class="badge rounded bg-label-primary me-4 p-2">
      <i class="ti ti-chart-pie-2 ti-lg"></i>
    </div>
    <div class="card-info">
      <h5 class="mb-0" id="total-sales">0</h5>
      <small>Sales</small>
    </div>
  </div>
</div>

          <div class="col-md-3 col-6">
  <div class="d-flex align-items-center">
    <div class="badge rounded bg-label-info me-4 p-2">
      <i class="ti ti-users ti-lg"></i>
    </div>
    <div class="card-info">
      <h5 class="mb-0" id="customers-count">0</h5>
      <small>Customers</small>
    </div>
  </div>
</div>

           <div class="col-md-3 col-6">
  <div class="d-flex align-items-center">
    <div class="badge rounded bg-label-danger me-4 p-2">
      <i class="ti ti-shopping-cart ti-lg"></i>
    </div>

    <div class="card-info">
      <h5 id="products-count" class="mb-0">—</h5>
      <small>Products</small>
    </div>
  </div>
</div>

           <div class="col-md-3 col-6">
  <div class="d-flex align-items-center">
    <div class="badge rounded bg-label-danger me-4 p-2">
      <i class="ti ti-shopping-cart ti-lg"></i>
    </div>

    <div class="card-info">
      <h5 id="revenue-amount" class="mb-0">0</h5>
      <small>Revenue</small>
    </div>
  </div>
</div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Statistics -->
<div class="col-xxl-12 col-12">
  <div class="row g-6">

    <!-- Category-wise Products -->
   <div class="col-xl-6 col-sm-6">
  <div class="card h-100">
    <div class="card-header pb-0">
      <h5 class="card-title mb-1">Products by Category</h5>
      <p class="card-subtitle">Men / Women / Kids</p>
    </div>

    <div class="card-body pb-0">
      <ul class="p-0 m-0">

        <li class="d-flex align-items-center mb-4">
          <div class="me-4">
            <span class="badge bg-label-primary rounded p-1_5">
              <i class="ti ti-man ti-md"></i>
            </span>
          </div>
          <div class="d-flex w-100 justify-content-between">
            <h6 class="mb-0">Men</h6>
            <small id="menCount">0</small>
          </div>
        </li>

        <li class="d-flex align-items-center mb-4">
          <div class="me-4">
            <span class="badge bg-label-danger rounded p-1_5">
              <i class="ti ti-woman ti-md"></i>
            </span>
          </div>
          <div class="d-flex w-100 justify-content-between">
            <h6 class="mb-0">Women</h6>
            <small id="womenCount">0</small>
          </div>
        </li>

        <li class="d-flex align-items-center mb-4">
          <div class="me-4">
            <span class="badge bg-label-success rounded p-1_5">
              <i class="ti ti-baby-carriage ti-md"></i>
            </span>
          </div>
          <div class="d-flex w-100 justify-content-between">
            <h6 class="mb-0">Kids</h6>
            <small id="kidsCount">0</small>
          </div>
        </li>

      </ul>

      <div id="categoryBarChart"></div>
    </div>
  </div>
</div>


    <!-- Draft vs Published -->
  <div class="col-xl-6 col-sm-6">
  <div class="card h-100">
    <div class="card-header pb-0">
      <h5 class="card-title mb-1">Product Status</h5>
      <p class="card-subtitle">Draft vs Published</p>
    </div>

    <div class="card-body pb-0">
      <ul class="p-0 m-0">

        <li class="d-flex align-items-center mb-4">
          <div class="me-4">
            <span class="badge bg-label-success rounded p-1_5">
              <i class="ti ti-check ti-md"></i>
            </span>
          </div>
          <div class="d-flex w-100 justify-content-between">
            <h6 class="mb-0">Published</h6>
            <small id="publishedCount">0</small>
          </div>
        </li>

        <li class="d-flex align-items-center mb-4">
          <div class="me-4">
            <span class="badge bg-label-warning rounded p-1_5">
              <i class="ti ti-file ti-md"></i>
            </span>
          </div>
          <div class="d-flex w-100 justify-content-between">
            <h6 class="mb-0">Draft</h6>
            <small id="draftCount">0</small>
          </div>
        </li>

      </ul>

      <div id="statusBarChart"></div>
    </div>
  </div>
</div>

  </div>
</div>

 

  <!-- Revenue Report -->
<div class="col-xxl-12">
  <div class="card h-100">
    <div class="card-body p-0">
      <div class="row row-bordered g-0">

        <!-- GRAPH -->
        <div class="col-md-8 position-relative p-6">
          <div class="card-header d-flex justify-content-between p-0 mb-4">
            <h5 class="m-0 card-title">Revenue Report</h5>

            <!-- Date Picker -->
            <input
              type="date"
              id="revenueDate"
              class="form-control form-control-sm"
              style="max-width: 160px;"
            />
          </div>

<div class="revenue-chart-wrapper">
  <canvas id="totalRevenueChart"></canvas>

</div>

          <h3 class="mb-1" id="totalRevenueText">₹&nbsp;0</h3>
          <p class="text-muted mb-6">Total Revenue</p>
        </div>

        <!-- Expenses -->
<div class="col-md-4 p-4 d-flex flex-column align-items-center  text-center">
<div class="card h-100 w-100 p-3">

  <!-- Header row -->
  <div class="d-flex justify-content-between align-items-start mb-2">
    <div>
      <h5 class="mb-0">Expenses</h5>
      <p class="mb-0 text-muted">2026</p>
    </div>

    <div class="text-end">
      <h3 class="mb-0" id="totalExpenseText">₹0</h3>
    </div>
  </div>

  <!-- Chart center -->
  <div class="d-flex justify-content-center align-items-center flex-grow-1">
    <div style="width:300px; height:300px;">
      <canvas id="generatedLeadsChart"></canvas>
    </div>
  </div>

</div>

</div>
          

        </div>

      </div>
    </div>
  </div>


 <div class="col-xxl-12 col-12">
    <div class="row g-6">
      <!-- Profit last month -->
      <div class="col-xl-6 col-sm-6">
        <div class="card h-100">
  <div class="card-body text-center">
    <h5 class="mb-2">Profit Overview</h5>
<small>(All Time)</small>
    <h3 id="profitAmount" class="mb-1">₹0</h3>
    <p class="text-muted mb-4">Revenue vs Expenses</p>

    <div style="height:280px">
      <canvas id="profitChart"></canvas>
    </div>
  </div>
</div>

      </div>
      <!--/ Profit last month -->

      <!-- Expenses -->
       <div class="col-xxl-6 col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title m-0 me-2">
          <h5 class="mb-1">Newly Added Products</h5>
        </div>
        <div class="dropdown">
          <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-2 me-n1" type="button" id="popularProduct" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ti ti-dots-vertical ti-md text-muted"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="popularProduct">
            <a class="dropdown-item filter-products" data-days="7">Last 7 Days</a>
       <a class="dropdown-item filter-products" data-days="28">Last 28 Days</a>
       <a class="dropdown-item filter-products" data-days="30">Last Month</a>
       <a class="dropdown-item filter-products" data-days="365">Last Year</a>

          </div>
        </div>
      </div>
      <div class="card-body">
<ul class="p-0 m-0" id="newProductsList"></ul>
         
        </ul>
      </div>
    </div>
      </div>
      <!--/ Expenses -->

   
      <!--/ Generated Leads -->
    </div>
  </div>
 
  <!-- /Invoice table -->
</div>
@endsection
