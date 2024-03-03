<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['pi_users_id', 'total', 'shipping', 'cancelled_at', 'auto_cancelled_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'shipping' => 'array',
        'noshipping' => 'boolean',
        'paid' => 'boolean',
    ];

    public function lineOrders()
    {
        return $this->hasMany(LineOrder::class, 'orders_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(PiUser::class, 'pi_users_id', 'id');
    }
}
