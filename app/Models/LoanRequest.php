<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{
    use HasFactory;

    // Statuts possibles
    const STATUS_DRAFT           = 'draft';
    const STATUS_PENDING         = 'pending';
    const STATUS_VALIDATED       = 'validated';
    const STATUS_CONTRACT_SENT   = 'contract_sent';
    const STATUS_CONTRACT_SIGNED = 'contract_signed';
    const STATUS_FINALIZED       = 'finalized';
    const STATUS_REJECTED        = 'rejected';

    const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PENDING,
        self::STATUS_VALIDATED,
        self::STATUS_CONTRACT_SENT,
        self::STATUS_CONTRACT_SIGNED,
        self::STATUS_FINALIZED,
        self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'reference', 'archive_ref',
        'admin_id', 'client_id', 'contract_template_id', 'insurance_template_id',
        'name', 'email', 'phone', 'address',
        'amount', 'interest_rate', 'currency', 'start_date',
        'monthly_payment', 'total_cost', 'total_with_interest',
        'admin_fees', 'frais_assurance', 'date_fin_assurance', 'bank_account', 'agent_suivi', 'directeur',
        'darly', 'objet', 'subject', 'npi',
        'extra_fields',
        'special_conditions',
        'contract_content', 'contract_pdf_path', 'insurance_pdf_path', 'contract_language',
        'amortization_schedule',
        'status', 'notes', 'files',
        'validated_at', 'sent_at', 'signed_received_at',
    ];

    protected $casts = [
        'files'                => 'array',
        'extra_fields'         => 'array',
        'amortization_schedule'=> 'array',
        'start_date'           => 'date',
        'validated_at'         => 'datetime',
        'sent_at'              => 'datetime',
        'signed_received_at'   => 'datetime',
        'amount'               => 'decimal:2',
        'monthly_payment'      => 'decimal:2',
        'total_cost'           => 'decimal:2',
        'total_with_interest'  => 'decimal:2',
        'admin_fees'           => 'decimal:2',
        'frais_assurance'      => 'decimal:2',
        'date_fin_assurance'   => 'date',
        'interest_rate'        => 'decimal:2',
    ];

    // Relations
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function template()
    {
        return $this->belongsTo(ContractTemplate::class, 'contract_template_id');
    }

    public function contractTemplate()
    {
        return $this->belongsTo(ContractTemplate::class, 'contract_template_id');
    }

    public function insuranceTemplate()
    {
        return $this->belongsTo(ContractTemplate::class, 'insurance_template_id');
    }

    public function history()
    {
        return $this->hasMany(LoanHistory::class)->latest();
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class)->latest();
    }

    // Helpers
    public function isEditable(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_PENDING,
            self::STATUS_VALIDATED,
            self::STATUS_CONTRACT_SENT,
        ]);
    }

    public function canBeValidated(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING]);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT           => __('app.status_draft'),
            self::STATUS_PENDING         => __('app.status_pending'),
            self::STATUS_VALIDATED       => __('app.status_validated'),
            self::STATUS_CONTRACT_SENT   => __('app.status_sent'),
            self::STATUS_CONTRACT_SIGNED => __('app.status_signed'),
            self::STATUS_FINALIZED       => __('app.status_finalized'),
            self::STATUS_REJECTED        => __('app.status_rejected'),
            default                      => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT           => 'secondary',
            self::STATUS_PENDING         => 'warning',
            self::STATUS_VALIDATED       => 'info',
            self::STATUS_CONTRACT_SENT   => 'primary',
            self::STATUS_CONTRACT_SIGNED => 'success',
            self::STATUS_FINALIZED       => 'dark',
            self::STATUS_REJECTED        => 'danger',
            default                      => 'secondary',
        };
    }

    // Génère une référence unique CR-YYYY-XXXX
    public static function generateReference(): string
    {
        $year  = now()->format('Y');
        $last  = self::whereYear('created_at', $year)->count() + 1;
        return 'CR-' . $year . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
