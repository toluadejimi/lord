@extends('vas.layout', [
    'vasTitle' => 'Data Bundles',
    'vasSubtitle' => 'Pick a network, choose a plan, enter phone number',
    'vasIcon' => 'ti-wifi',
    'wallet' => $wallet,
    'vasConfigured' => $vasConfigured,
])

@section('vas-body')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <form method="post" action="{{ route('vas.data.buy') }}" class="card vas-card" id="vas-form">
            @csrf
            <input type="hidden" name="variation_code" id="variation-code" value="{{ old('variation_code') }}">
            <div class="card-body">
                @include('vas.partials.network-picker', ['networks' => $networks])

                <div class="mb-3">
                    <div class="vas-field-label">Search bundles</div>
                    <input type="text" class="form-control vas-input" id="bundle-search" placeholder="Search plan name…">
                    <div class="amount-chips mt-2">
                        <button type="button" class="amount-chip filter-btn" data-filter="daily">Daily</button>
                        <button type="button" class="amount-chip filter-btn" data-filter="weekly">Weekly</button>
                        <button type="button" class="amount-chip filter-btn" data-filter="monthly">Monthly</button>
                        <button type="button" class="amount-chip filter-btn" data-filter="yearly">Yearly</button>
                    </div>
                </div>

                <div id="bundle-grid" class="row g-2 mb-4">
                    <div class="col-12 text-muted small py-3 text-center">Select a network to load bundles.</div>
                </div>

                <div class="mb-4">
                    <div class="vas-field-label">Phone number</div>
                    <input class="form-control vas-input phone-input" name="phone" type="tel" maxlength="11"
                        placeholder="08012345678" value="{{ old('phone') }}" required>
                </div>

                <div class="mb-4">
                    <div class="vas-field-label">Amount</div>
                    <div class="input-group">
                        <span class="input-group-text border-end-0 bg-white">₦</span>
                        <input class="form-control vas-input amount-input border-start-0 ps-0" name="amount" id="amount-input"
                            type="number" min="1" max="500000" step="1" value="{{ old('amount') }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary vas-submit btn-lg w-100 text-white" id="submit-btn" disabled>
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
    const networkInput = document.getElementById('network-input');
    const bundleGrid = document.getElementById('bundle-grid');
    const variationInput = document.getElementById('variation-code');
    const amountInput = document.getElementById('amount-input');
    const submitBtn = document.getElementById('submit-btn');
    const bundleSearch = document.getElementById('bundle-search');
    const catalogUrl = @json(route('vas.catalog.data'));
    let bundles = [];
    let activeFilter = '';

    function parseBundles(raw) {
        const list = raw.data?.variations || raw.data?.content || raw.variations || raw.content || raw.data || raw;
        const arr = Array.isArray(list) ? list : (Array.isArray(list?.data) ? list.data : []);
        return arr.filter(function (item) { return item && typeof item === 'object'; }).map(function (item) {
            return {
                code: item.variation_code || item.code || '',
                name: item.name || item.variation_name || 'Bundle',
                amount: item.variation_amount || item.amount || item.price || '',
            };
        }).filter(function (b) { return b.code; });
    }

    function renderBundles() {
        const q = (bundleSearch?.value || '').toLowerCase();
        const filtered = bundles.filter(function (b) {
            const name = b.name.toLowerCase();
            if (q && !name.includes(q) && !b.code.toLowerCase().includes(q)) return false;
            if (!activeFilter) return true;
            return name.includes(activeFilter);
        });

        bundleGrid.innerHTML = '';
        if (!filtered.length) {
            bundleGrid.innerHTML = '<div class="col-12 text-muted small py-3 text-center">No bundles found.</div>';
            return;
        }

        filtered.forEach(function (b) {
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4';
            const card = document.createElement('div');
            card.className = 'bundle-card' + (variationInput.value === b.code ? ' selected' : '');
            card.innerHTML = '<div class="fw-semibold small">' + b.name + '</div>' +
                (b.amount ? '<div class="text-primary fw-bold mt-1">₦' + Number(b.amount).toLocaleString() + '</div>' : '');
            card.addEventListener('click', function () {
                variationInput.value = b.code;
                if (b.amount) amountInput.value = b.amount;
                document.querySelectorAll('.bundle-card').forEach(function (el) { el.classList.remove('selected'); });
                card.classList.add('selected');
                submitBtn.disabled = false;
            });
            col.appendChild(card);
            bundleGrid.appendChild(col);
        });
    }

    function loadBundles(network) {
        bundleGrid.innerHTML = '<div class="col-12 text-muted small py-4 text-center">Loading bundles…</div>';
        variationInput.value = '';
        submitBtn.disabled = true;
        fetch(catalogUrl + '?network=' + encodeURIComponent(network))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                bundles = parseBundles(data);
                renderBundles();
            })
            .catch(function () {
                bundleGrid.innerHTML = '<div class="col-12 text-danger small py-3 text-center">Could not load bundles.</div>';
            });
    }

    networkInput?.addEventListener('change', function () {
        if (networkInput.value) loadBundles(networkInput.value);
    });

    bundleSearch?.addEventListener('input', renderBundles);

    document.querySelectorAll('.filter-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activeFilter = btn.dataset.filter || '';
            document.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            renderBundles();
        });
    });

    if (networkInput?.value) loadBundles(networkInput.value);
})();
</script>
@endpush
