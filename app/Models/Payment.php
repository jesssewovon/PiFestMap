<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	//ORIGIN
	const PIKETPLACE_WALLET = 1;
	const PINETWORK_WALLET = 2;

	//TYPE
	const DEPOSIT = 1;
	const WITHDRAW = 2;
	const TRANSFER = 3;
	const DONATE = 4;
	const PURCHASE = 5;
    const PAYMENT_SELLER = 6;//Payment back to the seller when buyer receive the product
    const REFUND_ORDER_CANCELLED = 7;
    const PLEDGING = 8;
    const WITHDRAW_FEE = 9;
    const TRANSFER_FEE = 10;
    const REFUND_PLEDGING = 11;
    const SALES_COMMISSION = 12;
    const SHIPPINGS_COMMISSION = 13;
    const PAYMENT_DELIVER = 14;
    const SHIPPING_FEE = 15;
    const REFUND_ORDER_AUTO_CANCELLED = 16;

    use HasFactory;

    protected $casts = [
    	'meta' => 'array',
        'is_piket' => 'boolean',
    ];

    protected $fillable = ['pi_users_id', 'pi_users_username', 'public_key', 'deleted_at', 'confirmed_at', 'is_piket', 'line_orders_id', 'payments_id'];

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'orders_id');
    }

    public function user()
    {
        return $this->hasOne(PiUser::class, 'id', 'pi_users_id');
    }

    public function from_wallet()
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id', 'id');
    }

    public function line_order()
    {
        return $this->belongsTo(LineOrder::class, 'line_orders_id', 'id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payments_id', 'id');
    }

    public function add($data)
    {
        $this->type = $data['type'];
        $this->amount = $data['amount'];
        $tab = $this->meta;
        if(isset($data['pi_users_id'])) {
        	$this->pi_users_id = $data['pi_users_id'];
        }
        if(isset($data['pi_users_username'])) {
            $this->pi_users_username = $data['pi_users_username'];
        }
        if (isset($data['wallet_id'])) {
            $this->wallet_id = $data['wallet_id'];
        }
        if(isset($data['line_orders_id'])) {
            $this->line_orders_id = $data['line_orders_id'];
        }
        if(isset($data['payments_id'])) {
            $this->payments_id = $data['payments_id'];
        }
        if(isset($data['user_is_piketplace'])) {
            $this->user_is_piketplace = $data['user_is_piketplace'];
        }
        if (isset($data['confirmed_at'])) {
        	$this->confirmed_at = $data['confirmed_at'];
        }
        if (isset($data['origin'])) {
        	$this->origin = $data['origin'];
        }
        if (isset($data['is_piket'])) {
            $this->is_piket = $data['is_piket'];
        }
        if (isset($data['fee'])) {//Withdraw
        	$tab['fee'] = $data['fee'];
        	$this->meta = $tab;
        }
        if (isset($data['public_key'])) {//Withdraw
            $this->public_key = $data['public_key'];
        }
        if (isset($data['from_wallet_id'])) {//Transfer
        	$this->from_wallet_id = $data['from_wallet_id'];
        	$this->to_wallet_id = $data['to_wallet_id'];
        }
        if (isset($data['meta'])) {
        	$tab = $this->meta;
        	foreach ($data['meta'] as $key => $value) {
        		$tab[$key] = $value;
        	}
        	$this->meta = $tab;
        }
        $this->meta = $tab;
        $this->save();
        return $this;
    }
}
