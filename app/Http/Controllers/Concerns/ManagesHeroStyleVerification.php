<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Verification;
use App\Services\AppConfigService;
use App\Services\PricingService;
use App\Services\Sms\HeroCatalogService;
use App\Services\VerificationOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait ManagesHeroStyleVerification
{
    abstract protected function heroProviderKey(): string;

    abstract protected function heroEnabledConfigKey(): string;

    abstract protected function heroPricingSettingId(): int;

    abstract protected function heroVerificationType(): int;

    abstract protected function heroServerLabel(): string;

    abstract protected function heroServerNumber(): int;

    abstract protected function heroCatalogRoutePrefix(): string;

    abstract protected function heroOrderUrl(): string;

    abstract protected function heroPollUrl(): string;

    protected function heroRedirectWhenDisabled(): string
    {
        return 'cworld';
    }

    protected function heroPickerFlow(): string
    {
        return 'country-first';
    }

    protected function heroServerTitle(): string
    {
        return 'International SMS Verification';
    }

    protected function heroServerSubtitle(): string
    {
        return $this->heroPickerFlow() === 'service-first'
            ? 'Pick a service, choose a country, confirm price, then rent a number.'
            : 'Search a country, pick a service, confirm price, then rent a number.';
    }

    public function heroIndex(
        AppConfigService $config,
    ) {
        if (!$config->getBool($this->heroEnabledConfigKey())) {
            return redirect($this->heroRedirectWhenDisabled())->with('error', 'This server is not available.');
        }

        $verifications = Verification::where('user_id', Auth::id())
            ->where('type', $this->heroVerificationType())
            ->latest()
            ->take(20)
            ->get();

        return view('verification.hero-server', [
            'serverLabel' => $this->heroServerLabel(),
            'serverNum' => $this->heroServerNumber(),
            'serverTheme' => $this->heroServerNumber(),
            'serverTitle' => $this->heroServerTitle(),
            'serverSubtitle' => $this->heroServerSubtitle(),
            'catalogPrefix' => $this->heroCatalogRoutePrefix(),
            'pickerFlow' => $this->heroPickerFlow(),
            'countriesUrl' => url($this->heroCatalogRoutePrefix().'/catalog/countries'),
            'servicesUrl' => url($this->heroCatalogRoutePrefix().'/catalog/services'),
            'priceUrl' => url($this->heroCatalogRoutePrefix().'/catalog/price'),
            'orderUrl' => $this->heroOrderUrl(),
            'pollUrl' => $this->heroPollUrl(),
            'wallet' => (float) Auth::user()->wallet,
            'verifications' => $verifications,
        ]);
    }

    public function heroCatalogCountries(Request $request, HeroCatalogService $catalog)
    {
        return response()->json([
            'countries' => $catalog->countries($this->heroProviderKey()),
        ]);
    }

    public function heroCatalogServices(HeroCatalogService $catalog)
    {
        $services = $catalog->services($this->heroProviderKey());
        $payload = ['services' => $services];

        if ($services === [] && $this->heroProviderKey() === 'sv3') {
            $payload['error'] = 'Could not load services. Check Server 4 API key in Admin → SMS Services.';
        }

        return response()->json($payload);
    }

    public function heroCatalogPrice(Request $request, HeroCatalogService $catalog, PricingService $pricing)
    {
        $request->validate([
            'country' => 'required|string',
            'service' => 'required|string',
        ]);

        $quote = $catalog->quote($this->heroProviderKey(), $request->country, $request->service);

        if (!$quote) {
            $message = 'Number not available for this country and service.';
            if ($this->heroProviderKey() === 'sv3') {
                $apiKey = app(\App\Services\AppConfigService::class)->get('SMS_SERVER_WORLD_SV3_API_KEY', '');
                if ($apiKey === '') {
                    $message = 'This server is not available right now. Please contact support.';
                }
            }

            return response()->json(['message' => $message], 404);
        }

        $ngn = $pricing->ngnFromUsd(
            $quote['usd'],
            $this->heroPricingSettingId(),
            Auth::user()
        );

        return response()->json([
            'usd' => $quote['usd'],
            'ngn' => $ngn,
            'available' => $quote['available'],
        ]);
    }

    public function heroOrder(
        Request $request,
        VerificationOrderService $orders,
        HeroCatalogService $catalog,
        PricingService $pricing,
    ) {
        $request->validate([
            'service' => 'required|string',
            'country' => 'required|string',
        ]);

        $quote = $catalog->quote($this->heroProviderKey(), $request->country, $request->service);

        if (!$quote) {
            return back()->with('error', 'This combination is not available right now.');
        }

        $apiCost = $quote['usd'];
        $ngn = $pricing->ngnFromUsd($apiCost, $this->heroPricingSettingId(), Auth::user());

        $maxPrice = sprintf('%.4f', $apiCost * 1.02);

        $result = $orders->orderHeroStyle(
            Auth::user(),
            $this->heroProviderKey(),
            $request->service,
            $request->country,
            $ngn,
            $apiCost,
            $maxPrice
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('message', 'Number rented: '.$result['verification']->phone);
    }

    public function heroPollSms(Request $request, VerificationOrderService $orders)
    {
        $verification = Verification::where('user_id', Auth::id())
            ->where('phone', $request->num)
            ->where('type', $this->heroVerificationType())
            ->first();

        if (!$verification) {
            return response()->json([
                'message' => 'waiting for sms',
                'status' => 0,
                'next_poll_seconds' => 12,
            ]);
        }

        if ((int) $verification->status === 1) {
            $orders->pollVerificationIfDue($verification, 10);
            $verification->refresh();
        }

        return response()->json([
            'message' => $verification->sms ?? 'waiting for sms',
            'status' => (int) $verification->status,
            'next_poll_seconds' => 10,
        ]);
    }
}
