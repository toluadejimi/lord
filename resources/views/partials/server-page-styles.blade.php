{{-- Shared server verification UI (Servers 1–4) — include once per page --}}
<style>
.sv-page {
    --sv-accent: #4f46e5;
    --sv-accent-soft: #eef2ff;
    --sv-accent-border: #c7d2fe;
    --sv-hero-bg: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #a855f7 100%);
    --sv-hero-shadow: rgba(79, 70, 229, .25);
    --sv-price-bg: #f5f3ff;
    --sv-price-border: #c4b5fd;
}

.sv-page.sv-theme-2 {
    --sv-accent: #ea580c;
    --sv-accent-soft: #fff7ed;
    --sv-accent-border: #fdba74;
    --sv-hero-bg: linear-gradient(135deg, #c2410c 0%, #ea580c 50%, #fb923c 100%);
    --sv-hero-shadow: rgba(234, 88, 12, .28);
    --sv-price-bg: #fff7ed;
    --sv-price-border: #fdba74;
}

.sv-page.sv-theme-3 {
    --sv-accent: #0891b2;
    --sv-accent-soft: #ecfeff;
    --sv-accent-border: #67e8f9;
    --sv-hero-bg: linear-gradient(135deg, #0e7490 0%, #0891b2 50%, #22d3ee 100%);
    --sv-hero-shadow: rgba(8, 145, 178, .28);
    --sv-price-bg: #ecfeff;
    --sv-price-border: #67e8f9;
}

.sv-page.sv-theme-4 {
    --sv-accent: #059669;
    --sv-accent-soft: #ecfdf5;
    --sv-accent-border: #6ee7b7;
    --sv-hero-bg: linear-gradient(135deg, #047857 0%, #059669 50%, #34d399 100%);
    --sv-hero-shadow: rgba(5, 150, 105, .28);
    --sv-price-bg: #ecfdf5;
    --sv-price-border: #6ee7b7;
}

.sv-hero {
    background: var(--sv-hero-bg);
    border-radius: 16px;
    color: #fff;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 10px 30px var(--sv-hero-shadow);
}

.sv-server-pill {
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
}

.sv-server-pill .sv-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.35rem;
    height: 1.35rem;
    border-radius: 50%;
    background: rgba(255,255,255,.22);
    font-size: .7rem;
}

.sv-wallet-block {
    text-align: right;
}

.sv-card {
    border: 0;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(15, 23, 42, .06);
    overflow: hidden;
}

.sv-card .card-body { padding: 1.35rem; }

.sv-step {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--sv-accent);
    margin-bottom: .55rem;
}

.sv-field .input-group-text {
    background: #fff;
    border-color: #e2e8f0;
    border-radius: 10px 0 0 10px;
    color: #94a3b8;
}

.sv-field .form-control {
    border-radius: 0 10px 10px 0;
    border-color: #e2e8f0;
    padding: .72rem .9rem;
    font-size: .9rem;
}

.sv-field .form-control:focus,
.sv-card .form-control:focus {
    border-color: var(--sv-accent);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--sv-accent) 18%, transparent);
}

.sv-field--solo .form-control {
    border-radius: 10px;
    border-left: 1px solid #e2e8f0;
}

.sv-picker-list {
    max-height: 280px;
    overflow-y: auto;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    -webkit-overflow-scrolling: touch;
}

.sv-picker-list .list-group-item {
    cursor: pointer;
    border-color: #f1f5f9;
    padding: .72rem 1rem;
    font-size: .875rem;
    transition: background .12s;
}

.sv-picker-list .list-group-item:hover,
.sv-picker-list .list-group-item:focus {
    background: var(--sv-accent-soft);
}

.sv-picker-list .list-group-item-action:active {
    background: var(--sv-accent-soft);
    color: var(--sv-accent);
}

.sv-search-dropdown {
    position: absolute;
    width: 100%;
    z-index: 1050;
    margin-top: .35rem;
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(15, 23, 42, .12);
    max-height: 50vh;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

.sv-search-dropdown .list-group-item {
    cursor: pointer;
    border-color: #f1f5f9;
    padding: .72rem 1rem;
    font-size: .875rem;
    transition: background .12s;
}

.sv-search-dropdown .list-group-item:hover,
.sv-search-dropdown .list-group-item:active {
    background: var(--sv-accent-soft);
    color: var(--sv-accent);
}

.sv-selected-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .4rem .85rem;
    background: var(--sv-accent-soft);
    border: 1px solid var(--sv-accent-border);
    border-radius: 999px;
    font-size: .82rem;
    font-weight: 600;
    color: var(--sv-accent);
}

.sv-price-box {
    background: var(--sv-price-bg);
    border-radius: 14px;
    border: 1px dashed var(--sv-price-border);
    padding: 1.25rem;
}

.sv-price-box .amount {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--sv-accent);
    line-height: 1.2;
}

.sv-btn-rent {
    border: 0;
    border-radius: 12px;
    padding: .9rem 1rem;
    font-weight: 700;
    font-size: 1rem;
    background: var(--sv-hero-bg);
    color: #fff;
    box-shadow: 0 8px 20px var(--sv-hero-shadow);
    transition: filter .15s, transform .15s;
}

.sv-btn-rent:hover:not(:disabled) {
    filter: brightness(1.06);
    color: #fff;
}

.sv-btn-rent:disabled {
    opacity: .55;
    box-shadow: none;
}

.sv-service-card {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 1rem 1.1rem;
    margin-bottom: .75rem;
    cursor: pointer;
    transition: .15s ease;
    background: #fff;
}

.sv-service-card:hover {
    border-color: var(--sv-accent-border);
    box-shadow: 0 8px 24px color-mix(in srgb, var(--sv-accent) 12%, transparent);
    transform: translateY(-1px);
}

.sv-service-card .svc-name { font-weight: 700; color: #0f172a; font-size: .92rem; }
.sv-service-card .svc-price { font-weight: 800; color: #059669; font-size: 1rem; }
.sv-service-card .svc-meta { font-size: .78rem; color: #64748b; }

.sv-operator-title {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #94a3b8;
    margin: 1.1rem 0 .45rem;
}

.sv-empty-hint {
    color: #94a3b8;
    font-size: .875rem;
    text-align: center;
    padding: 1.75rem 1rem;
}

/* Orders panel — mobile cards */
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
    .sv-page .pc-content { padding: 0.85rem !important; }

    .sv-hero {
        padding: 1rem 1.1rem;
        border-radius: 14px;
    }

    .sv-hero .h4, .sv-hero h2 {
        font-size: 1.15rem !important;
    }

    .sv-wallet-block {
        width: 100%;
        text-align: left !important;
        margin-top: .5rem;
        background: rgba(255,255,255,.12);
        border-radius: 12px;
        padding: .65rem .85rem;
    }

    .sv-card .card-body { padding: 1rem !important; }

    .sv-picker-list { max-height: 45vh; }

    .sv-btn-rent {
        position: sticky;
        bottom: .5rem;
        z-index: 10;
    }

    /* Orders: card layout on mobile */
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

    .vo-panel .vo-cancel-btn {
        width: 100%;
        margin-top: .25rem;
    }

    .vo-panel .px-4.pt-4 {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
}
</style>
