<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductCreatedMail;
use Spatie\Activitylog\Models\Activity;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    /**
     * Dashboard - Add Product Page
     */
   public function datatable(Request $request)
{
    $query = Product::with('detail')
        ->where('user_id', auth()->id());

  return DataTables::of($query)
    ->addIndexColumn()

    ->addColumn('category', fn ($p) => $p->detail->category ?? '-')
    ->addColumn('price', fn ($p) => $p->detail->base_price ?? '0')
    ->addColumn('stock', fn ($p) => $p->detail->stock ?? '0')
->addColumn('status', function ($p) {
    $checked = ($p->detail->status ?? 'draft') === 'published' ? 'checked' : '';
    $text    = $checked ? 'Published' : 'Draft';
    $class   = $checked ? 'text-success' : 'text-muted';

    return '
        <label class="switch switch-success mb-0">
            <input type="checkbox"
                   class="switch-input product-status-toggle"
                   data-id="'.Crypt::encrypt($p->id).'"
                   '.$checked.'>
            <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
            </span>
            <span class="switch-label '.$class.'">'.$text.'</span>
        </label>
    ';
})



    ->addColumn('image', function ($p) {
        if ($p->product_image) {
            return '<img src="'.asset($p->product_image).'" width="50">';
        }
        return '-';
    })

    ->addColumn('actions', function ($p) {
        $id = Crypt::encrypt($p->id);

        return '
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary edit-product" data-id="'.$id.'">
                    Edit
                </button>
                <button class="btn btn-sm btn-outline-danger delete-product" data-id="'.$id.'">
                    Delete
                </button>
            </div>
        ';
    })

    ->rawColumns(['image', 'actions','status'])
    ->make(true);

}

    public function dashboard()
    {
         if (!auth()->check()) {
        return redirect()->route('login');
    }
        return view('products.addproducts');
    }


    /**
     * Store Product
     */
      public function storeold(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string',
            'status' => 'required|in:published,draft',

            // 👇 image validation
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($request) {

            /** -------------------------
             * IMAGE UPLOAD
             * ------------------------- */
            $imagePath = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image');

                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                $image->move(
                    public_path('uploads/product_images'),
                    $imageName
                );

                // path saved in DB
                $imagePath = 'uploads/product_images/' . $imageName;
            }

            /** -------------------------
             * PRODUCT CREATE
             * ------------------------- */
            $product = Product::create([
                'user_id' => auth()->id(),
                'product_name' => $request->name,
                'sku' => $request->sku,
                'barcode' => $request->barcode,
                'description' => $request->description,
                'product_image' => $imagePath, // ✅ FIXED
            ]);

            ProductDetail::create([
                'product_id' => $product->id,
                'base_price' => $request->price,
                'discounted_price' => $request->discount_price,
                'stock' => $request->stock,
                'category' => $request->category,
                'status' => $request->status,
            ]);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Product added successfully');
    }

   public function storenew(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'sku' => 'required|string|unique:products,sku',
        'barcode' => 'nullable|string',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'discount_price' => 'nullable|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'category' => 'required|string',
        'status' => 'required|in:published,draft',
        'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10096',
    ]);
$product = null;

    try {
        DB::transaction(function () use ($request) {
            

            // IMAGE UPLOAD
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/product_images'), $imageName);
                $imagePath = 'uploads/product_images/' . $imageName;
            }

            // PRODUCT
            $product = Product::create([
                'user_id' => auth()->id(),
                'product_name' => $request->name,
                'sku' => $request->sku,
                'barcode' => $request->barcode,
                'description' => $request->description,
                'product_image' => $imagePath,
            ]);

            ProductDetail::create([
                'product_id' => $product->id,
                'base_price' => $request->price,
                'discounted_price' => $request->discount_price,
                'stock' => $request->stock,
                'category' => $request->category,
                'status' => $request->status,
            ]);
            activity()
    ->performedOn($product)
    ->causedBy(auth()->user())
    ->withProperties([
        'product_name' => $product->product_name,
        'sku' => $product->sku,
        'price' => $request->price,
        'status' => $request->status,
    ])
    ->log('Product created');

            Mail::to(auth()->user()->email)
        ->send(new ProductCreatedMail($product->load('detail')));
        });
