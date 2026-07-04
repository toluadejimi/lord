@extends('layout.main')
@section('content')
@include('partials.server-page-styles')
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
    <div class="pc-content p-4 sv-page sv-theme-2">
        @if(session('message'))<div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>@endif

        @include('partials.server-hero', [
            'serverNum' => 2,
            'title' => 'US SMS Verification',
            'subtitle' => 'Rent US numbers for OTP delivery. Price is confirmed before purchase.',
        ])

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card sv-card h-100">
                    <div class="card-body">
                        <div class="sv-step">Step 1 — Service</div>
                        <div class="sv-field sv-field--solo mb-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" id="service-search" placeholder="Search services (whatsapp, google…)…" autocomplete="off">
                            </div>
                        </div>
                        <div id="service-results" class="list-group sv-picker-list mb-3"></div>
                        <div id="service-selected" class="sv-selected-pill d-none mb-3"></div>

                        <div id="options-step" style="display:none;">
                            <div class="sv-step">Step 2 — Options (optional)</div>
                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small text-muted mb-1">Area code</label>
                                    <input type="text" class="form-control" id="area-code" placeholder="e.g. 212">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small text-muted mb-1">Carrier</label>
                                    <input type="text" class="form-control" id="carrier" placeholder="Optional">
                                </div>
                            </div>
                        </div>

                        <div id="price-step" style="display:none;">
                            <div class="sv-step">Step 3 — Price</div>
                            <div id="price-box" class="sv-price-box text-center mb-3">
                                <div class="text-muted">Checking price…</div>
                            </div>
                        </div>

                        <form method="post" action="{{ url('order-usa2') }}" id="order-form" style="display:none;">
                            @csrf
                            <input type="hidden" name="service" id="input-service">
                            <input type="hidden" name="api_cost" id="input-api-cost">
                            <input type="hidden" name="area_code" id="input-area-code">
                            <input type="hidden" name="carrier" id="input-carrier">
                            <button type="submit" class="btn sv-btn-rent w-100" id="buy-btn" disabled>
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
                    'ordersPanelClass' => 'sv-orders-panel',
                ])
            </div>
        </div>
    </div>
</div>

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
        if (typeof item === 'string') return { id: item, name: item, usd: 1 };
        if (Array.isArray(item)) return { id: item[0], name: item[0], usd: parseFloat(item[1]) || 1 };
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
        serviceSelected.textContent = '✓ ' + s.name;
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
        priceBox.innerHTML = '<div class="text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Checking price…</div>';
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
                buyBtn.textContent = 'Rent US number — ₦' + Number(d.ngn).toLocaleString('en-NG', { minimumFractionDigits: 2 });
            })
            .catch(function () {
                priceBox.innerHTML = '<div class="text-danger small">Could not load price.</div>';
            });
    }

    serviceSearch.addEventListener('input', function () { renderServices(serviceSearch.value); });
    serviceSearch.addEventListener('focus', function () { renderServices(serviceSearch.value); });
    areaCode.addEventListener('change', function () { inputAreaCode.value = areaCode.value; refreshPrice(); });
    carrier.addEventListener('change', function () { inputCarrier.value = carrier.value; refreshPrice(); });

    if (normalized.length) {
        renderServices('');
    } else {
        serviceResults.innerHTML = '<div class="list-group-item text-muted small">No services loaded. Type a service ID below.</div>';
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
