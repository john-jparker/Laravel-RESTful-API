<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $primaryKey = 'id';

    protected $fillable = [
        'product_name',
        'details',
        'price',
        'stock',
        'discount',
        'status',
    ];


    public function reviews(){
        return $this->hasMany(Review::class, 'product_id', 'id');
    }
}
