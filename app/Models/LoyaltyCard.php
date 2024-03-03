<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
    use HasFactory;
    protected $fillable = ['business_profiles_id', 'stamp_free_item', 'name_free_item', 'number_free_item'];

    public function business_profile()
    {
        return $this->belongsTo(BusinessProfile::class, 'business_profiles_id', 'id');
    }
}
