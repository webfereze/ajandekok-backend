<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'address',
        'number',
        'country',
        'city',
        'zip_code',
        'phone',
        'total_price',
        'status',
        'details',
        'shipping',
        'coupon',
    ];

    public function orderPhotos()
    {
        return $this->hasMany(OrderPhoto::class);
    }
}
