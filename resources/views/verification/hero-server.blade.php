@extends('layout.main')
@section('content')
@include('partials.server-page-styles')
@php
    $serviceFirst = ($pickerFlow ?? 'country-first') === 'service-first';
@endphp
<div class="pc-container">
    <div class="pc-content p-4 sv-page sv-theme-{{ $serverTheme ?? $serverNum ?? 3 }}">
        @if(session('message'))<div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>@endif

        @include('partials.server-hero', [
            'serverNum' => $serverNum ?? 3,
            'title' => $serverTitle ?? 'International SMS Verification',
            'subtitle' => $serverSubtitle ?? 'Search a country, pick a service, and rent a number.',
            'wallet' => $wallet,
        ])

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card sv-card h-100">
                    <div class="card-body">
                        @if($serviceFirst)
                        <div class="mb-4" id="service-step">
                            <div class="sv-step">Step 1 — Service</div>
                            <div class="sv-field sv-field--solo mb-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-apps"></i></span>
                                    <input type="text" class="form-control" id="service-search" placeholder="Search services…" autocomplete="off">
                                </div>
                            </div>
                            <div id="service-results" class="list-group sv-picker-list mb-2"></div>
                            <div id="service-selected" class="sv-selected-pill d-none"></div>
                            <div id="service-error" class="text-danger small mt-2 d-none"></div>
                        </div>

                        <div class="mb-4" id="country-step" style="display:none;">
                            <div class="sv-step">Step 2 — Country</div>
                            <div class="sv-field sv-field--solo mb-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-world"></i></span>
                                    <input type="text" class="form-control" id="country-search" placeholder="Search countries…" autocomplete="off">
                                </div>
                            </div>
                            <div id="country-results" class="list-group sv-picker-list mb-2"></div>
                            <div id="country-selected" class="sv-selected-pill d-none"></div>
                            <div id="country-loading" class="text-muted small mt-2 d-none">Loading countries…</div>
                        </div>
                        @else
                        <div class="mb-4" id="country-step">
                            <div class="sv-step">Step 1 — Country</div>
                            <div class="sv-field sv-field--solo mb-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-world"></i></span>
                                    <input type="text" class="form-control" id="country-search" placeholder="Search countries…" autocomplete="off">
                                </div>
                            </div>
                            <div id="country-results" class="list-group sv-picker-list mb-2"></div>
                            <div id="country-selected" class="sv-selected-pill d-none"></div>
                        </div>

                        <div class="mb-4" id="service-step" style="display:none;">
                            <div class="sv-step">Step 2 — Service</div>
                            <div class="sv-field sv-field--solo mb-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-apps"></i></span>
                                    <input type="text" class="form-control" id="service-search" placeholder="Search services…" autocomplete="off">
                                </div>
                            </div>
                            <div id="service-results" class="list-group sv-picker-list mb-2"></div>
                            <div id="service-selected" class="sv-selected-pill d-none"></div>
                            <div id="service-error" class="text-danger small mt-2 d-none"></div>
                        </div>
                        @endif

                        <div class="mb-4" id="price-step" style="display:none;">
                            <div class="sv-step">Step 3 — Price</div>
                            <div id="price-box" class="sv-price-box text-center">
                                <div class="text-muted">Checking availability…</div>
                            </div>
                        </div>

                        <form method="post" action="{{ $orderUrl }}" id="order-form" style="display:none;">
                            @csrf
                            <input type="hidden" name="country" id="input-country">
                            <input type="hidden" name="service" id="input-service">
                            <button type="submit" class="btn sv-btn-rent w-100" id="buy-btn" disabled>
                                Rent number
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                @include('partials.verification-list', [
                    'verifications' => $verifications,
                    'panelTitle' => 'Recent orders',
                    'panelId' => 'hero-orders-panel',
                    'ordersPanelClass' => 'sv-orders-panel',
                ])
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const pickerFlow = @json($pickerFlow ?? 'country-first');
    const serviceFirst = pickerFlow === 'service-first';
    const countriesUrl = @json($countriesUrl);
    const servicesUrl = @json($servicesUrl);
    const priceUrl = @json($priceUrl);
    let countries = [];
    let services = [];
    let selectedCountry = null;
    let selectedService = null;

    const countrySearch = document.getElementById('country-search');
    const countryResults = document.getElementById('country-results');
    const countrySelected = document.getElementById('country-selected');
    const countryStep = document.getElementById('country-step');
    const countryLoading = document.getElementById('country-loading');
    const serviceStep = document.getElementById('service-step');
    const serviceSearch = document.getElementById('service-search');
    const serviceResults = document.getElementById('service-results');
    const serviceSelected = document.getElementById('service-selected');
    const serviceError = document.getElementById('service-error');
    const priceStep = document.getElementById('price-step');
    const priceBox = document.getElementById('price-box');
    const orderForm = document.getElementById('order-form');
    const buyBtn = document.getElementById('buy-btn');
    const inputCountry = document.getElementById('input-country');
    const inputService = document.getElementById('input-service');

    function renderList(container, items, onPick, emptyMessage) {
        container.innerHTML = '';
        if (!items.length) {
            container.innerHTML = '<div class="list-group-item text-muted small">' + (emptyMessage || 'No matches') + '</div>';
            return;
        }
        items.slice(0, 80).forEach(function (item) {
            const el = document.createElement('button');
            el.type = 'button';
            el.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            const suffix = item.available ? '<span class="badge bg-light text-dark">' + item.available + '</span>' : '';
            el.innerHTML = '<span>' + item.name + '</span>' + suffix;
            el.addEventListener('click', function () { onPick(item); });
            container.appendChild(el);
        });
    }

    function filterItems(list, query) {
        const q = query.trim().toLowerCase();
        if (!q) return list;
        return list.filter(function (item) {
            return item.name.toLowerCase().includes(q)
                || String(item.id || item.code || '').toLowerCase().includes(q);
        });
    }

    function resetPriceStep() {
        priceStep.style.display = 'none';
        orderForm.style.display = 'none';
        buyBtn.disabled = true;
    }

    function selectService(item) {
        selectedService = item;
        serviceSelected.classList.remove('d-none');
        serviceSelected.textContent = '✓ ' + item.name;
        serviceResults.innerHTML = '';
        serviceSearch.value = item.name;
        inputService.value = item.code;

        if (serviceFirst) {
            selectedCountry = null;
            countrySelected.classList.add('d-none');
            countrySearch.value = '';
            countryStep.style.display = '';
            resetPriceStep();
            loadCountries();
        } else {
            loadPrice();
        }
    }

    function selectCountry(item) {
        selectedCountry = item;
        countrySelected.classList.remove('d-none');
        countrySelected.textContent = '✓ ' + item.name;
        countryResults.innerHTML = '';
        countrySearch.value = item.name;
        inputCountry.value = item.id;

        if (serviceFirst) {
            loadPrice();
            return;
        }

        selectedService = null;
        serviceStep.style.display = '';
        resetPriceStep();
        serviceSelected.classList.add('d-none');
        serviceSearch.value = '';
        renderList(serviceResults, services, selectService, 'No services loaded');
    }

    function loadCountries() {
        if (countryLoading) countryLoading.classList.remove('d-none');
        countryResults.innerHTML = '<div class="list-group-item text-muted small">Loading countries…</div>';

        fetch(countriesUrl)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                countries = data.countries || [];
                if (countryLoading) countryLoading.classList.add('d-none');
                if (!countries.length) {
                    countryResults.innerHTML = '<div class="list-group-item text-muted small">No countries available.</div>';
                    return;
                }
                renderList(countryResults, countries, selectCountry, 'No matches');
            })
            .catch(function () {
                if (countryLoading) countryLoading.classList.add('d-none');
                countryResults.innerHTML = '<div class="list-group-item text-danger small">Could not load countries.</div>';
            });
    }

    function loadPrice() {
        if (!selectedCountry || !selectedService) return;
        priceStep.style.display = '';
        orderForm.style.display = 'none';
        buyBtn.disabled = true;
        priceBox.innerHTML = '<div class="text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Checking availability…</div>';

        const url = new URL(priceUrl);
        url.searchParams.set('country', selectedCountry.id);
        url.searchParams.set('service', selectedService.code);

        fetch(url)
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (res) {
                if (!res.ok) {
                    priceBox.innerHTML = '<div class="text-danger small">' + (res.data.message || 'Not available') + '</div>';
                    return;
                }
                const d = res.data;
                priceBox.innerHTML =
                    '<div class="amount">₦' + Number(d.ngn).toLocaleString('en-NG', {minimumFractionDigits: 2}) + '</div>' +
                    '<div class="text-muted small mt-1">' + (d.available ? d.available + ' available' : 'Available now') + '</div>';
                inputCountry.value = selectedCountry.id;
                inputService.value = selectedService.code;
                orderForm.style.display = '';
                buyBtn.disabled = false;
                buyBtn.textContent = 'Rent number — ₦' + Number(d.ngn).toLocaleString('en-NG', {minimumFractionDigits: 2});
            })
            .catch(function () {
                priceBox.innerHTML = '<div class="text-danger small">Could not load price. Try again.</div>';
            });
    }

    function loadServices() {
        serviceResults.innerHTML = '<div class="list-group-item text-muted small">Loading services…</div>';

        fetch(servicesUrl)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                services = data.services || [];
                if (data.error && serviceError) {
                    serviceError.textContent = data.error;
                    serviceError.classList.toggle('d-none', services.length > 0);
                }
                if (!services.length) {
                    serviceResults.innerHTML = '<div class="list-group-item text-danger small">Services could not be loaded.</div>';
                    return;
                }
                renderList(serviceResults, services, selectService, 'No matches');
            })
            .catch(function () {
                serviceResults.innerHTML = '<div class="list-group-item text-danger small">Could not load services.</div>';
            });
    }

    if (countrySearch) {
        countrySearch.addEventListener('input', function () {
            renderList(countryResults, filterItems(countries, countrySearch.value), selectCountry, 'No matches');
        });
        countrySearch.addEventListener('focus', function () {
            if (countries.length) renderList(countryResults, filterItems(countries, countrySearch.value), selectCountry, 'No matches');
        });
    }

    if (serviceSearch) {
        serviceSearch.addEventListener('input', function () {
            renderList(serviceResults, filterItems(services, serviceSearch.value), selectService, 'No matches');
        });
        serviceSearch.addEventListener('focus', function () {
            if (services.length) renderList(serviceResults, filterItems(services, serviceSearch.value), selectService, 'No matches');
        });
    }

    if (serviceFirst) {
        loadServices();
    } else {
        loadCountries();
        loadServices();
    }
})();
</script>
@endsection
