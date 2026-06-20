<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'content', 'is_default', 'created_by',
        'template_type', 'locale', 'docx_path', 'detected_tags',
        'watermark_path', 'logo_left_path', 'logo_right_path',
        'stamp_path', 'signature_admin_path', 'signature_agent_path',
    ];

    protected $casts = [
        'is_default'    => 'boolean',
        'detected_tags' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function loans()
    {
        return $this->hasMany(LoanRequest::class, 'contract_template_id');
    }
}
