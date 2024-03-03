<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Helper;

class PiUser extends Authenticatable
{
    use Helper, HasApiTokens, HasFactory, Notifiable;

    const PASSPORT = 1;
    const ID_CARD = 2;
    const VOTER_CARD = 3;
    const OTHER = 4;

    //protected $primaryKey = 'id';

    protected $fillable = [
        'updated_at',
        'last_notification_page_refresh',
        'card_type',
        'card_expired_at',
        'filter_country_code',
        'username',
        'email',
        'remember_token',
        'referred_by',
        'mining_data',
        'last_mining_at',
        'password',
        'is_partner',
        'partner_country_code',
        'email_verification_code',
        'email_code_generated_at',
        'email_verified_at',
        'cart',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'cart' => 'array',
        'addresses' => 'array',
        'living_address' => 'array',
        'profils' => 'array',
        'permissions' => 'array',
        'filter_country_code' => 'array',
        'mining_data' => 'array',
        'is_partner' => 'boolean',
        'partner_country_code' => 'array',
    ];

    protected $appends = [
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'isProfilSet',
        'balance',
        'tokenTotal',
        'token',
        'hasOnePendingWithdrawal',
        'nbNotification',
        'nbNewMessages',
        'appSettings',
        //'uid',
        //'avatar',
    ];

    public function business_profile()
    {
        return $this->hasOne(BusinessProfile::class, 'pi_users_id', 'id');
    }
}
