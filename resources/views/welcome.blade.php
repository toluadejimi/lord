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
                                <a style="font-size: 12px;" href="/home"
                                   class="btn btn-primary w-200 mt-1">
                                    🇺🇸 USA NUMBERS

                                </a>

                                <a style="font-size: 12px;" href="/world"
                                   class="btn btn-dark w-200 mt-1">
                                    🌎 ALL COUNTRIES

                                </a>


                            </div>

                        </div>

                        <p class="d-flex justify-content-center">You are on 🇺🇸 USA Numbers only Panel</p>


                        <div class="">

                            <div class="p-2 col-lg-6">
                                <input type="text" id="searchInput" class="form-control"
                                       placeholder="Search for a service..." onkeyup="filterServices()">
                            </div>


                            <div class="row my-3 p-1 text-white"
                                 style="background: #dedede; border-radius: 10px; font-size: 10px; border-radius: 12px">
                                <div class="col-5">
                                    <h5 class="mt-2">Services</h5>
                                </div>
                                <div class="col">
                                    <h5 class="mt-2">Price</h5>
                                </div>
                            </div>


                        </div>


                        <div style="height:300px; width:100%; overflow-y: scroll;" class="p-2">


                            @foreach ($services as $key => $value)
                                <div class="row service-row">
                                    @foreach ($value as $innerKey => $innerValue)
                                        <div style="font-size: 11px" class="col-5 service-name">
                                            🇺🇸 {{ $innerValue->name }}
                                        </div>

                                        <div style="font-size: 11px" class="col">
                                            @php $cost = $get_rate * $innerValue->cost + $margin  @endphp
                                            <strong>N{{ number_format($cost, 2) }}</strong>
                                        </div>

                                        <div style="font-size: 11px" class="col">

                                        </div>


                                        <div class="col mr-3">
                                            @auth
                                            <a class="myButton" onclick="hideButton(this)"
                                               href="/order-now?service={{ $key }}&price={{ $cost }}&cost={{ $innerValue->cost }}&name={{ $innerValue->name }}">
                                                <i class="fa fa-shopping-bag"></i>
                                            </a>
                                            @endauth

                                                <a class=""
                                                   href="/login">
                                                    <i class="fa fa-lock text-dark"> Login</i>
                                                </a>


                                            <script>
                                                function hideButton(link) {
                                                    // Hide the clicked link
                                                    link.style.display = 'none';

                                                    setTimeout(function () {
                                                        link.style.display = 'inline'; // or 'block' depending on your layout
                                                    }, 5000); // 5 seconds
                                                }
                                            </script>


                                        </div>


                                        <hr style="border-color: #cccccc" class=" my-2">
                                    @endforeach
                                </div>
                            @endforeach


                        </div>


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
