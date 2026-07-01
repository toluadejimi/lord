@extends('layout.main')
@section('content')
<style>
.cw-page { --cw-accent: #4f46e5; }
.cw-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #a855f7 100%);
    border-radius: 16px; color: #fff; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;
    box-shadow: 0 10px 30px rgba(79, 70, 229, .25);
}
.cw-card {
    border: 0; border-radius: 16px; box-shadow: 0 4px 24px rgba(15, 23, 42, .06);
}
.cw-card .form-control {
    border-radius: 10px; border-color: #e2e8f0; padding: .7rem .9rem;
}
.cw-card .form-control:focus {
    border-color: var(--cw-accent); box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.cw-step {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: var(--cw-accent); margin-bottom: .5rem;
}
.cw-search-results {
    max-height: 240px; overflow-y: auto; position: absolute; width: 100%;
    z-index: 20; border-radius: 10px; box-shadow: 0 12px 32px rgba(15,23,42,.12);
}
.cw-search-results .list-group-item {
    cursor: pointer; border-color: #f1f5f9; padding: .65rem .9rem;
}
.cw-search-results .list-group-item:hover { background: #eef2ff; }
.cw-service-card {
    border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem 1.1rem;
    margin-bottom: .75rem; cursor: pointer; transition: .15s ease;
    background: #fff;
}
.cw-service-card:hover {
    border-color: #a5b4fc; box-shadow: 0 8px 24px rgba(79,70,229,.1);
    transform: translateY(-1px);
}
.cw-service-card .svc-name { font-weight: 700; color: #0f172a; font-size: .95rem; }
.cw-service-card .svc-price { font-weight: 800; color: #059669; font-size: 1rem; }
.cw-service-card .svc-meta { font-size: .8rem; color: #64748b; }
.cw-operator-title {
    font-size: .75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #94a3b8; margin: 1.25rem 0 .5rem;
}
.cw-server-pill {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
    border-radius: 999px; padding: .3rem .75rem; font-size: .8rem; font-weight: 600;
}
.cw-orders-card .table th {
    font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b;
}
.cw-orders-card .table td { font-size: .85rem; vertical-align: middle; }
.cw-badge-pending { background: #fef3c7; color: #b45309; font-size: .7rem; font-weight: 700; padding: .25rem .5rem; border-radius: 999px; }
.cw-badge-done { background: #d1fae5; color: #047857; font-size: .7rem; font-weight: 700; padding: .25rem .5rem; border-radius: 999px; }
.cw-empty { text-align: center; color: #94a3b8; padding: 2rem 1rem; }
#responseData:empty + .cw-empty-hint { display: block; }
.cw-empty-hint { display: none; color: #94a3b8; font-size: .875rem; text-align: center; padding: 1.5rem; }
</style>

<div class="pc-container">
    <div class="pc-content p-4 cw-page">
        <div class="cw-hero d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <span class="cw-server-pill mb-2"><i class="ti ti-world"></i> Server 1</span>
                <h2 class="h4 mb-1">SMS Verification</h2>
                <p class="mb-0 small opacity-90">Search a country, pick a service, and rent a number instantly</p>
            </div>
            <div class="text-end">
                <div class="small opacity-75">Wallet balance</div>
                <div class="h4 mb-0 fw-bold">₦{{ number_format((float) Auth::user()->wallet, 2) }}</div>
                <a href="{{ url('fund-wallet') }}" class="small text-white text-decoration-underline opacity-90">Fund wallet</a>
            </div>
        </div>

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

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card cw-card h-100">
                    <div class="card-body p-4">
                        <div class="cw-step">Step 1 — Country</div>
                        <div class="form-group position-relative mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="countrySearch"
                                    placeholder="Search for a country…" autocomplete="off">
                            </div>
                            <ul class="list-group cw-search-results mt-1" id="countryList" style="display:none;"></ul>
                        </div>

                        <div id="filterSearch" style="display:none;">
                            <div class="cw-step">Step 2 — Service</div>
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-filter text-muted"></i></span>
                                <input type="text" id="filterSearchInput" class="form-control border-start-0 ps-0"
                                    placeholder="Filter services…" autocomplete="off">
                            </div>
                        </div>

                        <div id="responseData"></div>
                        <p class="cw-empty-hint mb-0">
                            <i class="ti ti-map-pin d-block mb-2" style="font-size:1.75rem;"></i>
                            Select a country above to see available services and prices
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card cw-card cw-orders-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Recent orders</h5>
                            <a href="{{ url('orders') }}" class="small text-decoration-none">View all</a>
                        </div>

                        @if($verification->isEmpty())
                            <div class="cw-empty">
                                <i class="ti ti-inbox d-block mb-2" style="font-size:2rem;opacity:.5;"></i>
                                No orders yet. Rent a number to get started.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Service</th>
                                            <th>Number</th>
                                            <th>Code</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($verification->take(8) as $data)
                                            <tr>
                                                <td class="text-truncate" style="max-width:90px;">{{ $data->service }}</td>
                                                <td>
                                                    <a href="{{ url('receive-sms?phone='.$data->id) }}" class="text-decoration-none font-monospace small">
                                                        {{ $data->phone }}
                                                    </a>
                                                </td>
                                                <td class="font-monospace small">{{ $data->sms ?: '—' }}</td>
                                                <td>
                                                    @if ((int) $data->status === 1)
                                                        <span class="cw-badge-pending">Pending</span>
                                                    @else
                                                        <span class="cw-badge-done">Done</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var countries = @json($countries);
var currentData = {};
var rate = {{ $rate }};
var margin = {{ $margin }};

$(document).ready(function () {
    $('#filterSearch').hide();

    $('#countrySearch').on('input', function () {
        var searchValue = $(this).val().toLowerCase();
        var matchedCountries = '';

        if (searchValue) {
            for (var key in countries) {
                if (countries[key].toLowerCase().includes(searchValue)) {
                    matchedCountries += '<li class="list-group-item" data-country="' + key + '">' + countries[key] + '</li>';
                }
            }
            $('#countryList').html(matchedCountries).toggle(matchedCountries !== '');
        } else {
            $('#countryList').hide();
        }
    });

    $('#countryList').on('click', 'li', function () {
        var country = $(this).data('country');
        $('#countrySearch').val($(this).text());
        $('#countryList').hide();
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
            output += '<div class="cw-operator-title">' + key.toUpperCase() + '</div>';
            for (var providerId in data[key]) {
                for (var provider in data[key][providerId]) {
                    var providerData = data[key][providerId][provider];
                    var cost = providerData.cost * rate + margin;
                    var formatted = cost.toLocaleString('en-US', { style: 'currency', currency: 'NGN' });

                    output += '<div class="cw-service-card operator-card" data-country="' + key + '" data-operator="' + provider + '" data-product="' + providerId + '" data-count="' + providerData.count + '">' +
                        '<div class="d-flex justify-content-between align-items-start gap-2">' +
                        '<div><div class="svc-name">' + providerId + '</div><div class="svc-meta mt-1">' + provider + ' · ' + providerData.count + ' available</div></div>' +
                        '<div class="text-end"><div class="svc-price">' + formatted + '</div><div class="svc-meta"><i class="ti ti-shopping-cart"></i> Rent</div></div>' +
                        '</div></div>';
                }
            }
        }
        return output || '<p class="text-muted small text-center py-3">No services found for this country.</p>';
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
            data: {
                country: $card.data('country'),
                operator: $card.data('operator'),
                product: $card.data('product'),
                count: $card.data('count'),
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response === "2" || response === "0") {
                    alert('Verification not available.');
                    $card.data('loading', false).css('opacity', '1');
                } else if (response === "4") {
                    window.location.href = '/orders';
                } else if (response === "9") {
                    window.location.href = '/fund-wallet';
                } else if (response && response.code === 200) {
                    window.location.href = '/orders?id=' + response.id;
                } else {
                    alert('Could not complete purchase.');
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
