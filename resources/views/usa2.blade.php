@extends('layout.main')
@section('content')
@php
    $serviceList = [];
    if (is_object($services)) {
        $raw = $services->services ?? $services->data ?? $services;
        if (is_array($raw)) {
            $serviceList = $raw;
        } elseif (is_object($raw)) {
            $serviceList = (array) $raw;
        }
    }
@endphp
<div class="pc-container">
    <div class="pc-content p-4">
        @if(session('message'))<div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>@endif

        <div class="usa2-hero d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <span class="usa2-pill mb-2"><i class="ti ti-flag"></i> Server 2</span>
                <h2 class="h4 mb-1 text-white">US SMS Verification</h2>
                <p class="mb-0 small text-white opacity-90">Rent US numbers for OTP delivery. Price is confirmed before purchase.</p>
            </div>
            <div class="text-end text-white">
                <div class="small opacity-75">Wallet</div>
                <div class="h4 mb-0 fw-bold">₦{{ number_format((float) Auth::user()->wallet, 2) }}</div>
                <a href="{{ url('fund-wallet') }}" class="small text-white text-decoration-underline opacity-90">Fund wallet</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="step-label">Step 1 — Service</div>
                        <input type="text" class="form-control mb-2" id="service-search" placeholder="Search services (e.g. whatsapp, google)…" autocomplete="off">
                        <div id="service-results" class="list-group usa2-picker-list mb-3"></div>
                        <div id="service-selected" class="selected-pill d-none mb-3"></div>

                        <div id="options-step" style="display:none;">
                            <div class="step-label">Step 2 — Options (optional)</div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted mb-1">Area code</label>
                                    <input type="text" class="form-control" id="area-code" placeholder="e.g. 212">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted mb-1">Carrier</label>
                                    <input type="text" class="form-control" id="carrier" placeholder="Optional">
                                </div>
                            </div>
                        </div>

                        <div id="price-step" style="display:none;">
                            <div class="step-label">Step 3 — Price</div>
                            <div id="price-box" class="price-box text-center py-4 mb-3">
                                <div class="text-muted">Checking price…</div>
                            </div>
                        </div>

                        <form method="post" action="{{ url('order-usa2') }}" id="order-form" style="display:none;">
                            @csrf
                            <input type="hidden" name="service" id="input-service">
                            <input type="hidden" name="api_cost" id="input-api-cost">
                            <input type="hidden" name="area_code" id="input-area-code">
                            <input type="hidden" name="carrier" id="input-carrier">
                            <button type="submit" class="btn btn-primary btn-lg w-100" id="buy-btn" disabled>
                                Rent US number
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                @include('partials.verification-list', [
                    'verifications' => $verifications,
                    'panelTitle' => 'Recent orders',
                    'panelId' => 'usa2-orders-panel',
                ])
            </div>
        </div>
    </div>
</div>

