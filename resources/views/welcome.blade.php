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
                            @auth
                            <div class="col-4">
                                <a href="fund-wallet">
                                    <h3 class="mt-2 d-flex text-white justify-content-end">
                                        N{{number_format(Auth::user()->wallet, 2)}}</h3>
                                </a>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div><!-- [ breadcrumb ] end --><!-- [ Main Content ] start -->

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






        <div class="row">

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">


                        <div class="card-header d-flex justify-content-center">
                            <h2 class="text-center">
                                Cheapest and Fastest Online SMS verification
                            </h2>
                        </div>

                        <p class="text-center">
                            Don't feel comfortable giving out your phone number? Protect your online identity by using our one-time-use non-VoIP phone numbers.

                        </p>
                        <img  class="d-flex justify-content-center"  src="{{url('')}}/public/assets/images/front.svg">



                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">

{{--                        <div class="d-flex justify-content-center my-3">--}}
{{--                         <strong></strong>   <a href="https://wa.me/2349036138449?text=Chequest+i+want+to+Buy+Permanent+Tmobile+or+UltraMobile+Esim"--}}
{{--                               class="text-center" style="font-size: 16px;"> Buy Permanent Tmobile & UltraMobile Esim </a></strong>--}}
{{--                        </div>--}}

                        <div class="d-flex justify-content-center my-3">
                            <div class="btn-group" role="group" aria-label="Third group">
                                <a style="font-size: 12px;" href="/world"
                                   class="btn btn-primary w-200 mt-1">
                                    🌎 SERVER 1

                                </a>

                                <a style="font-size: 12px;" href="/cworld"
                                   class="btn btn-dark w-200 mt-1">
                                    🌎 SERVER 2

                                </a>


                            </div>

                        </div>


                        @auth
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
                        @else
                            <div class="card">
                                <div class="card-body">


                                    <a href="register" class="btn btn-dark w-100 my-3  btn-block">
                                        Register
                                    </a>

                                    <a href="login" class="btn btn-primary w-100  btn-block">
                                        Login
                                    </a>


                                </div>

                            </div>
                        @endauth


                    </div>
                </div>
            </div>





            @auth
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">

                        <div class="">

                            <div class="p-2 col-lg-6">
                                <strong>
                                    <h4>Verifications</h4>
                                </strong>
                            </div>

                            <div>


                                <div class="table-responsive ">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Service</th>
                                            <th>Phone No</th>
                                            <th>Code</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Date</th>


                                        </tr>
                                        </thead>
                                        <tbody>


                                        @forelse($verification as $data)
                                            <tr>
                                                <td style="font-size: 12px;">{{ $data->id }}</td>
                                                <td style="font-size: 12px;">{{ $data->service }}</td>
                                                <td style="font-size: 12px; color: green"><a
                                                        href="receive-sms?phone={{ $data->id }}">{{ $data->phone }} </a>
                                                </td>
                                                <td style="font-size: 12px;">{{ $data->sms }}</td>
                                                <td style="font-size: 12px;">₦{{ number_format($data->cost, 2) }}</td>
                                                <td>
                                                    @if ($data->status == 1)
                                                        <span style="background: orange; border:0px; font-size: 10px"
                                                              class="btn btn-warning btn-sm">Pending</span>
                                                        <a href="cancle-sms?id={{  $data->id }}&delete=1"
                                                           style="background: rgb(168, 0, 14); border:0px; font-size: 10px"
                                                           class="btn btn-warning btn-sm">Delete</span>

                                                            @else
                                                                <span style="font-size: 10px;"
                                                                      class="text-white btn btn-success btn-sm">Completed</span>
                                                    @endif

                                                </td>
                                                <td style="font-size: 12px;">{{ $data->created_at }}</td>
                                            </tr>

                                        @empty

                                            <h6>No verification found</h6>
                                        @endforelse

                                        </tbody>

                                        {{ $verification->links() }}

                                    </table>
                                </div>
                            </div>


                        </div>


                    </div>
                </div><!-- [ sample-page ] end -->
                @endauth
            </div>
        </div><!-- [ Main Content ] end --></div>
    </div>
    <!-- [ Main Content ] end -->



    <script>
        function filterServices() {
            var input, filter, serviceRows, serviceNames, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            serviceRows = document.getElementsByClassName("service-row");
            for (i = 0; i < serviceRows.length; i++) {
                serviceNames = serviceRows[i].getElementsByClassName("service-name");
                txtValue = serviceNames[0].textContent || serviceNames[0].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    serviceRows[i].style.display = "";
                } else {
                    serviceRows[i].style.display = "none";
                }
            }
        }
    </script>


@endsection
