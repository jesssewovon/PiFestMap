<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStamp extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['business_profiles_id', 'pi_users_id', 'nb_stamps'];

    protected $casts = [
    	//'images' => 'array',
    ];

    public function business_profile()
    {
        return $this->belongsTo(BusinessProfile::class, 'business_profiles_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(PiUser::class, 'pi_users_id', 'id');
    }
}