<style>
.usa2-hero {
    background: linear-gradient(135deg, #ea580c 0%, #f97316 50%, #fb923c 100%);
    border-radius: 16px; padding: 1.25rem 1.5rem;
    box-shadow: 0 10px 30px rgba(234, 88, 12, .25);
}
.usa2-pill {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
    border-radius: 999px; padding: .3rem .75rem; font-size: .8rem; font-weight: 600; color: #fff;
}
.step-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #ea580c; margin-bottom: .5rem; }
.usa2-picker-list { max-height: 260px; overflow-y: auto; border-radius: 8px; }
.usa2-picker-list .list-group-item { cursor: pointer; }
.usa2-picker-list .list-group-item:hover { background: #fff7ed; }
.selected-pill { display: inline-block; padding: .35rem .75rem; background: #ffedd5; border-radius: 999px; font-size: .875rem; }
.price-box { background: #fff7ed; border-radius: 12px; border: 1px dashed #fdba74; }
.price-box .amount { font-size: 1.75rem; font-weight: 700; color: #ea580c; }
</style>

<script>
(function () {
    const services = @json(array_values($serviceList));
    const priceUrl = @json(url('usa2/catalog/price'));
    let selected = null;

    const serviceSearch = document.getElementById('service-search');
    const serviceResults = document.getElementById('service-results');
    const serviceSelected = document.getElementById('service-selected');
    const optionsStep = document.getElementById('options-step');
    const priceStep = document.getElementById('price-step');
    const priceBox = document.getElementById('price-box');
    const orderForm = document.getElementById('order-form');
    const buyBtn = document.getElementById('buy-btn');
    const inputService = document.getElementById('input-service');
    const inputApiCost = document.getElementById('input-api-cost');
    const inputAreaCode = document.getElementById('input-area-code');
    const inputCarrier = document.getElementById('input-carrier');
    const areaCode = document.getElementById('area-code');
    const carrier = document.getElementById('carrier');

    function normalizeService(item) {
        if (typeof item === 'string') {
            return { id: item, name: item, usd: 1 };
        }
        if (Array.isArray(item)) {
            return { id: item[0], name: item[0], usd: parseFloat(item[1]) || 1 };
        }
        const id = item.id ?? item.service ?? item.code ?? item.name ?? '';
        const name = item.name ?? item.service ?? item.label ?? id;
        const usd = parseFloat(item.price ?? item.cost ?? item.usd ?? 1) || 1;
        return { id: String(id), name: String(name), usd: usd };
    }

    const normalized = services.map(normalizeService).filter(function (s) { return s.id; });

    function renderServices(filter) {
        const q = (filter || '').toLowerCase();
        serviceResults.innerHTML = '';
        normalized
            .filter(function (s) { return !q || s.name.toLowerCase().includes(q) || s.id.toLowerCase().includes(q); })
            .slice(0, 40)
            .forEach(function (s) {
                const el = document.createElement('button');
                el.type = 'button';
                el.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                el.innerHTML = '<span>' + s.name + '</span><span class="text-muted small">~$' + s.usd.toFixed(2) + '</span>';
                el.addEventListener('click', function () { selectService(s); });
                serviceResults.appendChild(el);
            });
    }

    function selectService(s) {
        selected = s;
        serviceSearch.value = s.name;
        serviceResults.innerHTML = '';
        serviceSelected.textContent = 'Selected: ' + s.name;
        serviceSelected.classList.remove('d-none');
        optionsStep.style.display = 'block';
        priceStep.style.display = 'block';
        orderForm.style.display = 'block';
        inputService.value = s.id;
        inputApiCost.value = s.usd;
        refreshPrice();
    }

    function refreshPrice() {
        if (!selected) return;
        priceBox.innerHTML = '<div class="text-muted">Checking price…</div>';
        buyBtn.disabled = true;

        const params = new URLSearchParams({
            usd: String(selected.usd),
            surcharge: (areaCode.value || carrier.value) ? '1' : '0'
        });

        fetch(priceUrl + '?' + params.toString())
            .then(function (r) { return r.json(); })
            .then(function (d) {
                priceBox.innerHTML = '<div class="amount">₦' + Number(d.ngn).toLocaleString('en-NG', { minimumFractionDigits: 2 }) + '</div>' +
                    '<div class="text-muted small mt-1">Provider ~$' + Number(d.usd).toFixed(2) + ' USD</div>';
                buyBtn.disabled = false;
            })
            .catch(function () {
                priceBox.innerHTML = '<div class="text-danger small">Could not load price.</div>';
            });
    }

    serviceSearch.addEventListener('input', function () { renderServices(serviceSearch.value); });
    serviceSearch.addEventListener('focus', function () { renderServices(serviceSearch.value); });
    areaCode.addEventListener('change', function () {
        inputAreaCode.value = areaCode.value;
        refreshPrice();
    });
    carrier.addEventListener('change', function () {
        inputCarrier.value = carrier.value;
        refreshPrice();
    });

    if (normalized.length) {
        renderServices('');
    } else {
        serviceResults.innerHTML = '<div class="list-group-item text-muted small">No services loaded. Enter a service ID manually below.</div>';
        orderForm.style.display = 'block';
        buyBtn.disabled = false;
        inputService.value = '';
        inputApiCost.value = '1';
        serviceSearch.placeholder = 'Service ID (e.g. whatsapp)';
        serviceSearch.addEventListener('change', function () {
            const id = serviceSearch.value.trim();
            if (!id) return;
            selectService({ id: id, name: id, usd: 1 });
        });
    }
})();
</script>
@endsection
