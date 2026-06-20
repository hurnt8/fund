<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanHistory extends Model
{
    protected $fillable = ['loan_request_id', 'admin_id', 'action', 'old_value', 'new_value'];

    protected $casts = ['old_value' => 'array', 'new_value' => 'array'];

    public function loan()
    {
        return $this->belongsTo(LoanRequest::class, 'loan_request_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
