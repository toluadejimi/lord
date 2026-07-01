@extends('vas.layout', [
    'vasTitle' => 'Electricity',
    'vasSubtitle' => 'Prepaid & postpaid token purchase',
    'vasIcon' => 'ti-bolt',
    'wallet' => $wallet,
    'vasConfigured' => $vasConfigured,
])

@section('vas-body')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <form method="post" action="{{ route('vas.electricity.buy') }}" class="card vas-card" id="vas-form">
            @csrf
            <input type="hidden" name="variation_code" id="variation-code" value="{{ old('variation_code') }}">
            <div class="card-body">
                <div class="mb-4">
                    <div class="vas-field-label">Distribution company (DISCO)</div>
                    <select id="disco-select" name="service_id" class="form-select vas-input" required>
                        <option value="">Select disco</option>
                        @foreach($discos as $disco)
                        <option value="{{ $disco['id'] }}" @selected(old('service_id') === $disco['id'])>{{ $disco['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <div class="vas-field-label">Meter type</div>
                    <select id="meter-type" class="form-select vas-input" required>
                        <option value="prepaid">Prepaid</option>
                        <option value="postpaid">Postpaid</option>
                    </select>
                </div>
                <div class="mb-4">
                    <div class="vas-field-label">Tariff / plan</div>
                    <select id="tariff-select" class="form-select vas-input" required disabled>
                        <option value="">Select DISCO first</option>
                    </select>
                </div>
                <div class="mb-4">
                    <div class="vas-field-label">Meter number</div>
                    <input class="form-control vas-input" name="billersCode" id="billers-code" type="text"
                        value="{{ old('billersCode') }}" placeholder="Enter meter number" required>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="validate-btn">
                        <i class="ti ti-check"></i> Validate meter
                    </button>
                    <div id="validate-result" class="small mt-2"></div>
                </div>
                <div class="mb-4">
                    <div class="vas-field-label">Phone number</div>
                    <input class="form-control vas-input phone-input" name="phone" type="tel" minlength="10" maxlength="11"
                        value="{{ old('phone') }}" placeholder="08012345678" required>
                </div>
                <div class="mb-4">
                    <div class="vas-field-label">Amount</div>
                    <div class="input-group">
                        <span class="input-group-text border-end-0 bg-white">₦</span>
                        <input class="form-control vas-input amount-input border-start-0 ps-0" name="amount" type="number"
                            min="100" max="500000" step="1" value="{{ old('amount') }}" required>
                    </div>
                    <div class="form-text">Minimum ₦100</div>
                </div>
                <button type="submit" class="btn btn-primary vas-submit btn-lg w-100 text-white" @disabled(!$vasConfigured)>
                    Pay from wallet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('vas-scripts')
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
        if (!variationInput.value) variationInput.value = meterType.value;
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
                validateResult.innerHTML = name
                    ? '<span class="text-success"><strong>' + name + '</strong> — verified.</span>'
                    : '<span class="text-danger">' + (data.message || data.msg || 'Validation failed.') + '</span>';
            })
            .catch(function () { validateResult.innerHTML = '<span class="text-danger">Validation request failed.</span>'; })
            .finally(function () { validateBtn.disabled = false; });
    });

    if (discoSelect?.value) loadTariffs();
})();
</script>
@endpush
