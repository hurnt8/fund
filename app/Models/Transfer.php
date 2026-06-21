<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'user_id', 'reference', 'type',
        'amount', 'currency',
        'beneficiary_name', 'beneficiary_iban',
        'note', 'status', 'processed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateReference(): string
    {
        $year = now()->format('Y');
        $seq  = self::whereYear('created_at', $year)->count() + 1;
        return 'TRF-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
}
