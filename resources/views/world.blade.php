@extends('layout.main')
@section('content')


    <style>
        .search-box {
            width: 300px;
            top: 10px;
            padding: 10px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .dropdown2 {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            width: 100%;
            box-sizing: border-box;
            margin-top: 70px;
        }

        .dropdown2 .item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
        }

        .dropdown2 .item:hover {
            background-color: #ddd;
        }


    </style>
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

                            </div>


                                <form action="check-av" method="POST">
                                    @csrf
                                    <label for="country" class="col-12 my-3"> Choose Country </label>
                                    <div class="search-container mb-3">
                                        <input type="text" id="search" name="country_name"
                                               class="form-control search-box d-flex justify-content-center"
                                               placeholder="Search country"
                                               onkeyup="filterItems()" onclick="toggleDropdown()">
                                        <div id="dropdown" class="dropdown2">
                                            @foreach ($countries as $data)
                                                <div class="item" data-id="{{ $data->ID }}" onclick="selectCountry(this)">
                                                    {{ $data->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <label for="service" class="col-12 my-3"> Choose
                                        Service </label>
                                    <div class="search-container mb-3">
                                        <input type="text" id="searchservice"
                                               class="form-control search-box"
                                               placeholder="Search service"
                                               onkeyup="filterItemsservice()" onclick="toggleDropdownservice()">
                                        <div id="dropdownservice" class="dropdown2">
                                            @foreach ($services as $data)
                                                <div class="item" data-id="{{ $data->ID }}" onclick="selectService(this)">
                                                    {{ $data->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <input type="hidden" name="selectedID" id="selectedID">
                                    <input type="hidden" name="serviceID" id="serviceID">


                                    <button type="submit"
                                            class="btn btn-primary btn-lg mt-3">Check Service Availability
                                    </button>


                                </form>


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
