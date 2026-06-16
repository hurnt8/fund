<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'duration',
        'interest_rate',
        'start_date',
        'name',
        'email',
        'phone',
        'address',
        'employ',
        'salary',
        'darly',
        'objet',
        'status',
        'npi',
        'files'
    ];
}
