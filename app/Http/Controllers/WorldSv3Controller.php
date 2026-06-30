<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Services\AppConfigService;
use App\Services\PricingService;
use App\Services\Sms\HeroHandlerProvider;
use App\Services\VerificationOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorldSv3Controller extends Controller
{
    public function __construct(
        protected HeroHandlerProvider $hero,
        protected VerificationOrderService $orders,
        protected PricingService $pricing,
        protected AppConfigService $config,
    ) {}

    public function index()
    {
        if (!$this->config->getBool('provider_sv3_enabled')) {
            return redirect('cworld')->with('error', 'Service is not enabled.');
        }

        $verifications = Verification::where('user_id', Auth::id())->where('type', 10)->latest()->take(20)->get();

        return view('world-sv3', compact('verifications'));
    }

    public function order(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
            'country' => 'nullable|string',
            'api_cost' => 'nullable|numeric',
            'max_price' => 'nullable',
        ]);

        $apiCost = (float) ($request->api_cost ?? 1);
        $ngn = $this->pricing->ngnFromUsd($apiCost, 6, Auth::user());

        $result = $this->orders->orderHeroStyle(
            Auth::user(),
            'sv3',
            $request->service,
            $request->country,
            $ngn,
            $apiCost,
            $request->max_price
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('message', 'Number rented: '.$result['verification']->phone);
    }

    public function pollSms(Request $request)
    {
        $verification = Verification::where('user_id', Auth::id())
            ->where('phone', $request->num)
            ->where('type', 10)
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
