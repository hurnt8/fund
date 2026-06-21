@php
$badgeClass = match($loan->status) {
    'draft'            => 'sb-draft',
    'pending','validated' => 'sb-pending',
    'contract_sent'    => 'sb-sent',
    'contract_signed'  => 'sb-signed',
    'finalized'        => 'sb-done',
    'rejected'         => 'sb-rejected',
    default            => 'sb-draft',
};
$statusLabel = match($loan->status) {
    'draft'            => __('app.status_draft'),
    'pending'          => __('app.status_pending'),
    'validated'        => __('app.status_validated'),
    'contract_sent'    => __('app.status_sent'),
    'contract_signed'  => __('app.status_signed'),
    'finalized'        => __('app.status_finalized'),
    'rejected'         => __('app.status_rejected'),
    default            => $loan->status,
};
@endphp
<a href="{{ route('client.loans.show', $loan) }}" style="text-decoration:none">
  <div class="app-card" style="margin-bottom:.6rem;transition:border-color .15s"
       onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='var(--border)'">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.65rem">
      <div>
        <div style="font-family:monospace;font-size:.78rem;color:var(--gold);font-weight:700;margin-bottom:.2rem">
          {{ $loan->reference }}
        </div>
        <div class="fw-600" style="font-size:.95rem;color:var(--text)">
          {{ number_format((float)$loan->amount, 2, ',', ' ') }}
          <span style="font-size:.8rem;color:var(--gold)">{{ $loan->currency }}</span>
        </div>
      </div>
      <span class="status-badge {{ $badgeClass }}">{{ $statusLabel }}</span>
    </div>
    <div style="display:flex;gap:1rem">
      <div class="fs-xs text-muted">
        <i class="fas fa-calendar-alt" style="margin-right:.3rem"></i>
        {{ $loan->darly }} {{ __('app.months') }}
      </div>
      <div class="fs-xs text-muted">
        <i class="fas fa-redo" style="margin-right:.3rem"></i>
        {{ number_format((float)$loan->monthly_payment, 2, ',', ' ') }} {{ $loan->currency }}{{ __('app.per_month') }}
      </div>
    </div>
    @if($loan->objet)
    <div class="fs-xs text-muted" style="margin-top:.45rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
      <i class="fas fa-tag" style="margin-right:.3rem"></i>{{ $loan->objet }}
    </div>
    @endif
  </div>
</a>
