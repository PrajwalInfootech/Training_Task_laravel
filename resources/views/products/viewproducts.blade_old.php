@php
    use Illuminate\Support\Facades\Crypt;
@endphp

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
        <table class="table table-bordered table-hover align-middle">
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

            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            @if ($product->product_image)
                                <img
                                    src="{{ asset($product->product_image) }}"
                                    width="50"
                                    height="50"
                                    class="rounded"
                                >
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>

                        <td>{{ $product->product_name }}</td>

                        <td>{{ $product->detail->category ?? '-' }}</td>

                        <td>
                            ₹ {{ number_format($product->detail->base_price ?? 0, 2) }}
                        </td>

                        <td>{{ $product->detail->stock ?? 0 }}</td>

                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input
                                    class="form-check-input product-status-toggle"
                                    type="checkbox"
                                    role="switch"
                                    data-id="{{ Crypt::encrypt($product->id) }}"
                                    {{ ($product->detail->status ?? '') === 'published' ? 'checked' : '' }}
                                >
                            </div>

                            <small
                                class="fw-semibold
                                    {{ ($product->detail->status ?? '') === 'published'
                                        ? 'text-success'
                                        : 'text-muted'
                                    }}"
                            >
                                {{ ($product->detail->status ?? '') === 'published'
                                    ? 'Published'
                                    : 'Draft'
                                }}
                            </small>
                        </td>

                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-info btn-sm">
                                    Edit
                                </button>

                                <form
                                    action="{{ route('products.destroy', Crypt::encrypt($product->id)) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this product?')"
                                    style="display:inline-block"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No products found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Status Toggle Script --}}
<script>
    document.querySelectorAll('.product-status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const productId = this.dataset.id;
            const label = this.closest('td').querySelector('small');

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
                    label.textContent = 'Published';
                    label.classList.remove('text-muted');
                    label.classList.add('text-success');
                } else {
                    label.textContent = 'Draft';
                    label.classList.remove('text-success');
                    label.classList.add('text-muted');
                }
            })
            .catch(() => {
                alert('Failed to update status');
                this.checked = !this.checked;
            });
        });
    });
</script>
@endsection
