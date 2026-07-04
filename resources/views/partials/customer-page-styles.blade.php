{{-- Shared customer page hero + wallet UI — include once per page --}}
<style>
.cp-page {
    --cp-accent: #4f46e5;
    --cp-accent-soft: #eef2ff;
    --cp-hero-bg: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #a855f7 100%);
    --cp-hero-shadow: rgba(79, 70, 229, .25);
}

.cp-page.cp-theme-2 {
    --cp-accent: #ea580c;
    --cp-accent-soft: #fff7ed;
    --cp-hero-bg: linear-gradient(135deg, #c2410c 0%, #ea580c 50%, #fb923c 100%);
    --cp-hero-shadow: rgba(234, 88, 12, .28);
}

.cp-page.cp-theme-3 {
    --cp-accent: #0891b2;
    --cp-accent-soft: #ecfeff;
    --cp-hero-bg: linear-gradient(135deg, #0e7490 0%, #0891b2 50%, #22d3ee 100%);
    --cp-hero-shadow: rgba(8, 145, 178, .28);
}

.cp-page.cp-theme-4 {
    --cp-accent: #059669;
    --cp-accent-soft: #ecfdf5;
    --cp-hero-bg: linear-gradient(135deg, #047857 0%, #059669 50%, #34d399 100%);
    --cp-hero-shadow: rgba(5, 150, 105, .28);
}

.cp-page.cp-theme-telegram {
    --cp-accent: #229ed9;
    --cp-accent-soft: #f0f9ff;
    --cp-hero-bg: linear-gradient(135deg, #0088cc 0%, #229ed9 50%, #54c7f5 100%);
    --cp-hero-shadow: rgba(0, 136, 204, .28);
}

.cp-hero {
    background: var(--cp-hero-bg);
    border-radius: 16px;
    color: #fff;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 10px 30px var(--cp-hero-shadow);
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

@media (min-width: 768px) {
    .cp-hero {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.25rem;
    }
}

.cp-hero__main { flex: 0 1 auto; min-width: 0; }

@media (min-width: 768px) {
    .cp-hero__main { flex: 1 1 220px; }
}

.cp-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 999px;
    padding: .32rem .8rem;
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .02em;
    margin-bottom: .5rem;
}

.cp-hero__badge-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.35rem;
    height: 1.35rem;
    border-radius: 50%;
    background: rgba(255,255,255,.22);
    font-size: .7rem;
}

.cp-hero h1, .cp-hero h2 {
    color: #fff;
    font-weight: 700;
    margin-bottom: .25rem;
}

.cp-hero__subtitle {
    margin: 0;
    font-size: .875rem;
    opacity: .9;
    line-height: 1.45;
}

.cp-hero__back {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    color: rgba(255,255,255,.88);
    text-decoration: none;
    font-size: .8125rem;
    margin-bottom: .5rem;
}

.cp-hero__back:hover { color: #fff; }

.cp-hero-nav {
    display: flex;
    flex-wrap: wrap;
    gap: .45rem;
    margin-top: .85rem;
}

.cp-hero-nav a {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .38rem .78rem;
    border-radius: 999px;
    font-size: .76rem;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.22);
    color: #fff;
    text-decoration: none;
    transition: background .15s;
}

.cp-hero-nav a:hover {
    background: rgba(255,255,255,.22);
    color: #fff;
}

.cp-wallet-card {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
    background: rgba(255,255,255,.14);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 14px;
    padding: .9rem 1rem;
    flex: 0 1 auto;
    min-width: min(100%, 260px);
}

.cp-wallet-card__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 12px;
    background: rgba(255,255,255,.2);
    flex-shrink: 0;
}

.cp-wallet-card__icon i {
    font-size: 1.2rem;
    color: #fff;
}

.cp-wallet-card__body {
    flex: 1;
    min-width: 0;
}

.cp-wallet-card__label {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .8;
    margin-bottom: .15rem;
}

.cp-wallet-card__amount {
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -.02em;
    margin-bottom: .55rem;
    word-break: break-word;
}

.cp-wallet-card__actions {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
}

.cp-wallet-card__btn {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .38rem .72rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s, transform .15s;
    white-space: nowrap;
}

.cp-wallet-card__btn--primary {
    background: #fff;
    color: var(--cp-accent, #4f46e5);
    box-shadow: 0 4px 12px rgba(0,0,0,.12);
}

.cp-wallet-card__btn--primary:hover {
    background: #f8fafc;
    color: var(--cp-accent, #4f46e5);
    transform: translateY(-1px);
}

.cp-wallet-card__btn--ghost {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff;
}

.cp-wallet-card__btn--ghost:hover {
    background: rgba(255,255,255,.22);
    color: #fff;
}

.cp-card {
    border: 0;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(15, 23, 42, .06);
    overflow: hidden;
}

.cp-card .card-body { padding: 1.35rem; }

.cp-subnav {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    margin-bottom: 1.25rem;
    padding: .35rem;
    background: #f1f5f9;
    border-radius: 999px;
    width: fit-content;
    max-width: 100%;
}

.cp-subnav a {
    padding: .45rem 1rem;
    border-radius: 999px;
    text-decoration: none;
    color: #475569;
    font-size: .875rem;
    font-weight: 600;
    transition: .15s;
}

.cp-subnav a:hover {
    color: #1e293b;
    background: rgba(255,255,255,.6);
}

.cp-subnav a.active {
    background: #fff;
    color: var(--cp-accent, #4f46e5);
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}

/* Verification orders — mobile card layout */
.vo-panel.sv-orders-panel {
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(15, 23, 42, .06);
}

.vo-panel .vo-table thead th {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #64748b;
    background: #f8fafc;
}

@media (max-width: 767px) {
    .cp-page .pc-content { padding: 0.85rem !important; }

    .cp-hero {
        padding: 0.9rem 1rem;
        border-radius: 14px;
        gap: 0.65rem;
    }

    .cp-hero__main {
        flex: 0 0 auto;
    }

    .cp-hero__badge {
        margin-bottom: 0.35rem;
    }

    .cp-hero h1, .cp-hero h2 {
        font-size: 1.15rem !important;
        margin-bottom: 0.15rem !important;
    }

    .cp-hero__subtitle {
        font-size: 0.8125rem;
        line-height: 1.35;
    }

    .cp-wallet-card {
        width: 100%;
        min-width: 0;
        padding: 0.75rem 0.85rem;
        margin-top: 0;
    }

    .cp-wallet-card__amount {
        font-size: 1.2rem;
    }

    .cp-wallet-card__actions {
        width: 100%;
    }

    .cp-wallet-card__btn {
        flex: 1 1 auto;
        justify-content: center;
        min-width: calc(50% - .25rem);
    }

    .cp-card .card-body { padding: 1rem !important; }

    .vo-panel .table-responsive { border: 0; }
    .vo-panel .vo-table thead { display: none; }
    .vo-panel .vo-table tbody tr {
        display: block;
        margin: 0 .85rem .75rem;
        padding: .85rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
    }
    .vo-panel .vo-table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .35rem 0 !important;
        border: 0 !important;
        font-size: .82rem;
    }
    .vo-panel .vo-table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #64748b;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-right: .75rem;
        flex-shrink: 0;
    }
    .vo-panel .vo-table tbody td.text-end {
        justify-content: flex-end;
        padding-top: .5rem !important;
        border-top: 1px dashed #e2e8f0 !important;
        margin-top: .35rem;
    }
    .vo-panel .vo-table tbody td.text-end::before { display: none; }
    .vo-panel .vo-cancel-btn { width: 100%; margin-top: .25rem; }
    .vo-panel .px-4.pt-4 {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
}
</style>
