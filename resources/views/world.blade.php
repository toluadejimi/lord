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


                            <div class="d-flex justify-content-center my-3">
                                <a href="https://wa.me/2349036138449?text=Chequest+i+want+to+Buy+Permanent+Tmobile+or+UltraMobile+Esim"
                                   class="text-center" style="font-size: 16px;"> Buy Permanent Tmobile & UltraMobile Esim </a></strong>
                            </div>

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


                            <form action="check-av" method="POST">
                                @csrf

                                <div class="row">


                                    <div class="col-xl-10 col-md-10 col-sm-12 p-3">

                                        <p class="d-flex justify-content-center">You are on all 🌎 countries Panel</p>


                                        <p class="mb-3 text-muted d-flex justify-content-center"> Choose country and
                                            service
                                        </p>

                                        <hr>

                                        <label for="country" class="mb-2 mt-3 text-muted">🌎 Select
                                            Country</label>
                                        <div>
                                            <select style="border-color:rgb(0, 11, 136);" class="w-100"
                                                    id="select_page" class="operator" name="country">
                                                <option style="background: black" value=""> Select Country</option>
                                                @foreach ($countries as $data)
                                                    <option value="{{ $data->ID }}">{{ $data->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>


                                        <label for="country" class="mt-3 text-muted mb-2">💬 Select
                                            Services</label>
                                        <div>
                                            <select class="form-control w-100"
                                                    id="select_page2" name="service">

                                                <option value=""> Choose Service</option>
                                                @foreach ($services as $data)
                                                    <option value="{{ $data->ID }}">{{ $data->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>


                                        <button style="border: 0px" type="submit"
                                                class="btn btn-primary w-100 mt-3 border-0">Check
                                            availability
                                        </button>

                                    </div>
                                </div>
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

                                        <form action="order_now" method="POST">
                                            @csrf

                                            <input type="text" name="country" hidden value="{{ $count_id }}">
                                            <input type="text" name="price" hidden value="{{ $price }}">
                                            <input type="text" name="service" hidden value="{{ $serv }}">


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
