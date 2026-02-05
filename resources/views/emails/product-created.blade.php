<h2>New Product Added 🎉</h2>

<p><strong>Name:</strong> {{ $product->product_name }}</p>
<p><strong>SKU:</strong> {{ $product->sku }}</p>
<p><strong>Category:</strong> {{ $product->detail->category ?? '-' }}</p>
<p><strong>Price:</strong> ₹{{ $product->detail->base_price ?? 0 }}</p>

<p>
    Added by: {{ $product->user->name ?? 'User' }}
</p>
