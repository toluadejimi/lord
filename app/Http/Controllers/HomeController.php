<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use App\Models\ManualPayment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\SoldLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;


class HomeController extends Controller
{
    public function index(request $request)
    {
        $countries = get_s_countries();

        $verification = Verification::where('user_id', Auth::id())->paginate(10);
        $s_rate = Setting::where('id', 3)->first();

        //$data['services'] = $services;
        $data['countries'] = $countries;
        $data['verification'] = $verification;

        $data['product'] = null;

        $data['rate'] = $s_rate->rate;
        $data['margin']= $s_rate->margin;


        return view('welcome', $data);
    }


    public function home(request $request)
    {

        $data['services'] = get_services();
        $data['get_rate'] = Setting::where('id', 4)->first()->rate;
        $data['margin'] = Setting::where('id', 4)->first()->margin;

        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->paginate('10');


        $data['order'] = 0;


        return view('home', $data);
    }


    public function pendng_sms(Request $request)
    {

        return view('receive-sms');

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

        $service = $request->service;
        $price = $request->price;
        $service_name = $request->name;

        $service = $request->service;
        $price = $request->price;
        $service_name = $request->name;

        $data['services'] = get_services();
        $data['get_rate'] = Setting::where('id', 4)->first()->rate;
        $data['margin'] = Setting::where('id', 4)->first()->margin;
        $innerValue =  get_d_price($service);
        $cost = $data['get_rate'] * $innerValue + $data['margin'];
        $ip = $request->ip();

        if($cost < $request->price){
            return redirect('home')->with('error', "Insufficient Funds");
        }


        if (Auth::user()->wallet < $cost) {
            return back()->with('error', "Insufficient Funds");
        }


        User::where('id', Auth::id())->decrement('wallet', $cost);
        $hold =  User::where('id', Auth::id())->increment('hold_wallet', $cost);

        if($hold == 1){
            $order = create_order($service, $price, $cost, $service_name, $ip);
        }else{
            return redirect('home')->with('error', "Insufficient Funds");
        }









        if ($order == 0) {
            User::where('id', Auth::id())->increment('wallet', $request->price);
            return redirect('home')->with('error', 'Number Currently out of stock, Please check back later');
        }

        if ($order == 0) {
            User::where('id', Auth::id())->increment('wallet', $request->price);
            $message = "SMSLORD | Low balance";
            send_notification($message);
            return redirect('home')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 0) {
            User::where('id', Auth::id())->increment('wallet', $request->price);
            $message = "SMSLORD | Error";
            send_notification($message);


            return redirect('home')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 1) {

            $data['services'] = get_services();
            $data['get_rate'] = Setting::where('id', 1)->first()->rate;
            $data['margin'] = Setting::where('id', 1)->first()->margin;
            $data['sms_order'] = Verification::where('user_id', Auth::id())->where('status', 1)->first();
            $data['order'] = 1;

            $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

            return redirect('home');
        }
    }





