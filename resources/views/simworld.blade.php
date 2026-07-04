@extends('layout.main')
@section('content')
@include('partials.server-page-styles')
<div class="pc-container">
    <div class="pc-content p-4 sv-page sv-theme-1">
        @include('partials.server-hero', [
            'serverNum' => 1,
            'title' => 'SMS Verification',
            'subtitle' => 'Search a country, pick a service, and rent a number instantly',
        ])

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif
        @if (!empty($countries_error))
            <div class="alert alert-warning border-0 shadow-sm">{{ $countries_error }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card sv-card h-100">
                    <div class="card-body">
                        <div class="sv-step">Step 1 — Country</div>
                        <div class="form-group position-relative mb-3">
                            <div class="sv-field sv-field--solo">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" id="countrySearch"
                                        placeholder="Search for a country…" autocomplete="off">
                                </div>
                            </div>
                            <ul class="list-group sv-search-dropdown" id="countryList" style="display:none;"></ul>
                        </div>

                        <div id="filterSearch" style="display:none;">
                            <div class="sv-step">Step 2 — Service</div>
                            <div class="sv-field sv-field--solo mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-filter"></i></span>
                                    <input type="text" id="filterSearchInput" class="form-control"
                                        placeholder="Filter services…" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div id="responseData"></div>
                        <p class="sv-empty-hint mb-0" id="emptyHint">
                            <i class="ti ti-map-pin d-block mb-2" style="font-size:1.75rem;opacity:.5;"></i>
                            Select a country above to see available services and prices
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                @include('partials.verification-orders-panel', [
                    'verifications' => $verification->take(8),
                    'panelTitle' => 'Recent orders',
                    'panelLink' => url('orders'),
                    'panelId' => 'simworld-orders-panel',
                    'ordersPanelClass' => 'sv-orders-panel',
                ])
            </div>
        </div>
    </div>
</div>

<script>
var countries = @json($countries);
var currentData = {};
var rate = {{ $rate }};
var margin = {{ $margin }};
var countriesUrl = @json(url('cworld/countries'));

function renderCountryList(searchValue) {
    searchValue = (searchValue || '').toLowerCase();
    var matchedCountries = '';
    var count = 0;

    for (var key in countries) {
        if (!searchValue || countries[key].toLowerCase().includes(searchValue) || key.toLowerCase().includes(searchValue)) {
            matchedCountries += '<li class="list-group-item list-group-item-action" data-country="' + key + '">' + countries[key] + '</li>';
            count++;
            if (!searchValue && count >= 30) break;
        }
    }

    if (count === 0) {
        $('#countryList').html('<li class="list-group-item text-muted small">No countries found</li>').show();
    } else {
        $('#countryList').html(matchedCountries).show();
    }
}

function loadCountriesFallback() {
    return $.getJSON(countriesUrl).then(function (res) {
        if (res.countries && Object.keys(res.countries).length) {
            countries = res.countries;
        }
    });
}

$(document).ready(function () {
    $('#filterSearch').hide();

    if (!countries || Object.keys(countries).length === 0) {
        loadCountriesFallback().always(function () {
            if (!countries || Object.keys(countries).length === 0) {
                $('#countrySearch').attr('placeholder', 'Countries unavailable — contact support');
            }
        });
    }

    $('#countrySearch').on('focus input', function () {
        renderCountryList($(this).val());
    });

    $('#countryList').on('click', 'li[data-country]', function () {
        var country = $(this).data('country');
        $('#countrySearch').val($(this).text());
        $('#countryList').hide();
        $('#emptyHint').hide();
        $('#responseData').html('<div class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading services…</div>');

        $.ajax({
            url: '/proxy/prices?country=' + country,
            type: 'GET',
            success: function (response) {
                currentData = response;
                $('#responseData').html(generateCards(response));
                $('#filterSearch').show();
            },
            error: function () {
                $('#responseData').html('<p class="text-danger small mb-0">Failed to load services. Please try again.</p>');
            }
        });
    });

    function generateCards(data) {
        var output = '';
        for (var key in data) {
            output += '<div class="sv-operator-title">' + key.toUpperCase() + '</div>';
            for (var providerId in data[key]) {
                for (var provider in data[key][providerId]) {
                    var providerData = data[key][providerId][provider];
                    var cost = providerData.cost * rate + margin;
                    var formatted = cost.toLocaleString('en-US', { style: 'currency', currency: 'NGN' });

                    output += '<div class="sv-service-card operator-card" data-country="' + key + '" data-operator="' + provider + '" data-product="' + providerId + '" data-count="' + providerData.count + '" data-usd-cost="' + providerData.cost + '">' +
                        '<div class="d-flex justify-content-between align-items-start gap-2">' +
                        '<div><div class="svc-name">' + providerId + '</div><div class="svc-meta mt-1">' + provider + ' · ' + providerData.count + ' available</div></div>' +
                        '<div class="text-end"><div class="svc-price">' + formatted + '</div><div class="svc-meta"><i class="ti ti-shopping-cart"></i> Tap to rent</div></div>' +
                        '</div></div>';
                }
            }
        }
        return output || '<p class="sv-empty-hint">No services found for this country.</p>';
    }

    $('#filterSearchInput').on('input', function () {
        var searchValue = $(this).val().toLowerCase();
        var filteredData = {};

        for (var key in currentData) {
            for (var providerId in currentData[key]) {
                for (var provider in currentData[key][providerId]) {
                    if (provider.toLowerCase().includes(searchValue) || providerId.toLowerCase().includes(searchValue)) {
                        if (!filteredData[key]) filteredData[key] = {};
                        if (!filteredData[key][providerId]) filteredData[key][providerId] = {};
                        filteredData[key][providerId][provider] = currentData[key][providerId][provider];
                    }
                }
            }
        }

        $('#responseData').html(generateCards(filteredData));
    });

    $('#responseData').on('click', '.operator-card', function () {
        var $card = $(this);
        if ($card.data('loading')) return;
        $card.data('loading', true).css('opacity', '.6');

        $.ajax({
            url: '/buy-csms',
            type: 'POST',
            dataType: 'json',
            data: {
                country: $card.data('country'),
                operator: $card.data('operator'),
                product: $card.data('product'),
                count: $card.data('count'),
                usd_cost: $card.data('usd-cost'),
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                var code = response && (response.code || response);
                if (code === 2 || code === '2' || code === 0 || code === '0') {
                    alert(response.message || 'Verification not available.');
                    $card.data('loading', false).css('opacity', '1');
                } else if (code === 4 || code === '4') {
                    window.location.href = '/orders';
                } else if (code === 9 || code === '9') {
                    window.location.href = '/fund-wallet';
                } else if (response && Number(response.code) === 200) {
                    window.location.reload();
                } else {
                    alert(response.message || 'Could not complete purchase.');
                    $card.data('loading', false).css('opacity', '1');
                }
            },
            error: function () {
                alert('Failed to complete purchase.');
                $card.data('loading', false).css('opacity', '1');
            }
        });
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#countrySearch, #countryList').length) {
            $('#countryList').hide();
        }
    });
});
</script>
@endsection
