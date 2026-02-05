<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'sku',
        'barcode',
        'description',
        'product_image',
    ];

    public function details()
    {
        return $this->hasOne(ProductDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function detail()
{
    return $this->hasOne(ProductDetail::class);
}
public function orders()
{
    return $this->hasMany(Order::class);
}

}
