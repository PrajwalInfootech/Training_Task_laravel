@extends('layouts/layoutMaster')

@section('title', 'eCommerce Product Add - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/katex.scss',
  'resources/assets/vendor/libs/quill/editor.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/dropzone/dropzone.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/quill/katex.js',
  'resources/assets/vendor/libs/quill/quill.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/dropzone/dropzone.js',
  'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/tagify/tagify.js'
])
@endsection

@section('page-script')
@vite([
  'resources/assets/js/app-ecommerce-product-add.js'
])
@endsection

@section('content')
{{-- SUCCESS MESSAGE --}}
@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ti ti-check me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- ERROR MESSAGE --}}
@if (session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ti ti-alert-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- VALIDATION ERRORS --}}
@if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Please fix the following:</strong>
    <ul class="mb-0 mt-2">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="app-ecommerce">

  <!-- Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
    <div>
      <h4 class="mb-1">Add New Product</h4>
      <p class="mb-0">Create and publish a product</p>
    </div>
    <button type="submit" form="productForm" class="btn btn-primary">
      Publish Product
    </button>
  </div>

  <form id="productForm" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf

    <div class="row">

      <!-- LEFT COLUMN -->
      <div class="col-12 col-lg-8">

        <!-- Product Info -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Product Information</h5>
          </div>
          <div class="card-body">

            <div class="mb-4">
              <label class="form-label">Product Name<span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" required>
              <div class="invalid-feedback">
  Product name is required
</div>
            </div>

            <div class="row mb-4">
              <div class="col">
                <label class="form-label">SKU<span class="text-danger">*</span></label>
                <input type="text" name="sku"  class="form-control" maxlength="10" required>
                  <small class="text-muted">Max 10 characters</small>
   <div class="invalid-feedback">
  SKU is required
</div>
              </div>
              <div class="col">
                <label class="form-label">Barcode</label>
                <input type="text" name="barcode" class="form-control" maxlength="10">
                  <small class="text-muted">Max 10 characters</small>

              </div>
            </div>

          
<label for="descriptionInput">Description</label>
<textarea
    class="form-control"
    name="description"
    id="descriptionInput"
    rows="4"
    maxlength="200"
    placeholder="Enter product description..."
></textarea>

  <small class="text-muted">Max 200 characters</small>
          </div>
        </div>

        <!-- Product Images -->
        <div class="card mb-6">
  <div class="card-header">
    <h5 class="mb-0">Product Image<span class="text-danger">*</span></h5>
  </div>

  <div class="card-body">
    <input
      type="file"
      name="image"
      id="imageInput"
      class="form-control"
      accept="image/*"
      required
    >
    <div class="mt-3">
      <img
        id="imagePreview"
        src=""
        alt="Preview"
        style="display:none; max-width: 200px; border-radius: 8px;"
      >
      <small class="text-muted">
           <div class="invalid-feedback">
  Product Image is required
</div>
  Image size: Min 100 KB, Max 10 MB
</small>

    </div>
  </div>
</div>


      </div>

      <!-- RIGHT COLUMN -->
      <div class="col-12 col-lg-4">

        <!-- Pricing -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Pricing</h5>
          </div>
          <div class="card-body">

           <div class="mb-4">
  <label class="form-label">Base Price<span class="text-danger">*</span></label>
  <input
    type="number"
    name="price"
    id="basePrice"
    class="form-control"
    min="0"
    step="0.01"
    required
  >   <div class="invalid-feedback">
   Base Price is required
</div>
</div>

<div>
  <label class="form-label">Discounted Price<span class="text-danger">*</span></label>
  <input
    type="number"
    name="discount_price"
    id="discountPrice"
    class="form-control"
    min="0"
    step="0.01"
  required>
     <div class="invalid-feedback">
  Discounted Price is required
</div>
  <small class="text-danger d-none" id="discountError">
    Discounted price cannot be greater than base price
  </small>
</div>


          </div>
        </div>

        <!-- Stock -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Stock</h5>
          </div>
          <div class="card-body">
            <label class="form-label">Units in Stock<span class="text-danger">*</span></label>
            <input id="stockPrice" type="number" name="stock" class="form-control" required>
               <div class="invalid-feedback">
  Stock is required
</div>
          </div>
        </div>

        <!-- Organize -->
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Organize</h5>
          </div>
          <div class="card-body">

            <div class="mb-4">
              <label class="form-label">Category<span class="text-danger">*</span></label>
             <select name="category" class="form-select select2" required>
  <option value="" selected disabled hidden>
    Select Category
  </option>
  <option value="men">Men</option>
  <option value="women">Women</option>
  <option value="kid">Kid</option>
</select>
   <div class="invalid-feedback">
  Stock is required
</div>
            </div>

            <div>
              <label class="form-label">Status<span class="text-danger">*</span></label>
              <select name="status" class="form-select select2" required>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
              </select>
            </div>

          </div>
        </div>

      </div>
    </div>
  </form>
</div>
<script>
document.getElementById('imageInput').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');

    const MIN_SIZE = 100 * 1024;      // 500 KB
    const MAX_SIZE = 10 * 1024 * 1024; // 4 MB

    // Reset preview
    preview.style.display = 'none';
    preview.src = '';

    if (!file) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file');
        event.target.value = '';
        return;
    }

    // Validate file size
    if (file.size < MIN_SIZE) {
        alert('Image size must be at least 500 KB');
        event.target.value = '';
        return;
    }

    if (file.size > MAX_SIZE) {
        alert('Image size must not exceed 4 MB');
        event.target.value = '';
        return;
    }

    // Preview image
    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
});
</script>

<script>
const basePrice = document.getElementById('basePrice');
const discountPrice = document.getElementById('discountPrice');
const discountError = document.getElementById('discountError');

function validatePrices() {
    const base = parseFloat(basePrice.value) || 0;
    const discount = parseFloat(discountPrice.value) || 0;

    if (discount > base) {
        discountError.classList.remove('d-none');
        discountPrice.setCustomValidity('Invalid');
    } else {
        discountError.classList.add('d-none');
        discountPrice.setCustomValidity('');
    }
}

basePrice.addEventListener('input', validatePrices);
discountPrice.addEventListener('input', validatePrices);
</script>
<script>
function allowOnlyNumbers(event) {
  // Block e, E, +, -, and letters
  if (
    event.key === 'e' ||
    event.key === 'E' ||
    event.key === '+' ||
    event.key === '-' ||
    isNaN(event.key) && event.key !== '.'
  ) {
    event.preventDefault();
  }
}

document.getElementById('basePrice').addEventListener('keydown', allowOnlyNumbers);
document.getElementById('discountPrice').addEventListener('keydown', allowOnlyNumbers);
stockPrice
document.getElementById('stockPrice').addEventListener('keydown', allowOnlyNumbers);

</script>
<script>
(() => {
  'use strict'

  const forms = document.querySelectorAll('.needs-validation')

  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>


@endsection
