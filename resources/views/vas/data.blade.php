@extends('layout.main')
@section('content')
<div class="pc-container">
    <div class="pc-content p-4">
        @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="mb-3">
            <a href="{{ route('vas.index') }}" class="text-muted small"><i class="ti ti-arrow-left"></i> Bills &amp; VTU</a>
        </div>

        <h2 class="mb-1">Data Bundles</h2>
        <p class="text-muted small mb-3">Choose network, pick a bundle, then enter the phone number.</p>

        @include('vas.partials.subnav')

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="post" action="{{ route('vas.data.buy') }}" class="card border-0 shadow-sm" id="vas-form">
                    @csrf
                    <input type="hidden" name="variation_code" id="variation-code" value="{{ old('variation_code') }}">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Network</label>
                            <select id="network-select" name="service_id" class="form-select" required>
                                <option value="">Select network</option>
                                @foreach($networks as $network)
                                <option value="{{ $network['id'] }}" @selected(old('service_id') === $network['id'])>{{ $network['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Search bundles</label>
                            <input type="text" class="form-control" id="bundle-search" placeholder="Search plan name…">
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="daily">Daily</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="weekly">Weekly</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="monthly">Monthly</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="yearly">Yearly</button>
                            </div>
                        </div>

                        <div id="bundle-grid" class="row g-2 mb-3">
                            <div class="col-12 text-muted small">Select a network to load bundles.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone number</label>
                            <input class="form-control phone-input" name="phone" type="tel" maxlength="11"
                                pattern="[0-9]{11}" placeholder="08012345678" value="{{ old('phone') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Amount (₦)</label>
                            <input class="form-control form-control-lg amount-input" name="amount" id="amount-input"
                                type="number" min="1" max="500000" step="1" value="{{ old('amount') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100" id="submit-btn" disabled>
                            Pay from wallet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('vas.partials.form-js')
<script>
(function () {
    const networkSelect = document.getElementById('network-select');
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
            bundleGrid.innerHTML = '<div class="col-12 text-muted small">No bundles found.</div>';
            return;
        }

        filtered.forEach(function (b) {
            const col = document.createElement('div');
            col.className = 'col-md-6';
            const card = document.createElement('div');
            card.className = 'bundle-card' + (variationInput.value === b.code ? ' selected' : '');
            card.innerHTML = '<div class="fw-semibold">' + b.name + '</div>' +
                (b.amount ? '<div class="small text-muted">₦' + Number(b.amount).toLocaleString() + '</div>' : '');
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
        bundleGrid.innerHTML = '<div class="col-12 text-muted small">Loading bundles…</div>';
        variationInput.value = '';
        submitBtn.disabled = true;
        fetch(catalogUrl + '?network=' + encodeURIComponent(network))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                bundles = parseBundles(data);
                renderBundles();
            })
            .catch(function () {
                bundleGrid.innerHTML = '<div class="col-12 text-danger small">Could not load bundles.</div>';
            });
    }

    networkSelect?.addEventListener('change', function () {
        if (networkSelect.value) loadBundles(networkSelect.value);
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

    if (networkSelect?.value) loadBundles(networkSelect.value);
})();
</script>
@endsection
