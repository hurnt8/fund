<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAuditLog extends Model
{
    public const UPDATED_AT = null;

    public const ACTION_TEMPLATE_UPLOADED  = 'TEMPLATE_UPLOADED';
    public const ACTION_TEMPLATE_VERSIONED = 'TEMPLATE_VERSIONED';
    public const ACTION_DOCUMENT_GENERATED = 'DOCUMENT_GENERATED';
    public const ACTION_DOCUMENT_DOWNLOADED = 'DOCUMENT_DOWNLOADED';
    public const ACTION_DOCUMENT_EMAILED   = 'DOCUMENT_EMAILED';

    protected $fillable = [
        'action',
        'user_id',
        'loan_request_id',
        'contract_template_id',
        'template_version',
        'ip',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class);
    }

    public function contractTemplate()
    {
        return $this->belongsTo(ContractTemplate::class);
    }
}
