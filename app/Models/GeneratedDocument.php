<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedDocument extends Model
{
    protected $fillable = [
        'loan_request_id',
        'contract_template_id',
        'generated_by',
        'template_version',
        'locale',
        'docx_path',
        'pdf_path',
        'checksum',
        'vars_snapshot',
    ];

    protected $casts = [
        'vars_snapshot' => 'array',
    ];

    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class);
    }

    public function contractTemplate()
    {
        return $this->belongsTo(ContractTemplate::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
