<style>
.esim-page {
    --esim-accent: #4f46e5;
    --esim-accent-2: #7c3aed;
    --cp-accent: #4f46e5;
    --cp-hero-bg: linear-gradient(135deg, #312e81 0%, #4f46e5 48%, #7c3aed 100%);
    --cp-hero-shadow: rgba(79, 70, 229, .32);
}

.esim-filters {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 18px;
    padding: 1rem 1.1rem;
    box-shadow: 0 8px 28px rgba(15, 23, 42, .05);
    margin-bottom: 1.25rem;
}

.esim-filters .form-label {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: .35rem;
}

.esim-filters .form-select {
    border-radius: 12px;
    border-color: #e2e8f0;
    min-height: 2.65rem;
    font-weight: 600;
    color: #0f172a;
    background-color: #f8fafc;
}

.esim-filters .form-select:focus {
    border-color: #a78bfa;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, .15);
    background: #fff;
}

.esim-filters__actions {
    display: flex;
    gap: .5rem;
    align-items: flex-end;
    height: 100%;
    padding-bottom: 2px;
}

.esim-btn {
    border: 0;
    border-radius: 12px;
    padding: .7rem 1.1rem;
    font-weight: 700;
    font-size: .88rem;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff;
    box-shadow: 0 8px 20px rgba(79, 70, 229, .28);
    transition: transform .15s, box-shadow .15s;
}

.esim-btn:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 12px 26px rgba(79, 70, 229, .34);
}

.esim-btn-ghost {
    background: #f1f5f9;
    color: #475569;
    box-shadow: none;
}

.esim-btn-ghost:hover {
    background: #e2e8f0;
    color: #0f172a;
    box-shadow: none;
    transform: none;
}

.esim-results-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    margin-bottom: .85rem;
}

.esim-results-meta h3 {
    font-size: .95rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.esim-results-meta span {
    font-size: .78rem;
    color: #64748b;
    font-weight: 600;
}

.esim-package {
    position: relative;
    border: 1px solid #e8edf5;
    border-radius: 18px;
    padding: 1.1rem 1rem 1rem;
    background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
    height: 100%;
    display: flex;
    flex-direction: column;
    gap: .45rem;
    transition: border-color .18s, box-shadow .18s, transform .18s;
    overflow: hidden;
}

.esim-package::before {
    content: '';
    position: absolute;
    inset: 0 0 auto 0;
    height: 3px;
    background: linear-gradient(90deg, #4f46e5, #a855f7);
    opacity: 0;
    transition: opacity .18s;
}

.esim-package:hover {
    border-color: #c4b5fd;
    box-shadow: 0 14px 32px rgba(79, 70, 229, .12);
    transform: translateY(-2px);
}

.esim-package:hover::before { opacity: 1; }

.esim-package__top {
    display: flex;
    align-items: flex-start;
    gap: .7rem;
}

.esim-package__icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eef2ff;
    color: #4f46e5;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.esim-package__name {
    font-weight: 800;
    color: #0f172a;
    font-size: .92rem;
    line-height: 1.25;
    letter-spacing: -.01em;
}

.esim-package__location {
    font-size: .75rem;
    font-weight: 600;
    color: #64748b;
    margin-top: .15rem;
}

.esim-chips {
    display: flex;
    flex-wrap: wrap;
    gap: .35rem;
    margin: .15rem 0 .25rem;
}

.esim-chip {
    font-size: .68rem;
    font-weight: 700;
    padding: .22rem .5rem;
    border-radius: 999px;
    background: #f1f5f9;
    color: #475569;
}

.esim-chip--data { background: #eef2ff; color: #4338ca; }
.esim-chip--days { background: #f5f3ff; color: #6d28d9; }
.esim-chip--speed { background: #ecfeff; color: #0e7490; }
.esim-chip--privacy { background: #ecfdf5; color: #047857; }

.esim-package__price {
    font-weight: 800;
    font-size: 1.25rem;
    color: #4f46e5;
    letter-spacing: -.02em;
    margin-top: auto;
    padding-top: .35rem;
}

.esim-package__price small {
    display: block;
    font-size: .68rem;
    font-weight: 600;
    color: #94a3b8;
    letter-spacing: 0;
}

.esim-empty {
    border: 0;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 8px 28px rgba(15, 23, 42, .05);
}

.esim-empty .esim-empty__icon {
    width: 4rem;
    height: 4rem;
    border-radius: 1.1rem;
    background: #eef2ff;
    color: #6366f1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin-bottom: 1rem;
}

.esim-order {
    border: 0;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 8px 28px rgba(15, 23, 42, .05);
}

.esim-status {
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: .3rem .65rem;
    border-radius: 999px;
}

.esim-status--completed { background: #dcfce7; color: #166534; }
.esim-status--processing { background: #e0e7ff; color: #3730a3; }
.esim-status--failed { background: #fee2e2; color: #991b1b; }

.esim-qr {
    max-width: 140px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

[data-pc-theme="dark"] .esim-filters,
[data-pc-theme="dark"] .esim-package,
[data-pc-theme="dark"] .esim-empty,
[data-pc-theme="dark"] .esim-order {
    background: #1e293b;
    border-color: #334155;
}

[data-pc-theme="dark"] .esim-package {
    background: linear-gradient(180deg, #1e293b 0%, #172033 100%);
}

[data-pc-theme="dark"] .esim-filters .form-select {
    background: #0f172a;
    border-color: #334155;
    color: #e2e8f0;
}

[data-pc-theme="dark"] .esim-package__name,
[data-pc-theme="dark"] .esim-results-meta h3 { color: #f1f5f9; }

[data-pc-theme="dark"] .esim-chip { background: #334155; color: #cbd5e1; }
[data-pc-theme="dark"] .esim-chip--data { background: rgba(99,102,241,.2); color: #c7d2fe; }
[data-pc-theme="dark"] .esim-chip--days { background: rgba(124,58,237,.2); color: #ddd6fe; }
</style>
