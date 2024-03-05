<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;
    protected $fillable = ['business_types_id', 'pi_users_id', 'pi_users_username', 'name', 'location', 'latitude', 'longitude', 'business_profile_photos', 'menu_status', 'orders_status', 'payments_status', 'loyalty_card_status'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'menu_status' => 'boolean',
        'orders_status' => 'boolean',
        'payments_status' => 'boolean',
        'loyalty_card_status' => 'boolean',
        'business_profile_photos' => 'array',
    ];

    public function business_type()
    {
        return $this->belongsTo(BusinessType::class, 'business_types_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(PiUser::class, 'pi_users_id', 'id');
    }

    public function loyalty_card()
    {
        return $this->hasOne(LoyaltyCard::class, 'business_profiles_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'business_profiles_id', 'id');
    }
}
