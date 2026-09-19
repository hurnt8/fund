{{--
    Modale de confirmation unique, pilotée par attribut.

    Usage — remplace `onsubmit="return confirm('…')"` :

        <form ... data-confirm="Supprimer ce modèle ?">
        <a href="…" data-confirm="Quitter sans enregistrer ?">
        <button type="button" data-confirm="…" data-confirm-danger>

    Attributs facultatifs :
        data-confirm-title   titre affiché (défaut : « Confirmation »)
        data-confirm-ok      libellé du bouton de validation (défaut : « Confirmer »)
        data-confirm-danger  présence = action destructrice (bouton rouge)

    Vanilla JS : la modale doit rester opérationnelle sur les vues autonomes
    qui ne chargent pas Alpine (visionneuses de contrat et d'attestation).
--}}

<div id="cfm-backdrop" role="dialog" aria-modal="true" aria-labelledby="cfm-title" hidden>
    <div id="cfm-box">
        <div id="cfm-head">
            <span id="cfm-icon" aria-hidden="true"><i class="fas fa-circle-question"></i></span>
            <h3 id="cfm-title">Confirmation</h3>
        </div>
        <p id="cfm-text"></p>
        <div id="cfm-actions">
            <button type="button" id="cfm-cancel">Annuler</button>
            <button type="button" id="cfm-ok">Confirmer</button>
        </div>
    </div>
</div>

<style>
#cfm-backdrop{
    position:fixed; inset:0; z-index:9000;
    background:rgba(8,42,32,.55);
    -webkit-backdrop-filter:blur(2px); backdrop-filter:blur(2px);
    display:flex; align-items:center; justify-content:center;
    padding:1.25rem;
    opacity:0; transition:opacity .16s ease;
}
#cfm-backdrop[hidden]{ display:none; }
#cfm-backdrop.is-open{ opacity:1; }

#cfm-box{
    background:#fff; width:100%; max-width:420px;
    border-radius:4px; border:1px solid #E4E8F0;
    box-shadow:0 18px 50px rgba(8,42,32,.28);
    padding:1.5rem;
    transform:translateY(8px) scale(.99);
    transition:transform .16s ease;
}
#cfm-backdrop.is-open #cfm-box{ transform:none; }

#cfm-head{ display:flex; align-items:center; gap:.75rem; margin-bottom:.75rem; }
#cfm-icon{
    width:40px; height:40px; flex-shrink:0; border-radius:50%;
    background:#F5EDDD; color:#9A7736;
    display:flex; align-items:center; justify-content:center; font-size:1rem;
}
#cfm-backdrop.is-danger #cfm-icon{ background:#FDECEC; color:#C0392B; }
#cfm-title{
    margin:0; font-size:1.05rem; font-weight:700; color:#0E3B2E;
    font-family:Georgia,'Times New Roman',serif;
}
#cfm-text{
    margin:0 0 1.25rem; font-size:.9rem; line-height:1.6; color:#5A6472;
    overflow-wrap:break-word;
}
#cfm-actions{ display:flex; gap:.6rem; justify-content:flex-end; flex-wrap:wrap; }
#cfm-actions button{
    border:none; cursor:pointer; padding:.6rem 1.15rem;
    border-radius:3px; font-size:.85rem; font-weight:700;
    font-family:inherit; transition:filter .15s ease, background .15s ease;
}
#cfm-cancel{ background:#EEF1F5; color:#3C4451; }
#cfm-cancel:hover{ background:#E2E7EE; }
#cfm-ok{ background:#0E3B2E; color:#FBF9F4; }
#cfm-ok:hover{ filter:brightness(1.18); }
#cfm-backdrop.is-danger #cfm-ok{ background:#C0392B; }
#cfm-actions button:focus-visible{ outline:2px solid #C6A15B; outline-offset:2px; }

@media (max-width:480px){
    #cfm-actions{ flex-direction:column-reverse; }
    #cfm-actions button{ width:100%; }
}
</style>

<script>
(function () {
    var backdrop = document.getElementById('cfm-backdrop');
    if (!backdrop) return;

    var boxText  = document.getElementById('cfm-text'),
        boxTitle = document.getElementById('cfm-title'),
        btnOk    = document.getElementById('cfm-ok'),
        btnNo    = document.getElementById('cfm-cancel'),
        cible    = null,      // élément à réactiver après confirmation
        renvoi   = null;      // élément qui avait le focus avant ouverture

    function ouvrir(el, message) {
        cible  = el;
        renvoi = document.activeElement;

        boxText.textContent  = message;
        boxTitle.textContent = el.getAttribute('data-confirm-title') || 'Confirmation';
        btnOk.textContent    = el.getAttribute('data-confirm-ok') || 'Confirmer';
        backdrop.classList.toggle('is-danger', el.hasAttribute('data-confirm-danger'));

        backdrop.hidden = false;
        requestAnimationFrame(function () { backdrop.classList.add('is-open'); });
        btnOk.focus();
    }

    function fermer() {
        backdrop.classList.remove('is-open');
        setTimeout(function () { backdrop.hidden = true; }, 160);
        if (renvoi && typeof renvoi.focus === 'function') renvoi.focus();
        cible = null;
    }

    function valider() {
        var el = cible;
        fermer();
        if (!el) return;

        // Marqueur : au second passage on laisse filer l'action d'origine.
        el.setAttribute('data-confirm-ok-once', '1');

        if (el.tagName === 'FORM') {
            if (typeof el.requestSubmit === 'function') el.requestSubmit();
            else el.submit();
        } else {
            el.click();
        }
    }

    btnOk.addEventListener('click', valider);
    btnNo.addEventListener('click', fermer);
    backdrop.addEventListener('mousedown', function (e) { if (e.target === backdrop) fermer(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !backdrop.hidden) fermer();
    });

    // Piège à focus : on garde la tabulation à l'intérieur de la modale.
    backdrop.addEventListener('keydown', function (e) {
        if (e.key !== 'Tab') return;
        var f = [btnNo, btnOk];
        var i = f.indexOf(document.activeElement);
        e.preventDefault();
        f[(i + (e.shiftKey ? -1 : 1) + f.length) % f.length].focus();
    });

    function intercepte(e, el) {
        if (el.getAttribute('data-confirm-ok-once') === '1') {
            el.removeAttribute('data-confirm-ok-once');
            return;                       // déjà confirmé : on laisse passer
        }
        e.preventDefault();
        e.stopPropagation();
        ouvrir(el, el.getAttribute('data-confirm'));
    }

    // Capture : on passe avant les autres gestionnaires de la page.
    document.addEventListener('submit', function (e) {
        var f = e.target.closest ? e.target.closest('form[data-confirm]') : null;
        if (f) intercepte(e, f);
    }, true);

    document.addEventListener('click', function (e) {
        if (!e.target.closest) return;
        var el = e.target.closest('[data-confirm]');
        if (!el || el.tagName === 'FORM') return;
        intercepte(e, el);
    }, true);
})();
</script>
