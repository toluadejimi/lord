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
    width: 100%;
    cursor: pointer;
    font: inherit;
    appearance: none;
    -webkit-appearance: none;
}

button.dash-service-tile { text-align: center; }

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

/* ——— Numbers bottom sheet ——— */
body.dash-sheet-open { overflow: hidden; }

.dash-sheet-root {
    position: fixed;
    inset: 0;
    z-index: 1080;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    pointer-events: none;
}

.dash-sheet-root.is-open { pointer-events: auto; }

.dash-sheet-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    opacity: 0;
    transition: opacity 0.28s ease;
    backdrop-filter: blur(3px);
}

.dash-sheet-root.is-open .dash-sheet-backdrop { opacity: 1; }

.dash-sheet {
    position: relative;
    width: min(100%, 520px);
    max-height: min(78vh, 560px);
    background: #fff;
    border-radius: 20px 20px 0 0;
    box-shadow: 0 -12px 40px rgba(15, 23, 42, 0.18);
    padding: 0.5rem 1rem 1.25rem;
    padding-bottom: calc(1.25rem + env(safe-area-inset-bottom, 0px));
    transform: translateY(100%);
    transition: transform 0.32s cubic-bezier(0.32, 0.72, 0, 1);
    overflow: auto;
}

.dash-sheet-root.is-open .dash-sheet { transform: translateY(0); }

.dash-sheet-handle {
    width: 2.5rem;
    height: 0.28rem;
    border-radius: 999px;
    background: #cbd5e1;
    margin: 0.35rem auto 0.85rem;
}

.dash-sheet-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.35rem;
}

.dash-sheet-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.dash-sheet-close {
    width: 2rem;
    height: 2rem;
    border: 0;
    border-radius: 10px;
    background: #f1f5f9;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
}

.dash-sheet-sub {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0 0 0.85rem;
}

.dash-sheet-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.dash-sheet-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 0.9rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    text-decoration: none;
    color: #0f172a;
    transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
}

.dash-sheet-option:hover {
    background: #fff;
    border-color: #c7d2fe;
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.1);
    color: #0f172a;
}

.dash-sheet-option-text {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    min-width: 0;
}

.dash-sheet-option-name {
    font-size: 0.9rem;
    font-weight: 700;
}

.dash-sheet-option-hint {
    font-size: 0.72rem;
    color: #64748b;
}

.dash-sheet-option-arrow {
    margin-left: auto;
    color: #94a3b8;
    flex-shrink: 0;
}

/* ——— Dark theme ——— */
[data-pc-theme="dark"] .dash-app .pc-content.dash-page {
    background: linear-gradient(180deg, #0f172a 0%, #131c2e 28%, #0b1220 100%);
}

[data-pc-theme="dark"] .dash-greeting { color: #94a3b8; }
[data-pc-theme="dark"] .dash-user { color: #f1f5f9; }

[data-pc-theme="dark"] .dash-brand-pill {
    background: #1e293b;
    border-color: #334155;
    color: #a5b4fc;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
}

[data-pc-theme="dark"] .dash-brand-pill span { color: #c4b5fd; }

[data-pc-theme="dark"] .dash-section-title { color: #f1f5f9; }
[data-pc-theme="dark"] .dash-see-all { color: #a5b4fc; }
[data-pc-theme="dark"] .dash-see-all:hover { color: #c7d2fe; }

[data-pc-theme="dark"] .dash-service-tile {
    background: #1e293b;
    border-color: #334155;
    color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
}

[data-pc-theme="dark"] .dash-service-tile:hover {
    background: #243044;
    border-color: #475569;
    color: #f1f5f9;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.28);
}

[data-pc-theme="dark"] .dash-tone-emerald .dash-service-icon { background: rgba(5, 150, 105, 0.18); color: #34d399; }
[data-pc-theme="dark"] .dash-tone-blue .dash-service-icon { background: rgba(37, 99, 235, 0.18); color: #60a5fa; }
[data-pc-theme="dark"] .dash-tone-violet .dash-service-icon { background: rgba(124, 58, 237, 0.18); color: #a78bfa; }
[data-pc-theme="dark"] .dash-tone-slate .dash-service-icon { background: rgba(71, 85, 105, 0.35); color: #cbd5e1; }
[data-pc-theme="dark"] .dash-tone-amber .dash-service-icon { background: rgba(217, 119, 6, 0.18); color: #fbbf24; }
[data-pc-theme="dark"] .dash-tone-sky .dash-service-icon { background: rgba(2, 132, 199, 0.18); color: #38bdf8; }
[data-pc-theme="dark"] .dash-tone-indigo .dash-service-icon { background: rgba(79, 70, 229, 0.2); color: #a5b4fc; }
[data-pc-theme="dark"] .dash-tone-rose .dash-service-icon { background: rgba(225, 29, 72, 0.18); color: #fb7185; }

[data-pc-theme="dark"] .dash-server-row,
[data-pc-theme="dark"] .dash-activity-card {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

[data-pc-theme="dark"] .dash-server-row:hover {
    border-color: #6366f1;
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.15);
    color: #f1f5f9;
}

[data-pc-theme="dark"] .dash-server-name,
[data-pc-theme="dark"] .dash-activity-service,
[data-pc-theme="dark"] .dash-activity-amount { color: #f1f5f9; }

[data-pc-theme="dark"] .dash-activity-meta { color: #94a3b8; }
[data-pc-theme="dark"] .dash-activity-row { border-bottom-color: #334155; }

[data-pc-theme="dark"] .dash-sheet {
    background: #1e293b;
    box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.45);
}

[data-pc-theme="dark"] .dash-sheet-handle { background: #475569; }
[data-pc-theme="dark"] .dash-sheet-title { color: #f1f5f9; }
[data-pc-theme="dark"] .dash-sheet-sub { color: #94a3b8; }

[data-pc-theme="dark"] .dash-sheet-close {
    background: #334155;
    color: #cbd5e1;
}

[data-pc-theme="dark"] .dash-sheet-option {
    background: #0f172a;
    border-color: #334155;
    color: #f1f5f9;
}

[data-pc-theme="dark"] .dash-sheet-option:hover {
    background: #131c2e;
    border-color: #6366f1;
    color: #f1f5f9;
}

[data-pc-theme="dark"] .dash-sheet-option-hint { color: #94a3b8; }
[data-pc-theme="dark"] .dash-sheet-option-arrow { color: #64748b; }

[data-pc-theme="dark"] .dash-alert.alert-success {
    background: rgba(5, 150, 105, 0.15);
    color: #6ee7b7;
}

[data-pc-theme="dark"] .dash-alert.alert-danger {
    background: rgba(220, 38, 38, 0.15);
    color: #fca5a5;
}
</style>
