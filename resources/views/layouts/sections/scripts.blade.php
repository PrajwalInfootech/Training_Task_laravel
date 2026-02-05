<!-- BEGIN: Vendor JS-->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
@vite([
  'resources/assets/vendor/js/bootstrap.js',

'resources/assets/vendor/libs/popper/popper.js',
  'resources/assets/vendor/libs/node-waves/node-waves.js',
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
  'resources/assets/vendor/libs/hammer/hammer.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/js/menu.js'
])

@yield('vendor-script')
<!-- END: Vendor JS -->

@vite(['resources/assets/js/main.js'])

@yield('page-script')
