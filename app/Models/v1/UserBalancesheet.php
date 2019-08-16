<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserBalancesheet extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'happens_at', 'happens_because','balance_type', 'amount', 'transaction_id', 'credit', 'debit'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'happens_at'    => 'SIGNUP',    /** [SIGNUP,COIN_PURCHASE]; **/
        'balance_type'  => 'DR',        /** [DR,CR]; **/
        'transaction_id' => null,       /** [REWARD,COIN_PURCHASE]; **/
        'credit' => 0,
        'debit' => 0,
    ];
}
