<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Helper;

class Setting extends Model
{
    use Helper, HasFactory;

    protected $fillable = [
    	'value',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    static function increasePiketplaceBalance($amount)
    {
        $balance = Setting::where('name', 'balance')->first();
        //$total = floatval($balance->value)+$amount;
        //$total = bcadd(strval($balance->value), strval($amount), 7);
        $total = piket_add($balance->value, $amount);
    	$balance->value = $total;
    	$balance->save();
    }

    static function decreasePiketplaceBalance($amount)
    {
        $balance = Setting::where('name', 'balance')->first();
        //$total = floatval($balance->value)-$amount;
        //$total = bcsub(strval($balance->value), strval($amount), 7);
        $total = piket_sub($balance->value, $amount);
        $balance->value = $total;
        $balance->save();
    }
}