    public function cancle_sms(Request $request)
    {


        $order = Verification::latest()->where('id', $request->id)->first() ?? null;


        if ($order == null) {
            return redirect('home')->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return redirect('home')->with('message', "Order Completed");
        }

        if ($order->status == 1 && $order->type == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_order($orderID);

            if ($request->delete == 1) {

                if ($order->status == 1) {

                    $amount = number_format($order->cost, 2);
                    User::where('id', Auth::id())->increment('wallet', $order->cost);
                    User::where('id', Auth::id())->decrement('hold_wallet', $order->cost);
                    Verification::where('id', $request->id)->delete();
                    return redirect('home')->with('message', "Order has been canceled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {
                return redirect('home')->with('error', "Order has been removed");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                User::where('id', Auth::id())->decrement('hold_wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been canceled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return redirect('home')->with('error', "Please try again later");
                }
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                User::where('id', Auth::id())->decrement('hold_wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been canceled, NGN$amount has been refunded");
            }
        }

        if ($order->status == 1 && $order->type == 2) {


            $orderID = $order->order_id;

            $can_order = cancel_world_order($orderID);

            if ($request->delete == 1) {


                if ($order->status == 1) {

                    $amount = number_format($order->cost, 2);
                    User::where('id', Auth::id())->increment('wallet', $order->cost);
                    User::where('id', Auth::id())->decrement('hold_wallet', $order->cost);
                    Verification::where('id', $request->id)->delete();
                    return redirect('world')->with('message', "Order has been canceled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {
                return back()->with('message', "Your order cannot be cancelled yet, please try again later.");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                User::where('id', Auth::id())->decrement('hold_wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('world')->with('message', "Order has been canceled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return redirect('world')->with('error', "Please try again later");
                }
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                User::where('id', Auth::id())->decrement('hold_wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('world')->with('message', "Order has been canceled, NGN$amount has been refunded");
            }
        }
    }

    public function admin_cancle_sms(Request $request)
    {


        $order = Verification::latest()->where('id', $request->id)->first() ?? null;


        if ($order == null) {
            return back()->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return back()->with('message', "Order Completed");
        }

        if ($order->status == 1 && $order->type == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_order($orderID);

            if ($request->delete == 1) {

                if ($order->status == 1) {

                    $amount = number_format($order->cost, 2);
                    User::where('id', $request->user_id)->increment('wallet', $order->cost);
                    User::where('id', $request->user_id)->decrement('hold_wallet', $order->cost);
                    Verification::where('id', $request->id)->delete();
                    return back()->with('message', "Order has been canceled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {
                return back()->with('error', "Order has been removed");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', $request->user_id)->increment('wallet', $order->cost);
                User::where('id', $request->user_id)->decrement('hold_wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return back()->with('error', "Please try again later");
                }
                $amount = number_format($order->cost, 2);
                User::where('id', $request->user_id)->increment('wallet', $order->cost);
                User::where('id', $request->user_id)->decrement('hold_wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }
        }

        if ($order->status == 1 && $order->type == 2) {


            $orderID = $order->order_id;

            $can_order = cancel_world_order($orderID);

            if ($request->delete == 1) {


                if ($order->status == 1) {

                    $amount = number_format($order->cost, 2);
                    User::where('id', $request->user_id)->increment('wallet', $order->cost);
                    User::where('id', $request->user_id)->decrement('hold_wallet', $order->cost);
                    Verification::where('id', $request->id)->delete();
                    return back()->with('message', "Order has been canceled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {
                return back()->with('message', "Your order cannot be cancelled yet, please try again later.");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', $request->user_id)->increment('wallet', $order->cost);
                User::where('id', $request->user_id)->decrement('hold_wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return back()->with('error', "Please try again later");
                }
                $amount = number_format($order->cost, 2);
                User::where('id', $request->user_id)->increment('wallet', $order->cost);
                User::where('id', $request->user_id)->decrement('hold_wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }
        }
    }


    public function check_sms(Request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;


        if ($request->count == 1) {

            $status = $order->status;

            if ($status == 1 || $status == 0) {

                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been canceled, NGN$amount has been refunded");

            }
        }

        $orderID = $order->order_id;
        $chk = check_sms($orderID);
        if ($chk == 3) {
            return redirect('home')->with('message', 'Sms Received, order completed');
        }

        if ($chk == 1) {
            return back()->with('error', 'No order found');
        }

        if ($chk == 2) {
            return back()->with('message', 'Please wait we are getting your sms');
        }

        if ($chk == 4) {
            return back()->with('error', 'Order has been cancled');
        }

    }

    public function fund_wallet(Request $request)
    {
        $user = Auth::id() ?? null;
        $pay = PaymentMethod::all();
        $transaction = Transaction::query()
            ->orderByRaw('updated_at DESC')
            ->where('user_id', Auth::id())
            ->paginate(10);

        return view('fund-wallet', compact('user', 'pay', 'transaction'));
    }


    public function fund_now(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|gt:0',
        ]);


            Transaction::where('user_id', Auth::id())->where('status', 1)->delete() ?? null;


        if ($request->type == 1) {

            if ($request->amount < 2000) {
                return back()->with('error', 'You can not fund less than NGN 2000');
            }


            if ($request->amount > 100000) {
                return back()->with('error', 'You can not fund more than NGN 100,000');
            }


            $key = env('WEBKEY');
            $ref = "VERF" . random_int(000, 999) . date('ymdhis');
            $email = Auth::user()->email;

            $url = "https://web.sprintpay.online/pay?amount=$request->amount&key=$key&ref=$ref&email=$email";


            $data = new Transaction();
            $data->user_id = Auth::id();
            $data->amount = $request->amount;
            $data->ref_id = $ref;
            $data->type = 2;
            $data->status = 1; //initiate
            $data->save();


            $message = Auth::user()->email . "| wants to fund |  NGN " . number_format($request->amount) . " | with ref | $ref |  on SMSLORD";
            send_notification2($message);


            return Redirect::to($url);
        }


        if ($request->type == 2) {

            if ($request->amount < 2000) {
                return back()->with('error', 'You can not fund less than NGN 2000');
            }


            if ($request->amount > 100000) {
                return back()->with('error', 'You can not fund more than NGN 100,000');
            }


            $ref = "VERFM" . random_int(000, 999) . date('ymdhis');
            $email = Auth::user()->email;


            $data = new Transaction();
            $data->user_id = Auth::id();
            $data->amount = $request->amount;
            $data->ref_id = $ref;
            $data->type = 2; //manual funding
            $data->status = 1; //initiate
            $data->save();


            $message = Auth::user()->email . "| wants to fund Manually |  NGN " . number_format($request->amount) . " | with ref | $ref |  on SMSLORD";
            send_notification2($message);


            $data['account_details'] = AccountDetail::where('id', 1)->first();
            $data['amount'] = $request->amount;

            return view('manual-fund', $data);
        }


    }


    public function fund_manual_now(Request $request)
    {


        if ($request->receipt == null) {
            return back()->with('error', "Payment receipt is required");
        }


        $file = $request->file('receipt');
        $receipt_fileName = date("ymis") . $file->getClientOriginalName();
        $destinationPath = public_path() . 'upload/receipt';
        $request->receipt->move(public_path('upload/receipt'), $receipt_fileName);


        $pay = new ManualPayment();
        $pay->receipt = $receipt_fileName;
        $pay->user_id = Auth::id();
        $pay->amount = $request->amount;
        $pay->save();


        $message = Auth::user()->email . "| submitted payment receipt |  NGN " . number_format($request->amount) . " | on SMSLORD";
        send_notification2($message);


        return view('confirm-pay');
    }


    public function confirm_pay(Request $request)
    {

        return view('confirm-pay');
    }


    public function verify_payment(request $request)
    {

        if($request->status == "success"){
            return redirect('fund-wallet')->with('message', "Wallet has been funded with $request->amount");
        }else{
            return redirect('fund-wallet')->with('error', 'Transaction has been canceled');
        }

    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {


            if(Auth::user()->status == 9){
                Auth::logout();
                return redirect('/user-ban');
            }


            $user = Auth::user();
            if ($user->session_id && $user->session_id !== session()->getId()) {
                session()->getHandler()->destroy($user->session_id);
            }
            $user->session_id = session()->getId();
            $user->save();

            $user = Auth::id() ?? null;
            return redirect('usno');
        }

        return back()->with('error', "Email or Password Incorrect");
    }


    public function destroy(Request $request)
    {
        $user = Auth::user();
        $user->session_id = null; // Clear session ID
        $user->save();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }


    public function register_index(Request $request)
    {
        return view('Auth.register');
    }


    public function login_index(Request $request)
    {
        return view('Auth.login');
    }


    public function forget_password(Request $request)
    {
        return view('Auth.forgot-password');
    }


    public function register(Request $request)
    {
        // Validate the user input
        $validatedData = $request->validate([
            'username' => 'required||string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:4|confirmed',
        ]);

        // Create a new user
        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        auth()->login($user);
        return redirect('home')->with('message', 'Welcome your account has been successfully created');
    }


    public function profile(request $request)
    {


        $user = Auth::id();
        $orders = SoldLog::latest()->where('user_id', Auth::id())->paginate(5);


        return view('profile', compact('user', 'orders'));
    }


    public function logout(Request $request)
    {

        Auth::logout();
        return redirect('/');
    }


    public function session_resolve(request $request)
    {


        $session_id = $request->session_id;
        $ref = $request->ref_id;


        $resolve = session_resolve($session_id, $ref);

        $status = $resolve[0]['status'];
        $amount = $resolve[0]['amount'];
        $message = $resolve[0]['message'];


        $trx = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;
        if ($trx == null) {

            $message = Auth::user()->email . "is trying to resolve from deleted transaction on SMSLORD";
            send_notification($message);

            $message = Auth::user()->email . "is trying to reslove from deleted transaction on SMSLORD";
            send_notification2($message);


            return back()->with('error', "Transaction has been deleted");
        }


        $chk = Transaction::where('ref_id', $request->ref_id)->first()->status ?? null;

        if ($chk == 2 || $chk == 4) {

            $message = Auth::user()->email . "is trying to steal hits the endpoint twice on SMSLORD";
            send_notification($message);

            $message = Auth::user()->email . "is trying to steal hits the endpoint twice on SMSLORD";
            send_notification2($message);


            return back()->with('message', "Error Occured");
        }


        if ($status == 'true') {

            User::where('id', Auth::id())->increment('wallet', $amount);
            Transaction::where('ref_id', $request->ref_id)->update(['status' => 4]);


            $ref = "LOG-" . random_int(000, 999) . date('ymdhis');


            $data = new Transaction();
            $data->user_id = Auth::id();
            $data->amount = $amount;
            $data->ref_id = $ref;
            $data->type = 2;
            $data->status = 2;
            $data->save();


            $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on SMSLORD";
            send_notification($message);

            $message = Auth::user()->email . "| just resolved with $request->session_id | NGN " . number_format($amount) . " on SMSLORD";
            send_notification2($message);


            return back()->with('message', "Transaction successfully Resolved, NGN $amount added to ur wallet");
        }

        if ($status == false) {
            return back()->with('error', "$message");
        }
    }


    public function change_password(request $request)
    {

        $user = Auth::id();


        return view('change-password', compact('user'));
    }


    public function faq(request $request)
    {
        $user = Auth::id();
        return view('faq', compact('user'));
    }

    public function terms(request $request)
    {
        $user = Auth::id();
        return view('terms', compact('user'));
    }

    public function rules(request $request)
    {
        $user = Auth::id();
        return view('rules', compact('user'));
    }


    public function update_password_now(request $request)
    {
        // Validate the user input
        $validatedData = $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);

        User::where('id', Auth::id())->update([
            'password' => Hash::make($validatedData['password']),
        ]);

        // Redirect the user to a protected route or dashboard
        return back()->with('message', 'Password Changed Successfully');
    }


    // public function forget_password(request $request)
    // {

    //     $user = Auth::id() ?? null;

    //     return view('forget-password', compact('user'));
    // }

    public function reset_password(request $request)
    {

        $email = $request->email;
        $expiryTimestamp = time() + 24 * 60 * 60; // 24 hours in seconds
        $url = url('') . "/verify-password?code=$expiryTimestamp&email=$request->email";

        $ck = User::where('email', $request->email)->first()->email ?? null;
        $username = User::where('email', $request->email)->first()->username ?? null;


        if ($ck == $request->email) {

            User::where('email', $email)->update([
                'code' => $expiryTimestamp
            ]);

            $data = array(
                'fromsender' => 'noreply@smslord.com', 'SMSLORD',
                'subject' => "Reset Password",
                'toreceiver' => $email,
                'url' => $url,
                'user' => $username,
            );


            Mail::send('reset-password-mail', ["data1" => $data], function ($message) use ($data) {
                $message->from($data['fromsender']);
                $message->to($data['toreceiver']);
                $message->subject($data['subject']);
            });

            return redirect('/forgot-password')->with('message', "A reset password mail has been sent to $request->email, if not inside inbox check your spam folder");
        } else {
            return back()->with('error', 'Email can not be found on our system');
        }
    }


    public function verify_password(request $request)
    {

        $code = User::where('email', $request->email)->first()->code;


        $storedExpiryTimestamp = $request->code;;

        if (time() >= $storedExpiryTimestamp) {

            $user = Auth::id() ?? null;
            $email = $request->email;
            return view('expired', compact('user', 'email'));
        } else {

            $user = Auth::id() ?? null;
            $email = $request->email;

            return view('verify-password', compact('user', 'email'));
        }
    }


    public function expired(request $request)
    {
        $user = Auth::id() ?? null;
        return view('expired', compact('user'));
    }

    public function reset_password_now(request $request)
    {

        $validatedData = $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);


        $password = Hash::make($validatedData['password']);

        User::where('email', $request->email)->update([

            'password' => $password

        ]);

        return redirect('/login')->with('message', 'Password reset successful, Please login to continue');
    }


    public function resloveDeposit(Request $request)
    {
        $dep = Transaction::where('ref_id', $request->trx_ref)->first() ?? null;


        if ($dep == null) {
            return back()->with('error', "Transaction not Found");
        }

        if ($dep->status == 2) {
            return back()->with('error', "This Transaction has been successful");
        }


        if ($dep->status == 4) {
            return back()->with('error', "This Transaction has been resolved");
        }


        if ($dep == null) {
            return back()->with('error', "Transaction has been deleted");
        } else {

            $ref = $request->trx_ref;
            $user = Auth::user() ?? null;
            return view('resolve-page', compact('ref', 'user'));
        }
    }


    public function resolveNow(request $request)
    {

        if ($request->trx_ref == null || $request->session_id == null) {
            return back()->with('error', "Session ID or Ref Can not be null");
        }


        $trx = Transaction::where('ref_id', $request->trx_ref)->first()->status ?? null;
        $ck_trx = (int)$trx;
        if ($ck_trx == 2) {

            $email = Auth::user()->email;
            $message = "$email | SMSLORD  | is trying to fund and a successful order with orderid $request->trx_ref";
            send_notification2($message);

            $message = "$email | SMSLORD  | is trying to fund and a successful order with orderid $request->trx_ref";
            send_notification($message);


            return back()->with('error', "This Transaction has been successful");
        }


        if ($ck_trx != 1) {

            $email = Auth::user()->email;
            $message = "$email | SMSLORD  | is trying to fund and a successful order with orderid $request->trx_ref";
            send_notification2($message);


            $message = "$email | SMSLORD | is trying to fund and a successful order with orderid $request->trx_ref";
            send_notification($message);


            return back()->with('error', "This Transaction has been successful");
        }

        if ($ck_trx == 2) {

            $email = Auth::user()->email;
            $message = "$email |SMSLORD | is trying to fund and a successful order with orderid $request->trx_ref";
            send_notification2($message);

            $message = "$email | SMSLORD | is trying to fund and a successful order with orderid $request->trx_ref";
            send_notification($message);


            return back()->with('error', "This Transaction has been successful");
        }


        if ($ck_trx == 4) {

            $email = Auth::user()->email;
            $message = "$email |SMSLORD | is trying to fund and a successful order with orderid $request->trx_ref";
            send_notification2($message);

            $message = "$email | SMSLORD | is trying to fund and a successful order with orderid $request->trx_ref";
            send_notification($message);


            return back()->with('error', "This Transaction has been resolved");
        }


        if ($ck_trx == 1) {
            $session_id = $request->session_id;
            if ($session_id == null) {
                $notify[] = ['error', "session id or amount cant be empty"];
                return back()->withNotify($notify);
            }


            $curl = curl_init();
            $databody = array(
                'session_id' => "$session_id",
                'ref' => "$request->trx_ref"

            );

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://web.sprintpay.online/api/resolve',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $databody,
            ));

            $var = curl_exec($curl);
            curl_close($curl);
            $var = json_decode($var);


            $messager = $var->message ?? null;
            $status = $var->status ?? null;
            $trx = $var->trx ?? null;
            $amount = $var->amount ?? null;

            if ($status == true) {
                User::where('id', Auth::id())->increment('wallet', $var->amount);
                Transaction::where('ref_id', $request->trx_ref)->update(['status' => 2]);


                $user_email = Auth::user()->email;
                $message = "$user_email | $request->trx_ref | $session_id | $var->amount | just resolved deposit | SMSLORD";
                send_notification($message);
                send_notification2($message);


                return redirect('fund-wallet')->with('message', "Transaction successfully Resolved, NGN $amount added to ur wallet");
            }

            if ($status == false) {
                return back()->with('error', "$messager");
            }

            return back()->with('error', "please try again later");
        }
    }


    public function get_smscode(request $request)
    {


        $sms = Verification::where('phone', $request->num)->first()->sms ?? null;
        $order_id = Verification::where('phone', $request->num)->first()->order_id ?? null;
        check_sms($order_id);


        $originalString = 'sms loading...';
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


    public function smspool_webhook(request $request)
    {


        $activationId = $request->orderid;
        $messageId = $request->messageId;
        $service = $request->service;
        $text = $request->text;
        $code = $request->sms;
        $country = $request->country;
        $receivedAt = $request->receivedAt;


        $orders = Verification::where('order_id', $activationId)->update(['sms' => $code, 'status' => 2]);



    }

    public function diasy_webhook(request $request)
    {


        $activationId = $request->activationId;
        $messageId = $request->messageId;
        $service = $request->service;
        $text = $request->text;
        $code = $request->sms;
        $country = $request->country;
        $receivedAt = $request->receivedAt;

        $user_id = Verification::where('order_id', $activationId)->first()->user_id;
        $cost = Verification::where('order_id', $activationId)->first()->cost;
        $orders = Verification::where('order_id', $activationId)->update(['sms' => $code, 'status' => 2]);
        User::where('id', $user_id)->decrement('hold_wallet', $cost);



    }


    public function orders(request $request)
    {
        $orders = Verification::latest()->where('user_id', Auth::id())->get() ?? null;
        return view('orders', compact('orders'));
    }


    public function about_us(request $request)
    {

        return view('about-us');
    }


    public function policy(request $request)
    {

        return view('policy');
    }


    public function delete_order(request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;

        if ($order == null) {
            return redirect('home')->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            Verification::where('id', $request->id)->delete();
            return back()->with('message', "Order has been successfully deleted");
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_order($orderID);

            if ($can_order == 0) {

                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");

            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
            }
        }

        if ($order->status == 1 && $order->type == 2) {



            $orderID = $order->order_id;
            $can_order = cancel_world_order($orderID);

            if ($can_order == 0) {
                return back()->with('error', "Your order cannot be cancelled yet, please try again later");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
            }
        }

        return back()->with('error', 'Order has been deleted or completed');
    }


    public function e_check(request $request)
    {

        $get_user = User::where('email', $request->email)->first() ?? null;

        if ($get_user == null) {

            return response()->json([
                'status' => false,
                'message' => 'No user found, please check email and try again',
            ]);
        }


        return response()->json([
            'status' => true,
            'user' => $get_user->username,
        ]);
    }



    public function unban_users(request $request)
    {

        $total_bought = verification::where('user_id', $request->id)->where('status', 2)->sum('cost');
        $total_funded = Transaction::where('user_id', $request->id)->where('status', 2)->sum('amount');
        $wallet = User::where('id', $request->id)->first()->wallet;

        $ttb = $total_funded - $wallet;


        $ver = new Verification();
        $ver->user_id = $request->id;
        $ver->phone = "CENSORED";
        $ver->order_id = "CENSORED";
        $ver->country = "CENSORED";
        $ver->service = "CENSORED";
        $ver->cost = $ttb;
        $ver->status = 2;
        $ver->type = 3;
        $ver->save();


        Verification::where('user_id', $request->id)->where('type', 2)->delete();
        Verification::where('user_id', $request->id)->where('type', 1)->delete();

        User::where('id', $request->id)->update(['status' => 0]);



        return back()->with('message', 'User Unban');

    }


    public function ban_users(request $request)
    {
        User::where('id', $request->id)->update(['status' => 9]);
        return back()->with('message', 'User Banned');
    }


    public function user_ban(request $request)
    {
        return view('ban');
    }




}
