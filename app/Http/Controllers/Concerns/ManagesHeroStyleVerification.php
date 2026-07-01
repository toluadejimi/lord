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

    abstract protected function heroCatalogRoutePrefix(): string;

    abstract protected function heroOrderUrl(): string;

    abstract protected function heroPollUrl(): string;

    protected function heroRedirectWhenDisabled(): string
    {
        return 'cworld';
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
            'catalogPrefix' => $this->heroCatalogRoutePrefix(),
            'countriesUrl' => url($this->heroCatalogRoutePrefix().'/catalog/countries'),
            'servicesUrl' => url($this->heroCatalogRoutePrefix().'/catalog/services'),
            'priceUrl' => url($this->heroCatalogRoutePrefix().'/catalog/price'),
            'orderUrl' => $this->heroOrderUrl(),
            'pollUrl' => $this->heroPollUrl(),
            'wallet' => (float) Auth::user()->wallet,
            'verifications' => $verifications,
        ]);
    }

    public function heroCatalogCountries(HeroCatalogService $catalog)
    {
        return response()->json([
            'countries' => $catalog->countries($this->heroProviderKey()),
        ]);
    }

    public function heroCatalogServices(HeroCatalogService $catalog)
    {
        return response()->json([
            'services' => $catalog->services($this->heroProviderKey()),
        ]);
    }

    public function heroCatalogPrice(Request $request, HeroCatalogService $catalog, PricingService $pricing)
    {
        $request->validate([
            'country' => 'required|string',
            'service' => 'required|string',
        ]);

        $quote = $catalog->quote($this->heroProviderKey(), $request->country, $request->service);

        if (!$quote) {
            return response()->json(['message' => 'Number not available for this country and service.'], 404);
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

        $result = $orders->orderHeroStyle(
            Auth::user(),
            $this->heroProviderKey(),
            $request->service,
            $request->country,
            $ngn,
            $apiCost,
            null
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
            return response()->json(['message' => 'waiting for sms']);
        }

        if ((int) $verification->status === 1) {
            $orders->pollVerification($verification);
            $verification->refresh();
        }

        return response()->json(['message' => $verification->sms ?? 'waiting for sms']);
    }
}
