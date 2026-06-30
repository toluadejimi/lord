<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Verification;
use App\Services\AppConfigService;
use App\Services\PricingService;
use App\Services\Sms\UnlimitedPortalProvider;
use App\Services\VerificationOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Usa2Controller extends Controller
{
    public function __construct(
        protected UnlimitedPortalProvider $usa2,
        protected VerificationOrderService $orders,
        protected PricingService $pricing,
        protected AppConfigService $config,
    ) {}

    public function index()
    {
        if (!$this->config->getBool('provider_usa2_enabled')) {
            return redirect('cworld')->with('error', 'Service is not enabled.');
        }

        $services = $this->usa2->listServices();
        $verifications = Verification::where('user_id', Auth::id())->where('type', 4)->latest()->take(20)->get();

        return view('usa2', compact('services', 'verifications'));
    }

    public function order(Request $request)
    {
        $request->validate(['service' => 'required|string']);

        $setting = Setting::find(4);
        $apiCost = (float) ($request->api_cost ?? 1);
        $ngn = $this->pricing->ngnFromUsd($apiCost, 4, Auth::user());
        $ngn = $this->pricing->usaSurcharge($ngn, $request->filled('area_code') || $request->filled('carrier'));

        $result = $this->orders->orderUsa2(Auth::user(), $request->service, $ngn, $apiCost, $request->only(['area_code', 'carrier', 'zip']));

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('message', 'USA number rented: '.$result['verification']->phone);
    }

    public function pollSms(Request $request)
    {
        $verification = Verification::where('user_id', Auth::id())
            ->where('phone', $request->num)
            ->where('type', 4)
            ->first();

        if (!$verification) {
            return response()->json(['message' => 'waiting for sms']);
        }

        if ((int) $verification->status === 1) {
            $this->orders->pollVerification($verification);
            $verification->refresh();
        }

        return response()->json(['message' => $verification->sms ?? 'waiting for sms']);
    }
}
