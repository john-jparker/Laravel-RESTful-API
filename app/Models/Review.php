<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $primaryKey = 'id';

    protected $fillable =[
        'product_id',
        'customer',
        'review',
        'star',
        'review_status',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