/** 🔔 FCM PART (NOW IT WILL RUN) */
        $user = auth()->user();

        Log::info('ADD PRODUCT FCM HIT', [
            'user_id' => $user->id,
            'token' => $user->fcm_token,
        ]);

        if ($user->fcm_token) {
            FcmService::send(
                $user->fcm_token,
                'Product Created',
                $product->product_name . ' was created',
                [
                    'type' => 'product_created',
                    'product_id' => (string) $product->id,
                ]
            );
        } else {
            Log::warning('FCM token missing for user', ['user_id' => $user->id]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Product added successfully 🎉');

    } catch (\Exception $e) {
        Log::error('Product creation failed', [
            'error' => $e->getMessage()
        ]);

        return back()
            ->withInput()
            ->with('error', 'Error occurred while adding product. Please try again.');
    }
   
}
public function store(Request $request)
{
    $request->validate([
        'name'            => 'required|string|max:255',
        'sku'             => 'required|string|unique:products,sku',
        'barcode'         => 'nullable|string',
        'description'     => 'nullable|string',
        'price'           => 'required|numeric|min:0',
        'discount_price'  => 'nullable|numeric|min:0',
        'stock'           => 'required|integer|min:0',
        'category'        => 'required|string',
        'status'          => 'required|in:published,draft',
        'image'           => 'required|image|mimes:jpg,jpeg,png,webp|max:10096',
    ]);

    $product = null;

    try {

        DB::transaction(function () use ($request, &$product) {

            /** -------------------------
             * IMAGE UPLOAD
             * ------------------------- */
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/product_images'), $imageName);
                $imagePath = 'uploads/product_images/' . $imageName;
            }

            /** -------------------------
             * PRODUCT CREATE
             * ------------------------- */
            $product = Product::create([
                'user_id'        => auth()->id(),
                'product_name'   => $request->name,
                'sku'            => $request->sku,
                'barcode'        => $request->barcode,
                'description'    => $request->description,
                'product_image'  => $imagePath,
            ]);

            /** -------------------------
             * PRODUCT DETAIL
             * ------------------------- */
            ProductDetail::create([
                'product_id'        => $product->id,
                'base_price'        => $request->price,
                'discounted_price'  => $request->discount_price,
                'stock'             => $request->stock,
                'category'          => $request->category,
                'status'            => $request->status,
            ]);

            /** -------------------------
             * ACTIVITY LOG
             * ------------------------- */
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties([
                    'product_name' => $product->product_name,
                    'sku'          => $product->sku,
                    'price'        => $request->price,
                    'status'       => $request->status,
                ])
                ->log('Product created');

            /** -------------------------
             * EMAIL
             * ------------------------- */
            Mail::to(auth()->user()->email)
                ->send(new ProductCreatedMail($product->load('detail')));
        });

        /** 🔔 FCM NOTIFICATION (AFTER COMMIT) */
        $user = auth()->user();

        Log::info('ADD PRODUCT FCM HIT', [
            'user_id' => $user->id,
            'token'   => $user->fcm_token,
        ]);

        if ($user->fcm_token && $product) {
            FcmService::send(
                $user->fcm_token,
                'Product Created',
                $product->product_name . ' was created',
                [
                    'type'       => 'product_created',
                    'product_id' => (string) $product->id,
                ]
            );
        } else {
            Log::warning('FCM token missing or product null', [
                'user_id' => $user->id,
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Product added successfully 🎉');

    } catch (\Exception $e) {

        Log::error('Product creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return back()
            ->withInput()
            ->with('error', 'Error occurred while adding product. Please try again.');
    }
}


public function view()
{
    $products = Product::with('detail')
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    return view('products.viewproducts', compact('products'));
}
public function viewtest()
{
   $products = Product::where('user_id', auth()->id())->get();

    dd($products); 
}
public function toggleStatusold($id)
{
    $product = Product::with('detail')->findOrFail($id);

    if (!$product->detail) {
        return response()->json(['message' => 'Product detail not found'], 404);
    }

    $product->detail->status =
        $product->detail->status === 'published' ? 'draft' : 'published';

    $product->detail->save();

    return response()->json([
        'status' => $product->detail->status
    ]);
}
public function toggleStatus($encryptedId)
{
    try {
        $id = Crypt::decrypt($encryptedId);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Invalid ID'], 403);
    }

    $product = Product::with('detail')->findOrFail($id);

    $product->detail->status =
        $product->detail->status === 'published' ? 'draft' : 'published';

    $product->detail->save();
activity()
    ->performedOn($product)
    ->causedBy(auth()->user())
    ->withProperties([
        'new_status' => $product->detail->status
    ])
    ->log('Product status updated');

    return response()->json([
        'status' => $product->detail->status
    ]);
}
 public function destroyold(Product $product)
    {
        // delete image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.view')
            ->with('success', 'Product deleted successfully');
    }
    public function destroykindoflatest($encryptedId)
{
    try {
        $id = Crypt::decrypt($encryptedId);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Invalid product');
    }

    $product = Product::findOrFail($id);

    $product->delete();

    return redirect()
        ->route('products.view')
        ->with('success', 'Product deleted successfully');
}
public function destroy($encryptedId)
{
    try {
        $id = Crypt::decrypt($encryptedId);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Invalid product'], 403);
    }

    $product = Product::findOrFail($id);
    $product->delete();
activity()
    ->performedOn($product)
    ->causedBy(auth()->user())
    ->withProperties([
        'product_name' => $product->product_name,
        'sku' => $product->sku,
    ])
    ->log('Product deleted');

    return response()->json([
        'message' => 'Product deleted successfully'
    ]);
}


public function edit($encryptedId)
{
    try {
        $id = Crypt::decrypt($encryptedId);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Invalid product'], 403);
    }

    $product = Product::with('detail')
        ->where('user_id', auth()->id())
        ->findOrFail($id);

    return response()->json([
        'product_name' => $product->product_name,
        'description'  => $product->description,
        'detail' => [
            'category'          => $product->detail->category ?? null,
            'base_price'        => $product->detail->base_price ?? null,
            'discounted_price'  => $product->detail->discounted_price ?? null,
            'stock'             => $product->detail->stock ?? null,
            'status'            => $product->detail->status ?? 'draft',
        ],
        'image' => $product->product_image
            ? asset($product->product_image)
            : null
    ]);
}
public function updatekindofnew(Request $request, $encryptedId)
{
    try {
        $id = Crypt::decrypt($encryptedId);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Invalid product'], 403);
    }

    $request->validate([
        'name'            => 'required|string|max:255',
        'category'        => 'required|string',
        'price'           => 'required|numeric|min:0',
        'discount_price'  => 'nullable|numeric|min:0',
        'stock'           => 'required|integer|min:0',
        'status'          => 'required|in:published,draft',
        'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10096',
    ]);

  DB::transaction(function () use ($request, $id) {

    $product = Product::with('detail')
        ->where('user_id', auth()->id())
        ->findOrFail($id);

    // Capture OLD values
    $oldData = [
        'product_name' => $product->product_name,
        'category'     => $product->detail->category,
        'price'        => $product->detail->base_price,
        'discount'     => $product->detail->discounted_price,
        'stock'        => $product->detail->stock,
        'status'       => $product->detail->status,
    ];

    // Image update
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('uploads/product_images'), $imageName);
        $product->product_image = 'uploads/product_images/'.$imageName;
    }

    $product->update([
        'product_name' => $request->name,
        'description'  => $request->description,
    ]);

    $product->detail->update([
        'category'         => $request->category,
        'base_price'       => $request->price,
        'discounted_price' => $request->discount_price,
        'stock'            => $request->stock,
        'status'           => $request->status,
    ]);

    // Capture NEW values
    $newData = [
        'product_name' => $request->name,
        'category'     => $request->category,
        'price'        => $request->price,
        'discount'     => $request->discount_price,
        'stock'        => $request->stock,
        'status'       => $request->status,
    ];

    activity()
        ->performedOn($product)
        ->causedBy(auth()->user())
        ->withProperties([
            'old' => $oldData,
            'new' => $newData,
        ])
        ->log('Product updated');
});


    return response()->json([
        'message' => 'Product updated successfully'
    ]);
}
public function update(Request $request, $encryptedId)
{
    try {
        $id = Crypt::decrypt($encryptedId);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Invalid product'], 403);
    }

    $request->validate([
        'name'            => 'required|string|max:255',
        'category'        => 'required|string',
        'price'           => 'required|numeric|min:0',
        'discount_price'  => 'nullable|numeric|min:0',
        'stock'           => 'required|integer|min:0',
        'status'          => 'required|in:published,draft',
        'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10096',
    ]);

    $product = null; // ✅ IMPORTANT

    DB::transaction(function () use ($request, $id, &$product) {

        $product = Product::with('detail')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $oldData = [
            'product_name' => $product->product_name,
            'category'     => $product->detail->category,
            'price'        => $product->detail->base_price,
            'discount'     => $product->detail->discounted_price,
            'stock'        => $product->detail->stock,
            'status'       => $product->detail->status,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads/product_images'), $imageName);
            $product->product_image = 'uploads/product_images/'.$imageName;
        }

        $product->update([
            'product_name' => $request->name,
            'description'  => $request->description,
        ]);

        $product->detail->update([
            'category'         => $request->category,
            'base_price'       => $request->price,
            'discounted_price' => $request->discount_price,
            'stock'            => $request->stock,
            'status'           => $request->status,
        ]);

        $newData = [
            'product_name' => $request->name,
            'category'     => $request->category,
            'price'        => $request->price,
            'discount'     => $request->discount_price,
            'stock'        => $request->stock,
            'status'       => $request->status,
        ];

        activity()
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldData,
                'new' => $newData,
            ])
            ->log('Product updated');
    });

    /** 🔔 FCM AFTER COMMIT */
    $user = auth()->user();

    if ($user->fcm_token && $product) {
        FcmService::send(
            $user->fcm_token,
            'Product Updated',
            $product->product_name . ' was updated',
            [
                'type' => 'product_updated',
                'product_id' => (string) $product->id,
            ]
        );
    }

    return response()->json([
        'message' => 'Product updated successfully'
    ]);
}

}
