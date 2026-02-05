<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'base_price',
        'discounted_price',
        'stock',
        'category',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
