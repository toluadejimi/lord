{{-- VAS-specific styles (hero/wallet/subnav use shared cp-* partials) --}}
<style>
.vas-page { --vas-accent: #6366f1; --vas-accent-2: #8b5cf6; --cp-accent: #6366f1; }
.vas-card {
    border: 0; border-radius: 16px; box-shadow: 0 4px 24px rgba(15, 23, 42, .06);
    overflow: hidden;
}
.vas-card .card-body { padding: 1.5rem; }
.vas-field-label {
    font-size: .75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em; color: #64748b; margin-bottom: .5rem;
}
.vas-input, .vas-page .form-control, .vas-page .form-select {
    border-radius: 10px; border-color: #e2e8f0; padding: .65rem .85rem;
}
.vas-input:focus, .vas-page .form-control:focus, .vas-page .form-select:focus {
    border-color: var(--vas-accent); box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.network-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .75rem; }
@media (min-width: 576px) { .network-grid { grid-template-columns: repeat(4, 1fr); } }
.network-chip {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .5rem; padding: 1rem .5rem; border: 2px solid #e2e8f0; border-radius: 14px;
    background: #fff; cursor: pointer; transition: .15s; min-height: 88px;
    font-weight: 600; font-size: .875rem; color: #334155;
}
.network-chip:hover { border-color: #c7d2fe; background: #f8fafc; }
.network-chip.selected {
    border-color: var(--vas-accent); background: #eef2ff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
}
.network-logo {
    width: 40px; height: 40px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; font-weight: 800; font-size: .7rem; color: #fff;
}
.network-logo.mtn { background: #ffcc00; color: #1a1a1a; }
.network-logo.glo { background: #00a651; }
.network-logo.airtel { background: #ed1c24; }
.network-logo.n9mobile { background: #006848; }
.amount-chips { display: flex; flex-wrap: wrap; gap: .5rem; }
.amount-chip {
    border: 1px solid #e2e8f0; background: #fff; border-radius: 999px;
    padding: .35rem .85rem; font-size: .8125rem; font-weight: 600; color: #475569; cursor: pointer;
}
.amount-chip:hover, .amount-chip.active { border-color: var(--vas-accent); color: var(--vas-accent); background: #eef2ff; }
.vas-submit {
    border: 0; border-radius: 12px; padding: .85rem 1rem; font-weight: 700;
    background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 8px 20px rgba(79,70,229,.3);
    color: #fff;
}
.vas-submit:hover:not(:disabled) { filter: brightness(1.05); color: #fff; }
.vas-submit:disabled { opacity: .55; box-shadow: none; }
.bundle-card {
    cursor: pointer; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: .85rem; transition: .15s; height: 100%;
}
.bundle-card:hover, .bundle-card.selected { border-color: var(--vas-accent); background: #eef2ff; }
.vtu-service-card {
    border: 0; border-radius: 16px; transition: transform .15s, box-shadow .15s;
    box-shadow: 0 4px 20px rgba(15,23,42,.06);
}
.vtu-service-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(15,23,42,.1); }
.vtu-icon-wrap {
    width: 52px; height: 52px; border-radius: 14px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem;
}
</style>
