@extends('layout.main')
@section('content')
@php
    $serviceFirst = ($pickerFlow ?? 'country-first') === 'service-first';
@endphp
<div class="pc-container">
    <div class="pc-content p-4">
        @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="mb-1">{{ $serverLabel }}</h2>
                <p class="text-muted mb-0 small">
                    @if($serviceFirst)
                        Pick a service, choose a country, confirm price, then rent a number.
                    @else
                        Search a country, pick a service, confirm price, then rent a number.
                    @endif
                </p>
            </div>
            <div class="text-end">
                <div class="text-muted small">Wallet</div>
                <div class="h5 mb-0">₦{{ number_format($wallet, 2) }}</div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        @if($serviceFirst)
                        <div class="step-block mb-4" id="service-step">
                            <div class="step-label">Step 1 — Service</div>
                            <input type="text" class="form-control" id="service-search" placeholder="Search services…" autocomplete="off">
                            <div id="service-results" class="list-group mt-2 hero-picker-list"></div>
                            <div id="service-selected" class="selected-pill mt-2 d-none"></div>
                            <div id="service-error" class="text-danger small mt-2 d-none"></div>
                        </div>

                        <div class="step-block mb-4" id="country-step" style="display:none;">
                            <div class="step-label">Step 2 — Country</div>
                            <input type="text" class="form-control" id="country-search" placeholder="Search countries…" autocomplete="off">
                            <div id="country-results" class="list-group mt-2 hero-picker-list"></div>
                            <div id="country-selected" class="selected-pill mt-2 d-none"></div>
                            <div id="country-loading" class="text-muted small mt-2 d-none">Loading countries for this service…</div>
                        </div>
                        @else
                        <div class="step-block mb-4" id="country-step">
                            <div class="step-label">Step 1 — Country</div>
                            <input type="text" class="form-control" id="country-search" placeholder="Search countries…" autocomplete="off">
                            <div id="country-results" class="list-group mt-2 hero-picker-list"></div>
                            <div id="country-selected" class="selected-pill mt-2 d-none"></div>
                        </div>

                        <div class="step-block mb-4" id="service-step" style="display:none;">
                            <div class="step-label">Step 2 — Service</div>
                            <input type="text" class="form-control" id="service-search" placeholder="Search services…" autocomplete="off">
                            <div id="service-results" class="list-group mt-2 hero-picker-list"></div>
                            <div id="service-selected" class="selected-pill mt-2 d-none"></div>
                            <div id="service-error" class="text-danger small mt-2 d-none"></div>
                        </div>
                        @endif

                        <div class="step-block mb-4" id="price-step" style="display:none;">
                            <div class="step-label">Step 3 — Price</div>
                            <div id="price-box" class="price-box text-center py-4">
                                <div class="text-muted">Checking availability…</div>
                            </div>
                        </div>

                        <form method="post" action="{{ $orderUrl }}" id="order-form" style="display:none;">
                            @csrf
                            <input type="hidden" name="country" id="input-country">
                            <input type="hidden" name="service" id="input-service">
                            <button type="submit" class="btn btn-primary btn-lg w-100" id="buy-btn" disabled>
                                Buy number
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                @include('partials.verification-list', ['verifications' => $verifications, 'pollUrl' => $pollUrl])
            </div>
        </div>
    </div>
</div>

<style>
.step-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6366f1; margin-bottom: .5rem; }
.hero-picker-list { max-height: 220px; overflow-y: auto; border-radius: 8px; }
.hero-picker-list .list-group-item { cursor: pointer; }
.hero-picker-list .list-group-item:hover { background: #f8f9ff; }
.selected-pill { display: inline-block; padding: .35rem .75rem; background: #eef2ff; border-radius: 999px; font-size: .875rem; }
.price-box { background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; }
.price-box .amount { font-size: 1.75rem; font-weight: 700; color: #4f46e5; }
</style>

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
            el.className = 'list-group-item list-group-item-action';
            const suffix = item.available ? ' (' + item.available + ')' : '';
            el.textContent = item.name + suffix;
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
        serviceSelected.textContent = 'Selected: ' + item.name;
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
        countrySelected.textContent = 'Selected: ' + item.name;
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
        if (countryLoading) {
            countryLoading.classList.remove('d-none');
        }
        countryResults.innerHTML = '<div class="list-group-item text-muted small">Loading countries…</div>';

        fetch(countriesUrl)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                countries = data.countries || [];
                if (countryLoading) {
                    countryLoading.classList.add('d-none');
                }
                if (!countries.length) {
                    countryResults.innerHTML = '<div class="list-group-item text-muted small">No countries available.</div>';
                    return;
                }
                renderList(countryResults, countries, selectCountry, 'No matches');
            })
            .catch(function () {
                if (countryLoading) {
                    countryLoading.classList.add('d-none');
                }
                countryResults.innerHTML = '<div class="list-group-item text-danger small">Could not load countries.</div>';
            });
    }

    function loadPrice() {
        if (!selectedCountry || !selectedService) return;
        priceStep.style.display = '';
        orderForm.style.display = 'none';
        buyBtn.disabled = true;
        priceBox.innerHTML = '<div class="text-muted">Checking availability…</div>';

        const url = new URL(priceUrl);
        url.searchParams.set('country', selectedCountry.id);
        url.searchParams.set('service', selectedService.code);

        fetch(url)
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (res) {
                if (!res.ok) {
                    priceBox.innerHTML = '<div class="text-danger">' + (res.data.message || 'Not available') + '</div>';
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
                buyBtn.textContent = 'Buy number — ₦' + Number(d.ngn).toLocaleString('en-NG', {minimumFractionDigits: 2});
            })
            .catch(function () {
                priceBox.innerHTML = '<div class="text-danger">Could not load price. Try again.</div>';
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
    }

    if (serviceSearch) {
        serviceSearch.addEventListener('input', function () {
            renderList(serviceResults, filterItems(services, serviceSearch.value), selectService, 'No matches');
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
