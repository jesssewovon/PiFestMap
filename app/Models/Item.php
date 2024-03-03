<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['business_profiles_id', 'name', 'price', 'images', 'time', 'description'];

    protected $casts = [
    	'images' => 'array',
    ];

    public function business_profile()
    {
        return $this->belongsTo(BusinessProfile::class, 'business_profiles_id', 'id');
    }
}