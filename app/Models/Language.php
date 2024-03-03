<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'active', 'flag', 'country_code'];

    protected $casts = [
    ];

    public function region()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
