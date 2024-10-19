<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorldNumberController extends Controller
{

    public function home(request $request)
    {

        $countries = get_world_countries();
        $services = get_world_services();

        $verification = Verification::where('user_id', Auth::id())->get();

        $data['services'] = $services;
        $data['countries'] = $countries;
        $data['verification'] = $verification;

        $data['product'] = null;



        return view('world', $data);
    }



    public function check_av(Request $request)
    {

        $data['transaction'] = Transaction::query()
            ->orderByRaw('updated_at DESC')
            ->where('user_id', Auth::id())
            ->take(10)->get();

        $key = env('WKEY');


        $databody = array(
            "key" => $key,
            "country" => $request->selectedID,
            "service" => $request->serviceID,
            "pool" => '7',
        );

        $body = json_encode($databody);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.smspool.net/request/price',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $databody,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $key"
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);

        $price = $var->price ?? null;
        $rate = $var->success_rate ?? null;
        $product = 1;


        if ($price == null) {
            return redirect('home')->with('error', 'Verification not available for selected service');
        } else {

            $get_rate = Setting::where('id', 1)->first()->rate;
            $margin = Setting::where('id', 1)->first()->margin;
            $verification = Verification::where('user_id', Auth::id())->get();
            $count_id = Country::where('country_id', $request->country)->first()->short_name ?? null;

            $ngnprice = ($price * $get_rate) + $margin;


            $data['count_id'] = $request->selectedID;
            $data['serv'] = $request->serviceID;
            $data['verification'] = $verification;
            $countries = get_world_countries();
            $services = get_world_services();
            $data['services'] = $services;
            $data['countries'] = $countries;
            $data['rate'] = $rate;
            $data['price'] = $ngnprice;
            $data['product'] = 1;
            $data['country_name'] = $request->country_name;

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



    public function  get_smscode(request $request)
    {


        //$sms =  Verification::where('phone', $request->num)->first()->sms ?? null;
        $sms =  Verification::where('phone', $request->num)->first()->sms ?? null;



        $originalString = 'waiting for sms';
        $processedString = str_replace('"', '', $originalString);


        if ($sms == null) {
            return response()->json([
                'message' => $processedString
            ]);
        } else {

            return response()->json([
                'message' => $sms
            ]);
        }
    }


    public function webhook(request $request)
    {

        $message = json_encode($request->all());
        send_notification($message);
    }








    public function order_now(Request $request)
    {

        $total_funded = Transaction::where('user_id', Auth::id())->where('status', 2)->sum('amount');
        $total_bought = verification::where('user_id', Auth::id())->where('status', 2)->sum('cost');
        if ($total_bought > $total_funded) {

            $message = Auth::user()->email . " has been banned for cheating";
            send_notification($message);
            send_notification2($message);

            User::where('id', Auth::id())->update(['status' => 9]);
            Auth::logout();
            return redirect('ban');

        }



        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }


        if($request->ppe != $request->price){
            return back()->with('error', "Insufficient Funds");
        }


        $country = $request->country;
        $service = $request->service;
        $price = $request->price;




        $ser_cost = pool_cost($service, $country);
        $get_rate = Setting::where('id', 1)->first()->rate;
        $margin = Setting::where('id', 1)->first()->margin;

        $f_cost = $get_rate * $ser_cost + $margin;

        User::where('id', Auth::id())->decrement('wallet', $f_cost);
        if(Auth::user()->wallet < $f_cost){
            return redirect('home')->with('error', "Insufficient Funds");
        }else{
            $price = $f_cost;
            $order = create_world_order($country, $service, $price);
        }


        if ($order == 5) {
            User::where('id', Auth::id())->increment('wallet', $request->price);
            return redirect('world')->with('error', 'Number Currently out of stock, Please check back later');
        }

        if ($order == 1) {
            User::where('id', Auth::id())->increment('wallet', $request->price);
            $message = "SMS LORD | Low balance";
            send_notification($message);

            return redirect('world')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 2) {
            User::where('id', Auth::id())->increment('wallet', $request->price);
            $message = "SMS LORD | Error";
            send_notification($message);
            send_notification3($message);

            return redirect('world')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 3) {
            return redirect('orders');
        }
    }


    public function cancle_sms(Request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;

        if ($order == null) {
            return redirect('home')->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return redirect('home')->with('message', "Order Completed");
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_order($orderID);

            if ($can_order == 0) {

                $ck = Verification::where('id', $request->id)->first() ?? null;
                if($ck != null){

                    if($ck->status == 0){
                        $amount = number_format($order->cost, 2);
                        User::where('id', Auth::id())->increment('wallet', $order->cost);
                        Verification::where('id', $request->id)->delete();
                        return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
                    }
                }

                return back()->with('error', 'Order has been deleted or completed');
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {


                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return redirect('home')->with('error', "Please try again later");
                }

                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }
        }
    }


    public function check_sms(Request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;

        if ($order == null) {
            return back()->with('error', 'Order not found');
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $sms = check_sms($orderID);

            if ($sms == 1) {
                return redirect('home2')->with('error', 'Sms Pending, please wait and refresh again');
            }

            if ($sms == 6) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been canceled, NGN$amount has been refunded");
            }

            if ($sms == 6) {
                return back()->with('message', 'Sms Received, order completed');
            }
        }
    }

}
