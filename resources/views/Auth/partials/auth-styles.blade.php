<style>
    :root {
        --brand: #4f46e5;
        --brand-2: #7c3aed;
        --ink: #0f172a;
        --muted: #64748b;
    }
    .guest-body { font-family: Inter, system-ui, sans-serif; min-height: 100vh; margin: 0; }
    .auth-page { min-height: 100vh; display: flex; flex-wrap: wrap; }
    .auth-brand {
        flex: 1 1 45%;
        min-width: 280px;
        background: linear-gradient(160deg, #0f172a 0%, #3730a3 50%, #7c3aed 100%);
        color: #fff;
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .auth-brand::after {
        content: '';
        position: absolute;
        width: 320px; height: 320px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        top: -80px; right: -80px;
    }
    .auth-brand .inner { position: relative; z-index: 1; max-width: 400px; }
    .auth-brand h1 { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; line-height: 1.2; }
    .auth-brand p { color: rgba(255,255,255,.8); }
    .auth-feature {
        display: flex; align-items: center; gap: .75rem;
        margin-bottom: .85rem; font-size: .95rem;
    }
    .auth-form-side {
        flex: 1 1 55%;
        min-width: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1.5rem;
        background: #fff;
    }
    .auth-form-wrap { width: 100%; max-width: 400px; }
    .auth-form-wrap h2 { font-size: 1.5rem; font-weight: 800; color: var(--ink); }
    .auth-form-wrap .sub { color: var(--muted); font-size: .9rem; margin-bottom: 1.75rem; }
    .auth-form .form-label { font-size: .8rem; font-weight: 600; color: var(--muted); }
    .auth-form .form-control {
        border-radius: 10px;
        border-color: #e2e8f0;
        padding: .7rem .9rem;
    }
    .auth-form .form-control:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(79,70,229,.12);
    }
    .btn-auth {
        border: 0; border-radius: 10px; padding: .8rem;
        font-weight: 700; width: 100%;
        background: linear-gradient(135deg, var(--brand), var(--brand-2));
        color: #fff;
        box-shadow: 0 8px 20px rgba(79,70,229,.25);
    }
    .btn-auth:hover { filter: brightness(1.05); color: #fff; }
    .auth-switch {
        text-align: center;
        margin-top: 1.5rem;
        font-size: .9rem;
        color: var(--muted);
    }
    .auth-switch a { font-weight: 700; color: var(--brand); text-decoration: none; }
    .auth-switch a:hover { text-decoration: underline; }
    .auth-top-link {
        position: absolute;
        top: 1.25rem;
        left: 1.25rem;
        z-index: 10;
        color: #fff;
        text-decoration: none;
        font-size: .875rem;
        opacity: .9;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .auth-top-link:hover { color: #fff; opacity: 1; }
    .auth-home-link {
        color: var(--muted);
        font-size: .875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        margin-bottom: 1.5rem;
    }
    .auth-home-link:hover { color: var(--brand); }
</style>
