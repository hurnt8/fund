@props(['paginator'])

@if ($paginator->hasPages())
@php
    $window   = \Illuminate\Pagination\UrlWindow::make($paginator);
    $elements = array_filter([
        $window['first'],
        is_array($window['slider']) ? '...' : null,
        $window['slider'],
        is_array($window['last']) ? '...' : null,
        $window['last'],
    ]);
@endphp

@once
@push('styles')
<style>
.lp-wrap{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.lp-info{font-size:.75rem;color:var(--c-muted, var(--ca-text-3, #7D9488))}
.lp-info strong{color:var(--c-navy, var(--ca-text, #0E3B2E));font-weight:700}
.lp-pages{display:flex;align-items:center;gap:.3rem;list-style:none;margin:0;padding:0}
.lp-btn{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 .5rem;border:1.5px solid var(--c-border, var(--ca-border, #E4E8F0));border-radius:8px;background:var(--c-card, var(--ca-bg3, #fff));color:var(--c-navy, var(--ca-text, #0E3B2E));font-size:.78rem;font-weight:600;text-decoration:none;transition:.15s;cursor:pointer}
.lp-btn:hover{border-color:var(--c-gold, var(--ca-gold, #C6A15B));color:var(--c-gold-d, var(--ca-gold-l, #9A7736));text-decoration:none;background:var(--c-card-2, var(--ca-bg4, #fffdf7))}
.lp-btn--active{background:var(--c-navy, var(--ca-gold, #0E3B2E));border-color:var(--c-navy, var(--ca-gold, #0E3B2E));color:var(--c-on-navy, #fff);cursor:default}
.lp-btn--active:hover{border-color:var(--c-navy, var(--ca-gold, #0E3B2E));color:var(--c-on-navy, #fff);background:var(--c-navy, var(--ca-gold, #0E3B2E))}
.lp-btn--disabled{opacity:.35;cursor:default;pointer-events:none}
.lp-dots{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;color:var(--c-muted, var(--ca-text-3, #7D9488));font-size:.78rem}
@media(max-width:640px){
  .lp-wrap{justify-content:center;text-align:center}
  .lp-pages{order:-1;width:100%;justify-content:center}
}
</style>
@endpush
@endonce

<nav class="lp-wrap" role="navigation" aria-label="Pagination">
  <div class="lp-info">
    @if ($paginator->firstItem())
      Affichage de <strong>{{ $paginator->firstItem() }}</strong> à <strong>{{ $paginator->lastItem() }}</strong> sur <strong>{{ $paginator->total() }}</strong> résultat(s)
    @else
      {{ $paginator->total() }} résultat(s)
    @endif
  </div>

  <ul class="lp-pages">
    <li>
      @if ($paginator->onFirstPage())
        <span class="lp-btn lp-btn--disabled" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" class="lp-btn" rel="prev" aria-label="Précédent"><i class="fas fa-chevron-left"></i></a>
      @endif
    </li>

    @foreach ($elements as $element)
      @if (is_string($element))
        <li><span class="lp-dots">{{ $element }}</span></li>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          <li>
            @if ($page == $paginator->currentPage())
              <span class="lp-btn lp-btn--active" aria-current="page">{{ $page }}</span>
            @else
              <a href="{{ $url }}" class="lp-btn" aria-label="Page {{ $page }}">{{ $page }}</a>
            @endif
          </li>
        @endforeach
      @endif
    @endforeach

    <li>
      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="lp-btn" rel="next" aria-label="Suivant"><i class="fas fa-chevron-right"></i></a>
      @else
        <span class="lp-btn lp-btn--disabled" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
      @endif
    </li>
  </ul>
</nav>
@endif
