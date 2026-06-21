<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountMovement extends Model
{
    protected $fillable = [
        'user_id', 'admin_id', 'type', 'amount', 'currency',
        'balance_before', 'balance_after', 'note',
    ];

    protected $casts = [
        'amount'         => 'float',
        'balance_before' => 'float',
        'balance_after'  => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
