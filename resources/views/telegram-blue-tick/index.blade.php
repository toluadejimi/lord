@extends('telegram-blue-tick.layout')

@section('tbt-body')
@if($loadError)
<div class="card tbt-card">
    <div class="card-body text-center py-5">
        <i class="fab fa-telegram fs-1 text-muted mb-3 d-block"></i>
        <p class="text-muted mb-0">{{ $loadError }}</p>
    </div>
</div>
@elseif(empty($packages))
<div class="card tbt-card">
    <div class="card-body text-center py-5">
        <p class="text-muted mb-0">No packages available. Please try again later.</p>
    </div>
</div>
@else
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card tbt-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">1. Choose duration</h5>
                <div class="row g-3" id="package-grid">
                    @foreach($packages as $pkg)
                    <div class="col-md-4">
                        <div class="tbt-package" data-months="{{ $pkg['months'] }}" data-price="{{ $pkg['price_ngn'] }}">
                            <div class="months mb-1">{{ $pkg['label'] }}</div>
                            <div class="price">₦{{ number_format($pkg['price_ngn'], 2) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card tbt-card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">2. Enter Telegram username</h5>
                <p class="small text-muted">Username without @ — we verify the account can receive Premium before charging your wallet.</p>

                <div class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Telegram username</label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text" class="form-control" id="tbt-username" placeholder="johndoe" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn tbt-submit w-100" id="tbt-lookup" disabled>Verify recipient</button>
                    </div>
                </div>

                <div id="tbt-recipient-box" class="tbt-recipient mt-3 d-none">
                    <div class="d-flex align-items-center gap-3">
                        <img src="" alt="" id="tbt-recipient-photo" class="d-none">
                        <div>
                            <div class="fw-bold" id="tbt-recipient-name"></div>
                            <div class="small text-muted">@<span id="tbt-recipient-username"></span></div>
                        </div>
                    </div>
                </div>

                <div class="text-danger small mt-2 d-none" id="tbt-error"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card tbt-card sticky-top" style="top: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Order summary</h5>
                <dl class="row small mb-4">
                    <dt class="col-5 text-muted">Package</dt>
                    <dd class="col-7 fw-semibold mb-2" id="sum-months">—</dd>
                    <dt class="col-5 text-muted">Recipient</dt>
                    <dd class="col-7 fw-semibold mb-2" id="sum-username">—</dd>
                    <dt class="col-5 text-muted">Total</dt>
                    <dd class="col-7 fw-bold fs-5 text-primary mb-0" id="sum-price">—</dd>
                </dl>

                <form method="post" action="{{ route('telegram-blue-tick.purchase') }}" id="tbt-purchase-form">
                    @csrf
                    <input type="hidden" name="username" id="form-username">
                    <input type="hidden" name="recipient_hash" id="form-recipient-hash">
                    <input type="hidden" name="recipient_name" id="form-recipient-name">
                    <input type="hidden" name="months" id="form-months">
                    <input type="hidden" name="price_ngn" id="form-price">
                    <button type="submit" class="btn tbt-submit w-100" id="tbt-buy" disabled>
                        Pay from wallet
                    </button>
                </form>

                <p class="small text-muted mt-3 mb-0">
                    Delivery is usually within a few minutes. You will see status updates under <strong>My orders</strong>.
                </p>
            </div>
        </div>
    </div>
</div>
@endif

@push('page-scripts')
<script>
(function () {
    var selectedMonths = null;
    var selectedPrice = null;
    var recipientHash = null;
    var recipientName = null;
    var csrf = @json(csrf_token());
    var lookupUrl = @json(route('telegram-blue-tick.search'));

    function setError(msg) {
        var el = document.getElementById('tbt-error');
        if (!msg) { el.classList.add('d-none'); el.textContent = ''; return; }
        el.textContent = msg;
        el.classList.remove('d-none');
    }

    function resetRecipient() {
        recipientHash = null;
        recipientName = null;
        document.getElementById('tbt-recipient-box').classList.add('d-none');
        document.getElementById('tbt-buy').disabled = true;
        document.getElementById('form-recipient-hash').value = '';
        document.getElementById('form-recipient-name').value = '';
        document.getElementById('sum-username').textContent = '—';
    }

    document.querySelectorAll('.tbt-package').forEach(function (el) {
        el.addEventListener('click', function () {
            document.querySelectorAll('.tbt-package').forEach(function (p) { p.classList.remove('selected'); });
            el.classList.add('selected');
            selectedMonths = parseInt(el.getAttribute('data-months'), 10);
            selectedPrice = parseFloat(el.getAttribute('data-price'));
            document.getElementById('sum-months').textContent = selectedMonths + ' months';
            document.getElementById('sum-price').textContent = '₦' + selectedPrice.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('form-months').value = selectedMonths;
            document.getElementById('form-price').value = selectedPrice;
            document.getElementById('tbt-lookup').disabled = false;
            resetRecipient();
        });
    });

    function firstError(data) {
        if (data && data.message) return data.message;
        if (data && data.errors) {
            var k = Object.keys(data.errors)[0];
            if (k && data.errors[k] && data.errors[k][0]) return data.errors[k][0];
        }
        return 'Request failed. Please try again.';
    }

    document.getElementById('tbt-lookup')?.addEventListener('click', function () {
        var username = document.getElementById('tbt-username').value.trim().replace(/^@/, '');
        if (!username || !selectedMonths) return;
        setError('');
        this.disabled = true;
        this.textContent = 'Checking…';

        var body = new FormData();
        body.append('username', username);
        body.append('months', String(selectedMonths));
        body.append('_token', csrf);

        fetch(lookupUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: body
        })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (res) {
                document.getElementById('tbt-lookup').disabled = false;
                document.getElementById('tbt-lookup').textContent = 'Verify recipient';
                if (!res.ok || !res.data.success) {
                    resetRecipient();
                    setError(firstError(res.data));
                    return;
                }
                var rec = res.data.recipient || {};
                recipientHash = rec.recipient_hash || rec.recipient || '';
                recipientName = rec.name || username;
                if (!recipientHash) {
                    resetRecipient();
                    setError('Recipient could not be verified. Try another username.');
                    return;
                }
                document.getElementById('tbt-recipient-box').classList.remove('d-none');
                document.getElementById('tbt-recipient-name').textContent = recipientName;
                document.getElementById('tbt-recipient-username').textContent = username;
                document.getElementById('sum-username').textContent = '@' + username;
                document.getElementById('form-username').value = username;
                document.getElementById('form-recipient-hash').value = recipientHash;
                document.getElementById('form-recipient-name').value = recipientName;
                var photo = document.getElementById('tbt-recipient-photo');
                if (rec.photo) {
                    photo.src = rec.photo;
                    photo.classList.remove('d-none');
                } else {
                    photo.classList.add('d-none');
                }
                document.getElementById('tbt-buy').disabled = !recipientHash;
            })
            .catch(function () {
                document.getElementById('tbt-lookup').disabled = false;
                document.getElementById('tbt-lookup').textContent = 'Verify recipient';
                setError('Network error. Try again.');
            });
    });

    document.getElementById('tbt-purchase-form')?.addEventListener('submit', function () {
        document.getElementById('tbt-buy').disabled = true;
        document.getElementById('tbt-buy').textContent = 'Processing…';
    });
})();
</script>
@endpush
@endsection
