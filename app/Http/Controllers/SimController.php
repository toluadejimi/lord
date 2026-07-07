<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Services\SimWorldCatalogService;
use App\Services\AppConfigService;
use App\Support\VerificationLabels;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimController extends Controller
{

    public function index(request $request)
    {
        if (!app(AppConfigService::class)->getBool('provider_sim_enabled', true)) {
            return redirect('/')->with('error', VerificationLabels::customerMenuLabelForServer(1).' is not available right now.');
        }

        $countries = SimWorldCatalogService::countries();

        $verification = Verification::where('user_id', Auth::id())
            ->where('type', 3)
            ->latest()
            ->get();

        $s_rate = Setting::where('id', 3)->first();

        $data['countries'] = $countries;
        $data['verification'] = $verification;
        $data['product'] = null;
        $data['rate'] = (float) ($s_rate->rate ?? 0);
        $data['margin'] = (float) ($s_rate->margin ?? 0);
        $data['countries_error'] = $countries === [] ? 'Could not load countries. Check that SIMTOKEN is set in admin settings.' : null;

        return view('simworld', $data);
    }

    public function countriesJson()
    {
        $countries = SimWorldCatalogService::countries();

        return response()->json([
            'countries' => $countries,
            'count' => count($countries),
        ]);
    }

    protected function simToken(): ?string
    {
        if (function_exists('app_config')) {
            $token = app_config('SIMTOKEN');
            if ($token) {
                return $token;
            }
        }

        return env('SIMTOKEN') ?: null;
    }
    public function order_csms(request $request)
    {


//        $total_funded = Transaction::where('user_id', Auth::id())->where('status', 2)->sum('amount');
//        $total_bought = verification::where('user_id', Auth::id())->where('status', 2)->sum('cost');
//        if ($total_bought > $total_funded) {
//
//            $message = Auth::user()->email . " has been banned for cheating";
//            send_notification($message);
//            send_notification2($message);
//
//            User::where('id', Auth::id())->update(['status' => 9]);
//            Auth::logout();
//            return redirect('ban');
//
//        }



        $token = $this->simToken();
        if (!$token) {
            return response()->json(['code' => 0, 'message' => 'Server 1 is not configured (missing SIM token).'], 503);
        }

        $request->validate([
            'country' => 'required|string',
            'operator' => 'required|string',
            'product' => 'required|string',
            'count' => 'required|string',
        ]);



        if ($request->input('count') == '0') {
            return response()->json(['code' => 2, 'message' => 'Not available']);
        }




        $country = $request->input('country');
        $operator = $request->input('operator');
        $product = $request->input('product');

        if ($request->filled('usd_cost')) {
            $sRate = \App\Models\Setting::find(3);
            $cost = round(((float) $sRate->rate * (float) $request->usd_cost) + (float) $sRate->margin, 2);
        } else {
            $cost = SimWorldCatalogService::productCost($operator, $country, $product);
        }

        if ($cost == 0) {
            return response()->json(['code' => 0, 'message' => 'Price unavailable']);
        }

        if (Auth::user()->wallet < $cost) {
            return response()->json(['code' => 9, 'message' => 'Insufficient balance']);
        }

        $client = new Client();

        try {
            $response = $client->get("https://5sim.net/v1/user/buy/activation/$country/$operator/$product", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            $phone = str_replace("+", "", $responseBody['phone']);

            User::where('id', Auth::id())->decrement('wallet', $cost);

            Verification::where('phone', $phone)->where('status', 2)->delete() ?? null;
            $ver = new Verification();
            $ver->user_id = Auth::id();
            $ver->phone = $phone;
            $ver->order_id = $responseBody['id'];
            $ver->country =$responseBody['country'];
            $ver->service = $responseBody['product'];
            $ver->cost = $cost;
            $ver->api_cost = $responseBody['price'];
            $ver->status = 1;
            $ver->type = 3;
            $ver->save();

            return response()->json([
                'id' => $responseBody['id'],
                'code' => 200,
                'phone' => $phone,
            ]);



        } catch (\Exception $e) {
            // Handle errors
            return response()->json([
                'message' => 'Failed to complete purchase',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function get_s_country(request $request)
    {

        $balance = SimWorldCatalogService::countries();

        dd($balance);

    }
    public function check_av(Request $request)
    {

        $token = env('SIMTOKEN');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://5sim.net/v1/guest/prices?country=$request->country&product=$request->product",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => " ",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);




        $get_s_price = $var->price ?? null;
        $high_price = $var->high_price ?? null;
        $rate = $var->success_rate ?? null;
        $product = 1;

        if($high_price > 4){
            $price = $high_price * 1.3;
        }else{
            $price = $high_price;
        }



        if ($price == null) {
            return redirect('cworld')->with('error', 'Verification not available for selected service');
        } else {

            $get_rate = Setting::where('id', 3)->first()->rate;
            $margin = Setting::where('id', 3)->first()->margin;
            $verification = Verification::where('user_id', Auth::id())->get();
            $count_id = Country::where('country_id', $request->country)->first()->short_name ?? null;

            $ngnprice = ($price * $get_rate) + $margin;


            $data['count_id'] = $count_id;
            $data['serv'] = $request->service;
            $data['verification'] = $verification;
            $countries = get_world_countries();
            $services = get_world_services();
            $data['services'] = $services;
            $data['countries'] = $countries;
            $data['rate'] = $rate;
            $data['price'] = $ngnprice;
            $data['product'] = 1;

            $data['number_order'] = null;

            $verifications = Verification::where('user_id', Auth::id())->where('status', 1)->get();
            if ($verifications->count() > 1) {
                $data['pend'] = 1;
            } else {
                $data['pend'] = 0;
            }


            return view('world', $data);
        }
    }


    public function delete_sms(request $request, \App\Services\VerificationOrderService $orders)
    {
        $verification = Verification::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->where('type', 3)
            ->first();

        if ($verification === null) {
            return back()->with('error', 'Verification not found');
        }

        $result = $orders->cancelAndRefund($verification);

        return back()->with($result['success'] ? 'message' : 'error', $result['message']);
    }
    public function admin_delete_sms(request $request)
    {



        $token = env('SIMTOKEN');
        $ch = curl_init();
        $id = Verification::where('id', $request->id)->first()->order_id;
        $cost = Verification::where('id', $request->id)->first()->cost;
        $user_id = Verification::where('id', $request->id)->first()->user_id;

        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/user/cancel/' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $token;
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        $var = json_decode($result);
        $status = $var->status ?? null;



        if($status == "CANCELED"){

            Verification::where('id', $request->id)->delete();
            User::where('id', $request->user_id)->increment('wallet', $cost);
            return back()->with('message', "Number Canceled, NGN $cost has been refunded");

        }
        Verification::where('id', $request->id)->delete();
        return back()->with('error', "Number Canceled");

    }


    public function get_c_sms(request $request){

        $token = $this->simToken();
        if (!$token) {
            return response()->json(['message' => 'waiting for sms']);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/user/check/' . $request->id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $token;
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        $var = json_decode($result);
        $status = $var->status ?? null;
        $sms = $var->sms[0]->text ?? null;



        if($status == 'RECEIVED'){

            if($sms == null){
                $originalString = 'sms loading...';
                $processedString = str_replace('"', '', $originalString);
                return response()->json([
                    'message' => $processedString
                ]);
            }else{
                Verification::where('order_id', $request->id)->update(['full_sms' => $var->sms[0]->text, 'sms' => $var->sms[0]->code, 'status' => 2]);
            }

        }


        if($status == "FINISHED"){
            Verification::where('order_id', $request->id)->update(['full_sms' => $var->sms[0]->text, 'sms' => $var->sms[0]->code, 'status' => 2]);
        }

        $originalString = 'sms loading...';
        $processedString = str_replace('"', '', $originalString);
        return response()->json([
            'message' => $processedString
        ]);

    }

}
