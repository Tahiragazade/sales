<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class ProductSale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'sale_no', 'product_id','quantity',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    public function saleNo()
    {
        return $this->belongsTo(SaleNumber::class,'sale_no');
    }

    public function productName()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
