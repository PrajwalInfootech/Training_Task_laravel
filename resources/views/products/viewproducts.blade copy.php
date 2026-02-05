@extends('layouts.dashboard')

@section('title', 'Product List')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Products</h5>

        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            Add Product
        </a>
    </div>

    <div class="card-body table-responsive">
        <table id="productsTable" class="table table-bordered table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Product Name</th>
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
@endsection
@push('scripts')
<script>
$(document).ready(function () {

    const table = $('#productsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.datatable') }}",

        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'image',
                name: 'image',
                orderable: false,
                searchable: false
            },
            {
                data: 'product_name',
                name: 'product_name'
            },
            {
                data: 'category',
                name: 'detail.category'
            },
            {
                data: 'price',
                name: 'detail.base_price'
            },
            {
                data: 'stock',
                name: 'detail.stock'
            },
            {
                data: 'status',
                name: 'detail.status'
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false
            }
        ]
    });

});
</script>
@endpush
@push('scripts')
<script>
$(document).on('change', '.product-status-toggle', function () {
    const checkbox = $(this);
    const productId = checkbox.data('id');
    const label = checkbox.closest('label').find('.switch-label');

    fetch(`/products/${productId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'published') {
            label
                .text('Published')
                .removeClass('text-muted')
                .addClass('text-success');
        } else {
            label
                .text('Draft')
                .removeClass('text-success')
                .addClass('text-muted');
        }
    })
    .catch(() => {
        alert('Failed to update status');
        checkbox.prop('checked', !checkbox.prop('checked'));
    });
});

</script>
<script>
/* ===============================
   EDIT PRODUCT
================================ */
$(document).on('click', '.edit-product', function () {
    const encryptedId = $(this).data('id');

    // redirect to edit page
    window.location.href = `/products/${encryptedId}/edit`;
});

/* ===============================
   DELETE PRODUCT
================================ */
$(document).on('click', '.delete-product', function () {
    const encryptedId = $(this).data('id');

    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }

    fetch(`/products/${encryptedId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error();
        return res.json();
    })
    .then(() => {
        // reload datatable WITHOUT resetting pagination
        $('#productsTable').DataTable().ajax.reload(null, false);
    })
    .catch(() => {
        alert('Failed to delete product');
    });
});
</script>

@endpush
