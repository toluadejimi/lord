@extends('layout.main')
@section('content')
<div class="pc-container">
    <div class="pc-content p-4">
        @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="mb-3">
            <a href="{{ route('vas.index') }}" class="text-muted small"><i class="ti ti-arrow-left"></i> Bills &amp; VTU</a>
        </div>

        <h2 class="mb-1">Electricity</h2>
        <p class="text-muted small mb-3">Pay prepaid or postpaid electricity bills and receive your token.</p>

        @include('vas.partials.subnav')

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <form method="post" action="{{ route('vas.electricity.buy') }}" class="card border-0 shadow-sm" id="vas-form">
                    @csrf
                    <input type="hidden" name="variation_code" id="variation-code" value="{{ old('variation_code') }}">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">DISCO</label>
                            <select id="disco-select" name="service_id" class="form-select" required>
                                <option value="">Select disco</option>
                                @foreach($discos as $disco)
                                <option value="{{ $disco['id'] }}" @selected(old('service_id') === $disco['id'])>{{ $disco['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meter type</label>
                            <select id="meter-type" class="form-select" required>
                                <option value="prepaid">Prepaid</option>
                                <option value="postpaid">Postpaid</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tariff / plan</label>
                            <select id="tariff-select" class="form-select" required disabled>
                                <option value="">Select DISCO first</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meter number</label>
                            <input class="form-control" name="billersCode" id="billers-code" type="text"
                                value="{{ old('billersCode') }}" required>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="validate-btn">Validate meter</button>
                            <div id="validate-result" class="small mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone number</label>
                            <input class="form-control phone-input" name="phone" type="tel" minlength="10" maxlength="11"
                                value="{{ old('phone') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Amount (₦)</label>
                            <input class="form-control form-control-lg amount-input" name="amount" type="number"
                                min="100" max="500000" step="1" value="{{ old('amount') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100" @disabled(!$vasConfigured)>Pay from wallet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('vas.partials.form-js')
<script>
(function () {
    const discoSelect = document.getElementById('disco-select');
    const meterType = document.getElementById('meter-type');
    const tariffSelect = document.getElementById('tariff-select');
    const variationInput = document.getElementById('variation-code');
    const validateBtn = document.getElementById('validate-btn');
    const validateResult = document.getElementById('validate-result');
    const billersInput = document.getElementById('billers-code');
    const catalogUrl = @json(route('vas.catalog.electricity'));
    const validateUrl = @json(route('vas.electricity.validate'));
    const csrf = @json(csrf_token());

    function parseVariations(raw) {
        const list = raw.data?.variations || raw.data?.content || raw.variations || raw.content || raw.data || raw;
        const arr = Array.isArray(list) ? list : (Array.isArray(list?.data) ? list.data : []);
        return arr.filter(function (item) { return item && typeof item === 'object'; });
    }

    function loadTariffs() {
        if (!discoSelect.value) return;
        tariffSelect.disabled = true;
        tariffSelect.innerHTML = '<option value="">Loading…</option>';
        fetch(catalogUrl + '?serviceID=' + encodeURIComponent(discoSelect.value))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                const items = parseVariations(data);
                tariffSelect.innerHTML = '<option value="">Select tariff</option>';
                items.forEach(function (item) {
                    const code = item.variation_code || item.code || item.name || '';
                    const name = item.name || item.variation_name || code;
                    const opt = document.createElement('option');
                    opt.value = code;
                    opt.textContent = name;
                    tariffSelect.appendChild(opt);
                });
                tariffSelect.disabled = false;
            })
            .catch(function () {
                tariffSelect.innerHTML = '<option value="">Could not load tariffs</option>';
            });
    }

    discoSelect?.addEventListener('change', loadTariffs);
    tariffSelect?.addEventListener('change', function () {
        variationInput.value = tariffSelect.value || meterType.value;
    });
    meterType?.addEventListener('change', function () {
        if (!tariffSelect.value) variationInput.value = meterType.value;
    });

    if (!variationInput.value) variationInput.value = meterType.value;

    validateBtn?.addEventListener('click', function () {
        if (!discoSelect.value || !billersInput.value.trim()) {
            validateResult.innerHTML = '<span class="text-danger">Select DISCO and enter meter number.</span>';
            return;
        }
        validateBtn.disabled = true;
        validateResult.innerHTML = '<span class="text-muted">Validating…</span>';
        const body = new FormData();
        body.append('_token', csrf);
        body.append('service_id', discoSelect.value);
        body.append('billersCode', billersInput.value.trim());
        body.append('type', meterType.value);
        fetch(validateUrl, { method: 'POST', body: body })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                const name = data.customer_name || data.name || data.data?.customer_name || data.data?.name;
                if (name) {
                    validateResult.innerHTML = '<span class="text-success"><strong>' + name + '</strong> — verified.</span>';
                } else {
                    validateResult.innerHTML = '<span class="text-danger">' + (data.message || data.msg || 'Validation failed.') + '</span>';
                }
            })
            .catch(function () {
                validateResult.innerHTML = '<span class="text-danger">Validation request failed.</span>';
            })
            .finally(function () { validateBtn.disabled = false; });
    });

    if (discoSelect?.value) loadTariffs();
})();
</script>
@endsection
