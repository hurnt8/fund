<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address',
        'amount', 'darly', 'objet', 'subject',
        'npi', 'status', 'files'
    ];

    protected $casts = [
        'files' => 'array',
    ];
}
