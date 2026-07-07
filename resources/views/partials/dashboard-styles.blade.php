{{-- Otpsuite-inspired customer dashboard — SMSLord colors --}}
<style>
.dash-app .pc-content.dash-page {
    background: linear-gradient(180deg, #f0f4ff 0%, #f8fafc 28%, #f1f5f9 100%);
    min-height: calc(100vh - 56px);
    padding: 1rem 1rem 2rem !important;
}

.dash-alert { border-radius: 14px; margin-bottom: 1rem; }

.dash-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.1rem;
}

.dash-greeting {
    font-size: 0.82rem;
    color: #64748b;
    margin: 0 0 0.2rem;
    font-weight: 500;
}

.dash-user {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    letter-spacing: -0.02em;
    line-height: 1.2;
    word-break: break-word;
}

.dash-brand-pill {
    flex-shrink: 0;
    padding: 0.45rem 0.85rem;
    border-radius: 999px;
    background: #fff;
    border: 1px solid #e2e8f0;
    font-weight: 800;
    font-size: 0.78rem;
    color: #4f46e5;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.08);
}

.dash-brand-pill span { color: #7c3aed; }

.dash-balance-card {
    display: block;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 48%, #a855f7 100%);
    border-radius: 20px;
    padding: 1.35rem 1.4rem 1.15rem;
    color: #fff;
    box-shadow: 0 16px 40px rgba(79, 70, 229, 0.32);
    margin-bottom: 1.5rem;
    transition: transform 0.15s, box-shadow 0.15s;
}

.dash-balance-card:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 20px 48px rgba(79, 70, 229, 0.38);
}

.dash-balance-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    opacity: 0.88;
    margin-bottom: 0.35rem;
}

.dash-balance-amount {
    font-size: clamp(1.75rem, 6vw, 2.15rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.15;
    margin-bottom: 0.85rem;
}

.dash-balance-cta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.82rem;
    font-weight: 600;
    padding-top: 0.65rem;
    border-top: 1px solid rgba(255, 255, 255, 0.22);
    opacity: 0.95;
}

.dash-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.85rem;
}

.dash-section-title {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.dash-see-all {
    font-size: 0.8rem;
    font-weight: 600;
    color: #4f46e5;
    text-decoration: none;
}

.dash-see-all:hover { color: #4338ca; }

.dash-services-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.65rem;
}

@media (min-width: 576px) {
    .dash-services-grid { gap: 0.85rem; }
    .dash-page { max-width: 720px; margin: 0 auto; }
}

.dash-service-tile {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    padding: 0.85rem 0.35rem;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #eef2f7;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
    text-decoration: none;
    color: #334155;
    transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s;
    min-height: 88px;
}

.dash-service-tile:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    border-color: #e2e8f0;
    color: #0f172a;
}

.dash-service-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
}

.dash-tone-emerald .dash-service-icon { background: #ecfdf5; color: #059669; }
.dash-tone-blue .dash-service-icon { background: #eff6ff; color: #2563eb; }
.dash-tone-violet .dash-service-icon { background: #f5f3ff; color: #7c3aed; }
.dash-tone-slate .dash-service-icon { background: #f1f5f9; color: #475569; }
.dash-tone-amber .dash-service-icon { background: #fffbeb; color: #d97706; }
.dash-tone-sky .dash-service-icon { background: #f0f9ff; color: #0284c7; }
.dash-tone-indigo .dash-service-icon { background: #eef2ff; color: #4f46e5; }
.dash-tone-rose .dash-service-icon { background: #fff1f2; color: #e11d48; }

.dash-service-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-align: center;
    line-height: 1.2;
}

@media (min-width: 400px) {
    .dash-service-label { font-size: 0.72rem; }
}

.dash-server-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.dash-server-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    background: #fff;
    border-radius: 14px;
    border: 1px solid #eef2f7;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    text-decoration: none;
    color: #0f172a;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.dash-server-row:hover {
    border-color: #c7d2fe;
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.08);
    color: #0f172a;
}

.dash-server-num {
    width: 2rem;
    height: 2rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff;
    font-size: 0.8rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.dash-server-name {
    font-size: 0.88rem;
    font-weight: 600;
}

.dash-activity-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #eef2f7;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}

.dash-activity-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.9rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.dash-activity-row:last-child { border-bottom: 0; }

.dash-activity-service {
    font-size: 0.85rem;
    font-weight: 700;
    color: #0f172a;
}

.dash-activity-meta {
    font-size: 0.72rem;
    color: #64748b;
    margin-top: 0.15rem;
}

.dash-activity-amount {
    font-size: 0.82rem;
    font-weight: 700;
    color: #0f172a;
}

.dash-activity-status {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.dash-st-ok { color: #059669; }
.dash-st-pending { color: #d97706; }
.dash-st-muted { color: #94a3b8; }

@media (max-width: 380px) {
    .dash-services-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.45rem;
    }
    .dash-service-tile { min-height: 78px; padding: 0.65rem 0.2rem; }
    .dash-service-icon { width: 2.15rem; height: 2.15rem; font-size: 1rem; }
}
</style>
