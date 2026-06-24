<?php

namespace App\Services;

use App\Models\ContractTemplate;
use App\Models\DocumentAuditLog;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Enregistre chaque action sensible sur les documents dans document_audit_logs.
 * Table append-only — pas d'UPDATE, pas de DELETE.
 */
class AuditLogger
{
    public function log(
        string            $action,
        ?LoanRequest      $loan     = null,
        ?ContractTemplate $template = null,
        int               $version  = 0,
        array             $payload  = [],
    ): DocumentAuditLog {
        return DocumentAuditLog::create([
            'action'               => $action,
            'user_id'              => Auth::id(),
            'loan_request_id'      => $loan?->id,
            'contract_template_id' => $template?->id,
            'template_version'     => $version,
            'ip'                   => request()->ip(),
            'payload'              => $payload ?: null,
        ]);
    }
}
