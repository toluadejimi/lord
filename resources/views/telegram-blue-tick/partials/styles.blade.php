<style>
.tbt-page { --tbt-accent: #229ed9; --tbt-accent-2: #0088cc; }
.tbt-hero {
    background: linear-gradient(135deg, #0088cc 0%, #229ed9 50%, #54c7f5 100%);
    border-radius: 16px; color: #fff; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;
    box-shadow: 0 10px 30px rgba(0, 136, 204, .28);
}
.tbt-hero h2 { color: #fff; font-weight: 700; }
.tbt-wallet-pill {
    background: rgba(255,255,255,.15); backdrop-filter: blur(6px);
    border-radius: 12px; padding: .65rem 1rem; text-align: right;
}
.tbt-subnav {
    display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.25rem;
    padding: .35rem; background: #f1f5f9; border-radius: 999px; width: fit-content;
}
.tbt-subnav a {
    padding: .45rem 1rem; border-radius: 999px; text-decoration: none;
    color: #475569; font-size: .875rem; font-weight: 600;
}
.tbt-subnav a.active { background: #fff; color: var(--tbt-accent); box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.tbt-card { border: 0; border-radius: 16px; box-shadow: 0 4px 24px rgba(15, 23, 42, .06); }
.tbt-package {
    border: 2px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; cursor: pointer;
    transition: .15s; height: 100%; background: #fff;
}
.tbt-package:hover, .tbt-package.selected {
    border-color: var(--tbt-accent); background: #f0f9ff;
    box-shadow: 0 0 0 3px rgba(34, 158, 217, .12);
}
.tbt-package .months { font-size: 1.1rem; font-weight: 700; }
.tbt-package .price { font-size: 1.35rem; font-weight: 800; color: var(--tbt-accent-2); }
.tbt-recipient {
    border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; background: #f8fafc;
}
.tbt-recipient img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
.tbt-submit {
    border: 0; border-radius: 12px; padding: .85rem 1.25rem; font-weight: 700;
    background: linear-gradient(135deg, #0088cc, #229ed9); color: #fff;
    box-shadow: 0 8px 20px rgba(0,136,204,.3);
}
.tbt-submit:disabled { opacity: .55; box-shadow: none; }
.tbt-badge-pending { background: #fef3c7; color: #92400e; }
.tbt-badge-completed { background: #dcfce7; color: #166534; }
.tbt-badge-failed, .tbt-badge-refunded { background: #fee2e2; color: #991b1b; }
</style>
