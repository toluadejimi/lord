@extends('layout.main')
@section('content')
@php
    $exampleKey = 'YOUR_API_KEY';
@endphp
<style>
.api-page { --api-accent: #0f766e; }
.api-hero {
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 45%, #14b8a6 100%);
    border-radius: 16px; color: #fff; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;
    box-shadow: 0 10px 30px rgba(15, 118, 110, .28);
}
.api-card { border: 0; border-radius: 16px; box-shadow: 0 4px 24px rgba(15, 23, 42, .06); }
.api-key-box {
    background: #0f172a; color: #e2e8f0; border-radius: 12px; padding: 1rem 1.1rem;
    font-family: ui-monospace, monospace; font-size: .85rem; word-break: break-all;
}
.api-pill {
    display: inline-flex; align-items: center; gap: .35rem; padding: .28rem .65rem;
    border-radius: 999px; font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em;
}
.api-pill-get { background: #dbeafe; color: #1d4ed8; }
.api-pill-post { background: #dcfce7; color: #15803d; }
.api-endpoint {
    border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: .75rem; overflow: hidden;
}
.api-endpoint summary {
    cursor: pointer; padding: .85rem 1rem; background: #f8fafc; font-weight: 600;
    list-style: none; display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
}
.api-endpoint summary::-webkit-details-marker { display: none; }
.api-endpoint-body { padding: 1rem; border-top: 1px solid #e2e8f0; }
.api-code {
    background: #0f172a; color: #cbd5e1; border-radius: 10px; padding: .85rem 1rem;
    font-size: .78rem; overflow-x: auto; margin: 0;
}
.api-code .k { color: #7dd3fc; }
.api-code .s { color: #86efac; }
.api-nav .nav-link { font-size: .85rem; font-weight: 600; color: #64748b; border: 0; }
.api-nav .nav-link.active { color: #0f766e; border-bottom: 2px solid #0f766e; background: transparent; }
.api-security-list li { margin-bottom: .5rem; }
.api-once-banner {
    background: linear-gradient(90deg, #fef3c7, #fff7ed); border: 1px solid #fcd34d;
    border-radius: 12px; padding: 1rem 1.1rem;
}
</style>

<div class="pc-container">
    <div class="pc-content p-4 api-page">
        <div class="api-hero d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <span class="badge bg-white text-dark mb-2">Reseller API v1</span>
                <h1 class="h4 mb-1">API Documentation</h1>
                <p class="mb-0 small opacity-90">Integrate SMS verification into your app. Authenticate every request with your private API key.</p>
            </div>
            <div class="text-end text-white">
                <div class="small opacity-75">Wallet balance</div>
                <div class="h4 mb-0 fw-bold">₦{{ number_format((float) $user->wallet, 2) }}</div>
                <div class="small opacity-75 mt-1">Base URL: <code class="text-white">{{ $baseUrl }}</code></div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        @if ($showFullKey && $fullKey)
            <div class="api-once-banner mb-4">
                <div class="fw-bold text-warning-emphasis mb-1"><i class="ti ti-alert-triangle"></i> Copy your API key now</div>
                <p class="small mb-2 text-muted">This is the only time the full key is shown automatically. Store it in a secrets manager — never in client-side code or public repos.</p>
                <div class="api-key-box d-flex justify-content-between align-items-center gap-2">
                    <span id="new-key-full">{{ $fullKey }}</span>
                    <button type="button" class="btn btn-sm btn-light" onclick="copyText('{{ $fullKey }}')">Copy</button>
                </div>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card api-card mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="ti ti-key text-success"></i> API credentials</h5>
                        <label class="form-label small text-muted mb-1">Your API key (masked)</label>
                        <div class="api-key-box mb-3" id="api-key-display">{{ $maskedKey }}</div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-reveal-key">Reveal key</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-copy-masked" data-copy="{{ $maskedKey }}">Copy masked</button>
                        </div>

                        <form method="post" action="{{ url('api-docs/regenerate') }}" class="border-top pt-3" onsubmit="return confirm('Regenerating invalidates your current key everywhere. Continue?');">
                            @csrf
                            <label class="form-label small fw-semibold">Regenerate key</label>
                            <p class="small text-muted">Requires your account password. Old keys stop working immediately.</p>
                            <input type="password" name="password" class="form-control form-control-sm mb-2" placeholder="Account password" required autocomplete="current-password">
                            <button class="btn btn-warning btn-sm w-100">Regenerate API key</button>
                        </form>
                    </div>
                </div>

                <div class="card api-card mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="ti ti-webhook text-primary"></i> Webhook</h5>
                        <p class="small text-muted">Receive OTP codes via HTTPS POST when an order completes. Payloads are signed with HMAC-SHA256.</p>
                        <form method="post" action="{{ url('api-docs/webhook') }}">
                            @csrf
                            <label class="form-label small">Webhook URL (HTTPS only)</label>
                            <input class="form-control form-control-sm mb-2" name="webhook_url" value="{{ $user->webhook_url }}" placeholder="https://yoursite.com/smslord-webhook">
                            <input type="password" name="password" class="form-control form-control-sm mb-2" placeholder="Account password to confirm" required autocomplete="current-password">
                            <button class="btn btn-primary btn-sm w-100">Save webhook</button>
                        </form>
                    </div>
                </div>

                <div class="card api-card">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-danger mb-2"><i class="ti ti-shield-lock"></i> Security rules</h6>
                        <ul class="small text-muted api-security-list ps-3 mb-0">
                            <li>Never expose your API key in mobile apps, browsers, or GitHub.</li>
                            <li>Use the <code>Authorization: Bearer</code> header — not URL query strings.</li>
                            <li>Verify webhook signatures before trusting OTP codes.</li>
                            <li>Rate limit: 120 requests/minute per account; failed auth is throttled by IP.</li>
                            <li>Only rent numbers server-side after checking price and balance.</li>
                            <li>Report stolen keys immediately via regenerate.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <ul class="nav nav-tabs api-nav border-0 mb-3" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-auth">Authentication</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-world">World SMS</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-usa">USA (Server 2)</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-webhooks">Webhooks</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-errors">Errors</button></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-auth">
                        <div class="card api-card">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-2">Authentication</h5>
                                <p class="text-muted small">Every endpoint requires your API key. <strong>Recommended:</strong> send it in a header so it is not logged in access logs.</p>

                                <p class="small fw-semibold mb-1">Preferred — Authorization header</p>
                                <pre class="api-code mb-3">Authorization: Bearer {{ $exampleKey }}</pre>

                                <p class="small fw-semibold mb-1">Alternative headers</p>
                                <pre class="api-code mb-3">X-API-Key: {{ $exampleKey }}
X-SMSLORD-Key: {{ $exampleKey }}</pre>

                                <p class="small fw-semibold mb-1 text-warning">Avoid — query string (insecure)</p>
                                <pre class="api-code mb-3">{{ $baseUrl }}/balance?api_key={{ $exampleKey }}</pre>

                                <p class="small fw-semibold mb-1">Example request (cURL)</p>
                                <pre class="api-code">curl -X POST "{{ $baseUrl }}/balance" \
  -H "Authorization: Bearer {{ $exampleKey }}" \
  -H "Accept: application/json"</pre>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-world">
                        <div class="card api-card">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">World SMS (SMSPool)</h5>

                                <details class="api-endpoint" open>
                                    <summary><span class="api-pill api-pill-get">GET/POST</span> balance</summary>
                                    <div class="api-endpoint-body">
                                        <p class="small text-muted mb-2">Returns your wallet balance in NGN.</p>
                                        <pre class="api-code">{"success": true, "balance": 15000.00}</pre>
                                    </div>
                                </details>

                                <details class="api-endpoint">
                                    <summary><span class="api-pill api-pill-get">GET/POST</span> get-world-countries</summary>
                                    <div class="api-endpoint-body">
                                        <p class="small text-muted mb-2">List available countries for world verification.</p>
                                        <pre class="api-code">{"success": true, "data": { ... }}</pre>
                                    </div>
                                </details>

                                <details class="api-endpoint">
                                    <summary><span class="api-pill api-pill-get">GET/POST</span> get-world-services</summary>
                                    <div class="api-endpoint-body">
                                        <p class="small text-muted mb-2">List services (WhatsApp, Telegram, etc.).</p>
                                    </div>
                                </details>

                                <details class="api-endpoint">
                                    <summary><span class="api-pill api-pill-post">POST</span> check-world-number-availability</summary>
                                    <div class="api-endpoint-body">
                                        <p class="small mb-2"><strong>Parameters:</strong> <code>country</code>, <code>service</code></p>
                                        <pre class="api-code">{"success": true, "usd": 1.20, "price": 1850.00, "stock": 42}</pre>
                                        <p class="small text-muted mb-0">Always check price before renting. Charged amount matches <code>price</code> (NGN).</p>
                                    </div>
                                </details>

                                <details class="api-endpoint">
                                    <summary><span class="api-pill api-pill-post">POST</span> rent-world-number</summary>
                                    <div class="api-endpoint-body">
                                        <p class="small mb-2"><strong>Parameters:</strong> <code>country</code>, <code>service</code></p>
                                        <pre class="api-code">{
  "success": true,
  "order_id": 12345,
  "phone": "447911123456",
  "provider_order_id": "abc-provider-id"
}</pre>
                                        <p class="small text-muted mb-0"><code>order_id</code> is your SMSLORD order ID — use it for polling and cancel.</p>
                                    </div>
                                </details>

                                <details class="api-endpoint">
                                    <summary><span class="api-pill api-pill-post">POST</span> get-world-sms</summary>
                                    <div class="api-endpoint-body">
                                        <p class="small mb-2"><strong>Parameters:</strong> <code>order_id</code></p>
                                        <pre class="api-code">{
  "success": true,
  "status": 2,
  "code": "123456",
  "full_sms": "Your code is 123456"
}</pre>
                                        <p class="small text-muted mb-0">Status: <code>1</code> pending · <code>2</code> completed · <code>99</code> cancelled. Poll every 10–15s max.</p>
                                    </div>
                                </details>

                                <details class="api-endpoint">
                                    <summary><span class="api-pill api-pill-post">POST</span> cancel-world-sms</summary>
                                    <div class="api-endpoint-body">
                                        <p class="small mb-2"><strong>Parameters:</strong> <code>order_id</code></p>
                                        <pre class="api-code">{"success": true, "message": "Order cancelled and wallet refunded."}</pre>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-usa">
                        <div class="card api-card">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">USA Server 2</h5>

                                <details class="api-endpoint" open>
                                    <summary><span class="api-pill api-pill-post">POST</span> get-usa-sms</summary>
                                    <div class="api-endpoint-body">
                                        <p class="small mb-2"><strong>Parameters:</strong> <code>order_id</code> (USA2 / type 4 orders only)</p>
                                        <pre class="api-code">{"success": true, "status": 2, "code": "654321"}</pre>
                                    </div>
                                </details>

                                <details class="api-endpoint">
                                    <summary><span class="api-pill api-pill-post">POST</span> cancel-usa-sms</summary>
                                    <div class="api-endpoint-body">
                                        <p class="small mb-2"><strong>Parameters:</strong> <code>order_id</code></p>
                                        <p class="small text-muted mb-0">USA2 orders cannot be cancelled within 120 seconds of creation.</p>
                                    </div>
                                </details>

                                <div class="alert alert-secondary border-0 small mb-0 mt-3">
                                    <strong>Retired:</strong> <code>usa-services</code> and <code>rent-usa-number</code> return HTTP 410. Use the dashboard for new USA rentals or contact support for API expansion.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-webhooks">
                        <div class="card api-card">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-2">Outbound webhooks</h5>
                                <p class="text-muted small">When an order completes, SMSLORD POSTs JSON to your HTTPS webhook URL.</p>

                                <p class="small fw-semibold mb-1">Payload</p>
                                <pre class="api-code mb-3">{
  "phone": "447911123456",
  "code": "123456",
  "service": "whatsapp",
  "order_id": "12345",
  "full_sms": "Your WhatsApp code: 123456",
  "country": "GB",
  "timestamp": 1710000000
}</pre>

                                <p class="small fw-semibold mb-1">Headers</p>
                                <pre class="api-code mb-3">Content-Type: application/json
X-SMSLORD-Signature: &lt;hmac-sha256-hex&gt;
X-SMSLORD-Timestamp: &lt;unix timestamp&gt;</pre>

                                <p class="small fw-semibold mb-1">Verify signature (PHP)</p>
                                <pre class="api-code mb-0">$body = file_get_contents('php://input');
$expected = hash_hmac('sha256', $body, $yourApiKey);
if (!hash_equals($expected, $_SERVER['HTTP_X_SMSLORD_SIGNATURE'] ?? '')) {
    http_response_code(401);
    exit('Invalid signature');
}</pre>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-errors">
                        <div class="card api-card">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">HTTP status codes</h5>
                                <table class="table table-sm">
                                    <thead><tr><th>Code</th><th>Meaning</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>200</code></td><td>Success</td></tr>
                                        <tr><td><code>401</code></td><td>Invalid or missing API key</td></tr>
                                        <tr><td><code>404</code></td><td>Order not found (wrong order_id or not your order)</td></tr>
                                        <tr><td><code>410</code></td><td>Endpoint retired</td></tr>
                                        <tr><td><code>422</code></td><td>Business error (insufficient balance, cancel failed, etc.)</td></tr>
                                        <tr><td><code>429</code></td><td>Rate limit exceeded — back off and retry</td></tr>
                                    </tbody>
                                </table>
                                <p class="small text-muted mb-0">Orders are scoped to your account. You cannot read or cancel another user's <code>order_id</code>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="revealKeyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title">Reveal API key</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Enter your account password. Do not share the key or display it on stream recordings.</p>
                <input type="password" class="form-control" id="reveal-password" placeholder="Account password" autocomplete="current-password">
                <div class="text-danger small mt-2 d-none" id="reveal-error"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="reveal-submit">Reveal</button>
            </div>
        </div>
    </div>
</div>

<script>
function copyText(text) {
    navigator.clipboard.writeText(text).then(function () {
        alert('Copied to clipboard.');
    }).catch(function () {
        prompt('Copy this key:', text);
    });
}

document.getElementById('btn-copy-masked')?.addEventListener('click', function () {
    copyText(this.getAttribute('data-copy') || '');
});

document.getElementById('btn-reveal-key')?.addEventListener('click', function () {
    document.getElementById('reveal-password').value = '';
    document.getElementById('reveal-error').classList.add('d-none');
    new bootstrap.Modal(document.getElementById('revealKeyModal')).show();
});

document.getElementById('reveal-submit')?.addEventListener('click', function () {
    var password = document.getElementById('reveal-password').value;
    var errEl = document.getElementById('reveal-error');
    errEl.classList.add('d-none');

    fetch(@json(url('api-docs/reveal-key')), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': @json(csrf_token()),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ password: password })
    })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
        .then(function (res) {
            if (!res.ok) {
                errEl.textContent = res.data.message || 'Could not reveal key.';
                errEl.classList.remove('d-none');
                return;
            }
            document.getElementById('api-key-display').textContent = res.data.api_key;
            bootstrap.Modal.getInstance(document.getElementById('revealKeyModal')).hide();
        })
        .catch(function () {
            errEl.textContent = 'Network error. Try again.';
            errEl.classList.remove('d-none');
        });
});
</script>
@endsection
