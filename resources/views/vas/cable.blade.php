@extends('vas.layout', [
    'vasTitle' => 'Cable TV',
    'vasSubtitle' => 'DSTV, GOtv, StarTimes & more',
    'vasIcon' => 'ti-device-tv',
    'wallet' => $wallet,
    'vasConfigured' => $vasConfigured,
])

@section('vas-body')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <form method="post" action="{{ route('vas.cable.buy') }}" class="card vas-card" id="vas-form">
            @csrf
            <input type="hidden" name="variation_code" id="variation-code" value="{{ old('variation_code') }}">
            <div class="card-body">
                <div class="mb-4">
                    <div class="vas-field-label">TV provider</div>
                    <select id="provider-select" name="service_id" class="form-select vas-input" required>
                        <option value="">Loading providers…</option>
                    </select>
                </div>
                <div class="mb-4">
                    <div class="vas-field-label">Subscription plan</div>
                    <select id="plan-select" class="form-select vas-input" required disabled>
                        <option value="">Select provider first</option>
                    </select>
                </div>
                <div class="mb-4">
                    <div class="vas-field-label">Smartcard / IUC</div>
                    <input class="form-control vas-input" name="billersCode" id="billers-code" type="text"
                        value="{{ old('billersCode') }}" placeholder="Enter decoder number" required>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="validate-btn">
                        <i class="ti ti-check"></i> Validate customer
                    </button>
                    <div id="validate-result" class="small mt-2"></div>
                </div>
                <div class="mb-4">
                    <div class="vas-field-label">Contact phone <span class="text-muted fw-normal">(optional)</span></div>
                    <input class="form-control vas-input phone-input" name="phone" type="tel" maxlength="11" value="{{ old('phone') }}">
                </div>
                <div class="mb-4">
                    <div class="vas-field-label">Amount</div>
                    <div class="input-group">
                        <span class="input-group-text border-end-0 bg-white">₦</span>
                        <input class="form-control vas-input amount-input border-start-0 ps-0" name="amount" id="amount-input"
                            type="number" min="1" step="1" value="{{ old('amount') }}" required>
                    </div>
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
    const providerSelect = document.getElementById('provider-select');
    const planSelect = document.getElementById('plan-select');
    const variationInput = document.getElementById('variation-code');
    const amountInput = document.getElementById('amount-input');
    const validateBtn = document.getElementById('validate-btn');
    const validateResult = document.getElementById('validate-result');
    const billersInput = document.getElementById('billers-code');
    const catalogUrl = @json(route('vas.catalog.cable'));
    const validateUrl = @json(route('vas.cable.validate'));
    const csrf = @json(csrf_token());
    let providers = [];

    function parseCableCatalog(raw) {
        const data = raw.data || raw;
        const list = data.providers || data.content || data.variations || data;
        if (!Array.isArray(list)) {
            return Object.keys(list).map(function (key) {
                const item = list[key];
                return typeof item === 'object'
                    ? { id: item.service_id || item.id || key, name: item.name || key, plans: item.variations || item.plans || [] }
                    : { id: key, name: String(item), plans: [] };
            });
        }
        return list.map(function (item) {
            return {
                id: item.service_id || item.id || item.code || '',
                name: item.name || item.title || 'Provider',
                plans: item.variations || item.plans || item.content || [],
            };
        }).filter(function (p) { return p.id; });
    }

    function fillProviders() {
        providerSelect.innerHTML = '<option value="">Select provider</option>';
        providers.forEach(function (p) {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.name;
            providerSelect.appendChild(opt);
        });
    }

    function fillPlans(providerId) {
        const provider = providers.find(function (p) { return p.id === providerId; });
        planSelect.innerHTML = '<option value="">Select plan</option>';
        planSelect.disabled = !provider;
        variationInput.value = '';
        if (!provider) return;
        (provider.plans || []).forEach(function (plan) {
            const code = plan.variation_code || plan.code || '';
            const name = plan.name || plan.variation_name || code;
            const amount = plan.variation_amount || plan.amount || plan.price || '';
            const opt = document.createElement('option');
            opt.value = code;
            opt.textContent = name + (amount ? ' — ₦' + Number(amount).toLocaleString() : '');
            opt.dataset.amount = amount;
            planSelect.appendChild(opt);
        });
    }

    fetch(catalogUrl)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            providers = parseCableCatalog(data);
            fillProviders();
        })
        .catch(function () {
            providerSelect.innerHTML = '<option value="">Could not load providers</option>';
        });

    providerSelect?.addEventListener('change', function () { fillPlans(providerSelect.value); });
    planSelect?.addEventListener('change', function () {
        const opt = planSelect.selectedOptions[0];
        variationInput.value = opt ? opt.value : '';
        if (opt && opt.dataset.amount) amountInput.value = opt.dataset.amount;
    });

    validateBtn?.addEventListener('click', function () {
        if (!providerSelect.value || !billersInput.value.trim()) {
            validateResult.innerHTML = '<span class="text-danger">Select provider and enter smartcard number.</span>';
            return;
        }
        validateBtn.disabled = true;
        validateResult.innerHTML = '<span class="text-muted">Validating…</span>';
        const body = new FormData();
        body.append('_token', csrf);
        body.append('service_id', providerSelect.value);
        body.append('billersCode', billersInput.value.trim());
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
})();
</script>
@endpush
