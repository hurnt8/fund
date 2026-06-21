<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    const STATUS_PENDING      = 'pending';
    const STATUS_COMPLETED    = 'completed';
    const STATUS_REJECTED     = 'rejected';
    const STATUS_FEE_REQUIRED = 'fee_required';

    protected $fillable = [
        'user_id', 'admin_id', 'reference', 'type',
        'amount', 'currency',
        'beneficiary_name', 'beneficiary_iban',
        'note', 'admin_note', 'invoice_id',
        'status', 'processed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING      => 'En attente',
            self::STATUS_COMPLETED    => 'Validé',
            self::STATUS_REJECTED     => 'Rejeté',
            self::STATUS_FEE_REQUIRED => 'Frais requis',
            default                   => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING      => 'orange',
            self::STATUS_COMPLETED    => 'green',
            self::STATUS_REJECTED     => 'red',
            self::STATUS_FEE_REQUIRED => 'blue',
            default                   => 'gray',
        };
    }

    public static function generateReference(): string
    {
        $year = now()->format('Y');
        $seq  = self::whereYear('created_at', $year)->count() + 1;
        return 'TRF-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
}
