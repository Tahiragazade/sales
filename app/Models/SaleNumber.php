<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class SaleNumber extends Model 
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'sale_no'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    public function sales()
    {
        return $this->hasMany(ProductSale::class, 'sale_no', 'id');
    }
}
