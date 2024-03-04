<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Traits\Helper;

class LineOrder extends Model
{
    use Helper, HasFactory;
    protected $fillable = ['items_id', 'orders_id', 'name', 'price', 'quantity', 'shipped_at', 'cancelled_at'];

    protected $appends = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    ];

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'items_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id', 'id');
    }
}
