{{-- Otpsuite-inspired profile page — SMSLord colors --}}
<style>
.prof-app .pc-content.prof-page {
    background: linear-gradient(180deg, #f0f4ff 0%, #f8fafc 24%, #f1f5f9 100%);
    min-height: calc(100vh - 56px);
    padding: 1rem 1rem 2rem !important;
    max-width: 560px;
    margin: 0 auto;
}

.prof-alert { border-radius: 14px; margin-bottom: 1rem; }

.prof-hero {
    text-align: center;
    padding: 0.5rem 0 1.25rem;
}

.prof-hero--compact { text-align: left; padding-top: 0; }

.prof-back {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #4f46e5;
    text-decoration: none;
    margin-bottom: 0.85rem;
}

.prof-back:hover { color: #4338ca; }

.prof-avatar {
    width: 4.5rem;
    height: 4.5rem;
    margin: 0 auto 0.85rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #a855f7 100%);
    color: #fff;
    font-size: 1.35rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 12px 28px rgba(79, 70, 229, 0.28);
}

.prof-name {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 0.25rem;
    letter-spacing: -0.02em;
}

.prof-email, .prof-phone {
    margin: 0;
    font-size: 0.85rem;
    color: #64748b;
}

.prof-phone { margin-top: 0.15rem; }

.prof-wallet-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 48%, #a855f7 100%);
    border-radius: 18px;
    padding: 1.1rem 1.2rem;
    color: #fff;
    margin-bottom: 1.35rem;
    box-shadow: 0 14px 36px rgba(79, 70, 229, 0.28);
    transition: transform 0.15s, box-shadow 0.15s;
}

.prof-wallet-card:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 18px 42px rgba(79, 70, 229, 0.34);
}

.prof-wallet-label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    opacity: 0.9;
}

.prof-wallet-amount {
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    margin-top: 0.15rem;
}

.prof-wallet-cta {
    font-size: 0.82rem;
    font-weight: 700;
    white-space: nowrap;
    opacity: 0.95;
}

.prof-section-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
    margin: 0 0 0.5rem 0.35rem;
}

.prof-menu-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #eef2f7;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
    overflow: hidden;
    margin-bottom: 1.1rem;
}

.prof-form-card { padding: 1.15rem 1.1rem 1.25rem; }

.prof-menu-row {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.9rem 1rem;
    text-decoration: none;
    color: #0f172a;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
}

.prof-menu-row:last-child { border-bottom: 0; }
.prof-menu-row:hover { background: #f8fafc; color: #0f172a; }

.prof-menu-icon {
    width: 2.35rem;
    height: 2.35rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
}

.prof-tone-wallet { background: #eef2ff; color: #4f46e5; }
.prof-tone-logs { background: #f1f5f9; color: #475569; }
.prof-tone-numbers { background: #f5f3ff; color: #7c3aed; }
.prof-tone-lock { background: #fff7ed; color: #ea580c; }
.prof-tone-api { background: #ecfeff; color: #0891b2; }
.prof-tone-telegram { background: #f0f9ff; color: #0284c7; }
.prof-tone-policy { background: #ecfdf5; color: #059669; }
.prof-tone-terms { background: #fef3c7; color: #d97706; }

.prof-menu-text {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    min-width: 0;
    flex: 1;
}

.prof-menu-title {
    font-size: 0.9rem;
    font-weight: 700;
}

.prof-menu-hint {
    font-size: 0.72rem;
    color: #64748b;
}

.prof-menu-arrow {
    color: #94a3b8;
    flex-shrink: 0;
    font-size: 1rem;
}

.prof-logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    width: 100%;
    padding: 0.9rem 1rem;
    border-radius: 16px;
    background: #fff;
    border: 1px solid #fecaca;
    color: #dc2626;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    transition: background 0.15s, border-color 0.15s;
}

.prof-logout-btn:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
}

.prof-input {
    border-radius: 12px;
    border-color: #e2e8f0;
    padding: 0.7rem 0.9rem;
}

.prof-input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
}

.prof-submit {
    border: 0;
    border-radius: 14px;
    padding: 0.85rem 1rem;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.28);
}

.prof-submit:hover { filter: brightness(1.05); color: #fff; }

[data-pc-theme="dark"] .prof-app .pc-content.prof-page {
    background: linear-gradient(180deg, #0f172a 0%, #131c2e 24%, #0b1220 100%);
}

[data-pc-theme="dark"] .prof-name { color: #f1f5f9; }
[data-pc-theme="dark"] .prof-email,
[data-pc-theme="dark"] .prof-phone { color: #94a3b8; }
[data-pc-theme="dark"] .prof-section-label { color: #94a3b8; }

[data-pc-theme="dark"] .prof-menu-card {
    background: #1e293b;
    border-color: #334155;
}

[data-pc-theme="dark"] .prof-menu-row {
    border-bottom-color: #334155;
    color: #f1f5f9;
}

[data-pc-theme="dark"] .prof-menu-row:hover { background: #243044; color: #f1f5f9; }
[data-pc-theme="dark"] .prof-menu-hint { color: #94a3b8; }

[data-pc-theme="dark"] .prof-logout-btn {
    background: #1e293b;
    border-color: #7f1d1d;
    color: #fca5a5;
}

[data-pc-theme="dark"] .prof-logout-btn:hover {
    background: #2a1515;
    color: #fecaca;
}

[data-pc-theme="dark"] .prof-input {
    background: #0f172a;
    border-color: #334155;
    color: #f1f5f9;
}
</style>
