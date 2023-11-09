<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPhoto extends Model
{
    use HasFactory;

    protected $table = 'order_photos';

    protected $fillable = [
        'order_id',
        'image',
        'size',
        'quantity'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function canvas()
    {
        return $this->belongsTo(Canvas::class);
    }
}
