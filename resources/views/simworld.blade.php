@extends('layout.main')
@section('content')

    <div class="pc-container">
        <div class="pc-content"><!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">

                        </div>
                        <div class="col-12 row">
                            <div class="col-8">
                                <div class="page-header-title">
                                    <h2 class="d-flex justify-content-start">Welcome</h2>
                                </div>
                            </div>
                            <div class="col-4">
                                <a href="fund-wallet">
                                    <h3 class="mt-2 d-flex text-white justify-content-end">
                                        N{{number_format(Auth::user()->wallet, 2)}}</h3>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->


            <div class="row p-3">
                <div class="col-xl-6 col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session()->has('message'))
                                <div class="alert alert-success">
                                    {{ session()->get('message') }}
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger">
                                    {{ session()->get('error') }}
                                </div>
                            @endif


                            {{--                            <div class="d-flex justify-content-center my-3">--}}
                            {{--                                <a href="https://wa.me/2349036138449?text=Chequest+i+want+to+Buy+Permanent+Tmobile+or+UltraMobile+Esim"--}}
                            {{--                                   class="text-center" style="font-size: 16px;"> Buy Permanent Tmobile & UltraMobile Esim </a></strong>--}}
                            {{--                            </div>--}}

                            <div class="d-flex justify-content-center my-3">
                                <div class="btn-group" role="group" aria-label="Third group">
                                    <a style="font-size: 12px;" href="/home"
                                       class="btn btn-primary w-200 mt-1">
                                        🇺🇸 USA NUMBERS

                                    </a>

                                    <a style="font-size: 12px; box-shadow: deeppink" href="/world"
                                       class="btn btn-dark w-200 mt-1">
                                        🌎 ALL COUNTRIES

                                    </a>


                                </div>

                            </div>


                            <div class="row">


                                <div class="col-xl-10 col-md-10 col-sm-12 p-3">

                                    <p class="d-flex justify-content-center">You are on cheap all 🌎 countries
                                        Panel</p>


                                    <p class="mb-3 text-muted d-flex justify-content-center"> Choose country and
                                        service
                                    </p>

                                    <hr>


                                    <div class="form-group position-relative">
                                        <input type="text" class="form-control" id="countrySearch"
                                               placeholder="Search for a country...">
                                        <ul class="list-group search-results" id="countryList"></ul>
                                    </div>


                                    <!-- Filter Search Input -->
                                    <div class="mt-3" id="filterSearch">
                                        <input type="text" id="filterSearchInput" class="form-control"
                                               placeholder="Search for services...">
                                    </div>

                                    <div class="mt-5" id="responseData"></div>


                                </div>

                                <script>
                                    var countries = @json($countries);
                                    var currentData = {}; // Holds the raw API response data

                                    $(document).ready(function () {
                                        $('#filterSearch').hide();

                                        $('#countrySearch').on('input', function () {
                                            let searchValue = $(this).val().toLowerCase();
                                            let matchedCountries = '';

                                            if (searchValue) {
                                                for (let key in countries) {
                                                    if (countries[key].toLowerCase().includes(searchValue)) {
                                                        matchedCountries += `<li class="list-group-item" data-country="${key}">${countries[key]}</li>`;
                                                    }
                                                }
                                                $('#countryList').html(matchedCountries).show();
                                            } else {
                                                $('#countryList').hide();
                                            }
                                        });

                                        // When a country is clicked, trigger an AJAX request
                                        $('#countryList').on('click', 'li', function () {
                                            let country = $(this).data('country');
                                            $('#countrySearch').val($(this).text());
                                            $('#countryList').hide();

                                            // AJAX request to get country-specific data
                                            $.ajax({
                                                url: `/proxy/prices?country=${country}`,
                                                type: 'GET',
                                                success: function (response) {
                                                    currentData = response; // Save data for filtering later
                                                    let output = generateCards(response);
                                                    $('#responseData').html(output);
                                                    $('#filterSearch').show();
                                                },
                                                error: function (error) {
                                                    console.log(error);
                                                    $('#responseData').html('<p class="text-danger">Failed to retrieve data.</p>');
                                                }
                                            });
                                        });

                                        // Function to generate card HTML from data
                                        function generateCards(data) {
                                            let output = '';
                                            for (let key in data) {
                                                output += `<h6>${key.toUpperCase()}</h6>`;
                                                for (let providerId in data[key]) {
                                                    for (let provider in data[key][providerId]) {
                                                        let providerData = data[key][providerId][provider];
                                                        let multipliedCost = providerData.cost * {{$rate}} + {{$margin}};
                                                        let formattedMultipliedCost = multipliedCost.toLocaleString('en-US', {
                                                            style: 'currency',
                                                            currency: 'NGN'
                                                        });


                                                        output += `<div class="card mb-3 operator-card" data-country="${key}" data-operator="${provider}" data-product="${providerId}" data-count="${providerData.count}">
                                                            <div class="card-body">
                                                                   <div class="row">
                                                                    <div class="col-6 d-flex justify-content-start">
                                                                     <h6>${providerId}</h6>
                                                                    </div>
                                                                    <div class="col-6 d-flex justify-content-end">

                                                                    <h6 style="color: #0a3622;">${formattedMultipliedCost} </h6>
                                                                    </div>
                                                                    <div class="col-6 d-flex justify-content-start mt-2">
                                                                    <p>Available: ${providerData.count}</p>
                                                                    </div>

                                                                <div class="col-6 d-flex justify-content-end">
                                                                    <button class="btn btn-dark"><i
                                                                class="fa fa-shopping-bag"></i></button>
                                                                    </div>


                                                        </div>
                                                    </div>
                                                    </div>`;


                                                    }
                                                }
                                            }
                                            return output;
                                        }

                                        // Search within the loaded results
                                        $('#filterSearchInput').on('input', function () {
                                            let searchValue = $(this).val().toLowerCase();
                                            let filteredData = {};

                                            // Filter data based on the operator name or provider ID
                                            for (let key in currentData) {
                                                for (let providerId in currentData[key]) {
                                                    for (let provider in currentData[key][providerId]) {
                                                        if (provider.toLowerCase().includes(searchValue) || providerId.toLowerCase().includes(searchValue)) {
                                                            if (!filteredData[key]) filteredData[key] = {};
                                                            filteredData[key][providerId] = filteredData[key][providerId] || {};
                                                            filteredData[key][providerId][provider] = currentData[key][providerId][provider];
                                                        }
                                                    }
                                                }
                                            }

                                            // Update results
                                            let output = generateCards(filteredData);
                                            $('#responseData').html(output);
                                        });

                                        // When an operator is clicked, send a request to the backend controller
                                        $('#responseData').on('click', '.operator-card', function () {
                                            let country = $(this).data('country');
                                            let operator = $(this).data('operator');
                                            let product = $(this).data('product');
                                            let count = $(this).data('count');


                                            // Send to backend
                                            $.ajax({
                                                url: `/buy-csms`,
                                                type: 'POST',
                                                data: {
                                                    country: country,
                                                    operator: operator,
                                                    product: product,
                                                    count: count,
                                                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                                                },


                                                success: function (response) {

                                                    if (response === "2") {
                                                        alert('Verification Not Available.');
                                                    } else if (response === "4") {
                                                        window.location.href = '/orders'; // Modify the URL as needed
                                                    } else if (response === "9") {
                                                        window.location.href = '/fund-wallet'; // Modify the URL as needed
                                                    } else if (response === "0") {
                                                        alert('Verification Not Available.');
                                                    }else {
                                                        if (response.code === 200) {
                                                            var id =response.id;
                                                            window.location.href = '/orders?id=' + id; // Modify the URL as needed
                                                        }
                                                    }
                                                },
                                                error: function (error) {
                                                    console.log(error);
                                                    alert('Failed to complete purchase.');
                                                }
                                            });
                                        });
                                    });
                                </script>

                            </div>


                        </div>

                    </div>
                </div>


                <div class="col-xl-6 col-md-6 col-sm-12 p-3">

                    @if ($product != null)

                        <div class="card mb-3">
                            <div class="card-body">

                                <div class="row">
                                    <p class="text-muted">Service Infomation</p>
                                    <div class="col-xl-4 col-md-4 col-sm-6">
                                        <p class="text-muted">Price</p>
                                        <p>NGN {{ number_format($price, 2) }}</p>
                                    </div>

                                    <div class="col-xl-4 col-md-4 col-sm-6">
                                        <p class="text-muted">Success Rate</p>
                                        @if ($rate < 10)
                                            <p class="text-danger">{{ $rate }}%</p>
                                        @elseif ($rate < 20)
                                            <p class="text-danger">{{ $rate }}%</p>
                                        @elseif ($rate < 30)
                                            <p class="text-danger">{{ $rate }}%
                                            </p>
                                        @elseif ($rate < 40)
                                            <p class="text-warning">{{ $rate
                                                                }}%</p>
                                        @elseif ($rate < 50)
                                            <p class="text-warning">{{
                                                                    $rate }}%</p>
                                        @elseif ($rate < 60)
                                            <p class="text-success">{{
                                                                        $rate }}%</p>
                                        @elseif ($rate < 70)
                                            <p class="text-success">{{
                                                                            $rate }}%</p>
                                        @elseif ($rate < 80)
                                            <p
                                                class="text-success">{{ $rate }}%
                                            </p>
                                        @elseif ($rate < 90)
                                            <p
                                                class="text-success">{{ $rate
                                                                                    }}%</p>
                                        @elseif ($rate <= 100)
                                            <p
                                                class="text-success">{{
                                                                                        $rate }}%</p>
                                        @else
                                        @endif
                                    </div>


                                    @if(Auth::user()->wallet < $price)
                                        <a href="fund-wallet"
                                           class="btn btn-secondary text-white btn-lg">Fund Wallet</a>
                                    @else

                                        <form action="order_now_world" method="POST">
                                            @csrf
                                            <input type="text" name="country" hidden value="{{ $count_id }}">
                                            <input type="text" name="price" hidden value="{{ $price }}">
                                            <input type="text" name="service" hidden value="{{ $serv }}">
                                            <input type="text" name="ppe" hidden value="{{ $price }}">

                                            <button type="submit" class="btn btn-primary w-100 btn-sm mt-6">Buy Number
                                                Now
                                            </button>


                                        </form>

                                    @endif


                                </div>


                            </div>

                        </div>

                    @endif


                </div>

            </div>
        </div>
    </div>


    <!-- /.content-wrapper -->


    <script src="/livewire/livewire.js?id=90730a3b0e7144480175" data-turbo-eval="false"
            data-turbolinks-eval="false">
    </script>
    <script data-turbo-eval="false" data-turbolinks-eval="false">
        window.livewire = new Livewire();
        window.Livewire = window.livewire;
        window.livewire_app_url = '';
        window.livewire_token = 'JBt4aOzGju0YuBweWShPMRkAkmVxvzZzG4XOMx7V';
        window.deferLoadingAlpine = function (callback) {
            window.addEventListener('livewire:load', function () {
                callback();
            });
        };
        let started = false;
        window.addEventListener('alpine:initializing', function () {
            if (!started) {
                window.livewire.start();
                started = true;
            }
        });
        document.addEventListener("DOMContentLoaded", function () {
            if (!started) {
                window.livewire.start();
                started = true;
            }
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('data-table');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const countdownElement = row.cells[2]; // Assumes "Expires" is in the third column (index 2)
                let seconds = parseInt(countdownElement.getAttribute('data-seconds'), 10);

                const countdownInterval = setInterval(function () {
                    countdownElement.textContent = seconds + 's';

                    if (seconds <= 0) {
                        clearInterval(countdownInterval);
                        // Add your logic to handle the expiration, e.g., sendPostRequest(row);
                        console.log('Expired:', row);
                    }

                    seconds--;
                }, 1000);
            });

            // You may add the sendPostRequest function here or modify the code accordingly
        });
    </script>

    <script>
        $(document).ready(function () {
//change selectboxes to selectize mode to be searchable
            $("select").select2();
        });


    </script>

@endsection
