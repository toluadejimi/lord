<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Models\Setting;
use App\Models\User;
use App\Models\Verification;
use App\Services\PricingService;
use App\Services\SimWorldCatalogService;
use App\Services\Sms\HeroCatalogService;
use App\Services\Sms\UnlimitedPortalProvider;
use App\Support\VerificationLabels;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait HandlesResellerServers
{
    protected function findUserVerification(User $user, mixed $orderId, ?array $types = null): ?Verification
    {
        $query = Verification::where('id', $orderId)->where('user_id', $user->id);

        if ($types !== null) {
            $query->whereIn('type', $types);
        }

        return $query->first();
    }

    protected function rentResponse(Verification $verification): array
    {
        return [
            'success' => true,
            'server' => VerificationLabels::customerServerLabel((int) $verification->type),
            'order_id' => $verification->id,
            'phone' => $verification->phone,
            'service' => $verification->service,
            'country' => $verification->country,
            'price' => (float) $verification->cost,
            'provider_order_id' => (string) $verification->order_id,
        ];
    }

    protected function smsPollResponse(Verification $verification): array
    {
        return [
            'success' => true,
            'server' => VerificationLabels::customerServerLabel((int) $verification->type),
            'status' => (int) $verification->status,
            'code' => $verification->sms,
            'full_sms' => $verification->full_sms,
            'order_id' => $verification->id,
            'phone' => $verification->phone,
        ];
    }

    public function getOrderSms(Request $request): JsonResponse|array
    {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $request->validate(['order_id' => 'required']);

        $verification = $this->findUserVerification($user, $request->input('order_id'));
        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ((int) $verification->status === 1) {
            $this->orders->pollVerificationIfDue($verification, 10);
            $verification->refresh();
        }

        return response()->json($this->smsPollResponse($verification));
    }

    public function cancelOrderSms(Request $request): JsonResponse|array
    {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $request->validate(['order_id' => 'required']);

        $verification = $this->findUserVerification($user, $request->input('order_id'));
        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $result = $this->orders->cancelAndRefund($verification);

        return response()->json(array_merge([
            'server' => VerificationLabels::customerServerLabel((int) $verification->type),
        ], $result), $result['success'] ? 200 : 422);
    }

    public function server1Countries(Request $request): JsonResponse|array
    {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        if (!$this->config->getBool('provider_sim_enabled', true)) {
            return response()->json(['success' => false, 'message' => 'Server 1 is not enabled.'], 503);
        }

        return response()->json(['success' => true, 'countries' => SimWorldCatalogService::countries()]);
    }

    public function server1Prices(Request $request): JsonResponse|array
    {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $request->validate(['country' => 'required|string']);

        $response = Http::get('https://5sim.net/v1/guest/prices', [
            'country' => $request->input('country'),
        ]);

        if (!$response->successful()) {
            return response()->json(['success' => false, 'message' => 'Could not load prices.'], 502);
        }

        return response()->json(['success' => true, 'country' => $request->input('country'), 'data' => $response->json()]);
    }

    public function server1Price(Request $request, PricingService $pricing): JsonResponse|array
    {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $request->validate([
            'country' => 'required|string',
            'operator' => 'required|string',
            'product' => 'required|string',
            'usd_cost' => 'nullable|numeric|min:0',
        ]);

        $usd = $request->filled('usd_cost')
            ? (float) $request->usd_cost
            : $this->server1UsdFromCatalog($request->country, $request->operator, $request->product);

        if ($usd <= 0) {
            return response()->json(['success' => false, 'message' => 'Price not available.'], 404);
        }

        $setting = Setting::find(3);
        $ngn = round(((float) $setting->rate * $usd) + (float) $setting->margin, 2);

        return response()->json([
            'success' => true,
            'usd' => $usd,
            'price' => $ngn,
            'country' => $request->country,
            'operator' => $request->operator,
            'product' => $request->product,
        ]);
    }

    public function server1Rent(Request $request, PricingService $pricing): JsonResponse|array
    {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $request->validate([
            'country' => 'required|string',
            'operator' => 'required|string',
            'product' => 'required|string',
            'usd_cost' => 'nullable|numeric|min:0',
        ]);

        $usd = $request->filled('usd_cost')
            ? (float) $request->usd_cost
            : $this->server1UsdFromCatalog($request->country, $request->operator, $request->product);

        if ($usd <= 0) {
            return response()->json(['success' => false, 'message' => 'Price not available.'], 422);
        }

        $setting = Setting::find(3);
        $ngn = round(((float) $setting->rate * $usd) + (float) $setting->margin, 2);

        $result = $this->orders->order5sim(
            $user,
            $request->country,
            $request->operator,
            $request->product,
            $ngn,
            $usd,
        );

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($this->rentResponse($result['verification']));
    }

    public function server2Services(Request $request, UnlimitedPortalProvider $usa2): JsonResponse|array
    {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        if (!$this->config->getBool('provider_usa2_enabled')) {
            return response()->json(['success' => false, 'message' => 'Server 2 is not enabled.'], 503);
        }

        return response()->json(['success' => true, 'services' => $usa2->listServices()]);
    }

    public function server2Price(Request $request, PricingService $pricing): JsonResponse|array
    {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $request->validate([
            'service' => 'required|string',
            'usd_cost' => 'nullable|numeric|min:0',
            'area_code' => 'nullable|string',
            'carrier' => 'nullable|string',
        ]);

        $usd = (float) ($request->usd_cost ?? 1);
        $ngn = $pricing->ngnFromUsd($usd, 4, $user);
        $ngn = $pricing->usaSurcharge($ngn, $request->filled('area_code') || $request->filled('carrier'));

        return response()->json([
            'success' => true,
            'service' => $request->service,
            'usd' => $usd,
            'price' => $ngn,
        ]);
    }

    public function server2Rent(Request $request, PricingService $pricing): JsonResponse|array
    {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $request->validate([
            'service' => 'required|string',
            'usd_cost' => 'nullable|numeric|min:0',
            'area_code' => 'nullable|string',
            'carrier' => 'nullable|string',
            'zip' => 'nullable|string',
        ]);

        if (!$this->config->getBool('provider_usa2_enabled')) {
            return response()->json(['success' => false, 'message' => 'Server 2 is not enabled.'], 503);
        }

        $usd = (float) ($request->usd_cost ?? 1);
        $ngn = $pricing->ngnFromUsd($usd, 4, $user);
        $ngn = $pricing->usaSurcharge($ngn, $request->filled('area_code') || $request->filled('carrier'));

        $result = $this->orders->orderUsa2(
            $user,
            $request->service,
            $ngn,
            $usd,
            $request->only(['area_code', 'carrier', 'zip']),
        );

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($this->rentResponse($result['verification']));
    }

    public function server3Countries(Request $request, HeroCatalogService $catalog): JsonResponse|array
    {
        return $this->heroCountriesApi($request, $catalog, 'hero', 'provider_hero_enabled', 'Server 3');
    }

    public function server3Services(Request $request, HeroCatalogService $catalog): JsonResponse|array
    {
        return $this->heroServicesApi($request, $catalog, 'hero', 'provider_hero_enabled', 'Server 3');
    }

    public function server3Price(Request $request, HeroCatalogService $catalog, PricingService $pricing): JsonResponse|array
    {
        return $this->heroPriceApi($request, $catalog, $pricing, 'hero', 'provider_hero_enabled', 'Server 3', 5);
    }

    public function server3Rent(Request $request, HeroCatalogService $catalog, PricingService $pricing): JsonResponse|array
    {
        return $this->heroRentApi($request, $catalog, $pricing, 'hero', 'provider_hero_enabled', 'Server 3', 5);
    }

    public function server4Countries(Request $request, HeroCatalogService $catalog): JsonResponse|array
    {
        return $this->heroCountriesApi($request, $catalog, 'sv3', 'provider_sv3_enabled', 'Server 4');
    }

    public function server4Services(Request $request, HeroCatalogService $catalog): JsonResponse|array
    {
        return $this->heroServicesApi($request, $catalog, 'sv3', 'provider_sv3_enabled', 'Server 4');
    }

    public function server4Price(Request $request, HeroCatalogService $catalog, PricingService $pricing): JsonResponse|array
    {
        return $this->heroPriceApi($request, $catalog, $pricing, 'sv3', 'provider_sv3_enabled', 'Server 4', 6);
    }

    public function server4Rent(Request $request, HeroCatalogService $catalog, PricingService $pricing): JsonResponse|array
    {
        return $this->heroRentApi($request, $catalog, $pricing, 'sv3', 'provider_sv3_enabled', 'Server 4', 6);
    }

    protected function heroCountriesApi(
        Request $request,
        HeroCatalogService $catalog,
        string $providerKey,
        string $enabledKey,
        string $label,
    ): JsonResponse|array {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        if (!$this->config->getBool($enabledKey)) {
            return response()->json(['success' => false, 'message' => $label.' is not enabled.'], 503);
        }

        return response()->json(['success' => true, 'countries' => $catalog->countries($providerKey)]);
    }

    protected function heroServicesApi(
        Request $request,
        HeroCatalogService $catalog,
        string $providerKey,
        string $enabledKey,
        string $label,
    ): JsonResponse|array {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        if (!$this->config->getBool($enabledKey)) {
            return response()->json(['success' => false, 'message' => $label.' is not enabled.'], 503);
        }

        return response()->json(['success' => true, 'services' => $catalog->services($providerKey)]);
    }

    protected function heroPriceApi(
        Request $request,
        HeroCatalogService $catalog,
        PricingService $pricing,
        string $providerKey,
        string $enabledKey,
        string $label,
        int $settingId,
    ): JsonResponse|array {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $request->validate([
            'country' => 'required|string',
            'service' => 'required|string',
        ]);

        if (!$this->config->getBool($enabledKey)) {
            return response()->json(['success' => false, 'message' => $label.' is not enabled.'], 503);
        }

        $quote = $catalog->quote($providerKey, $request->country, $request->service);
        if (!$quote) {
            return response()->json(['success' => false, 'message' => 'Not available for this country and service.'], 404);
        }

        $ngn = $pricing->ngnFromUsd($quote['usd'], $settingId, $user);

        return response()->json([
            'success' => true,
            'country' => $request->country,
            'service' => $request->service,
            'usd' => $quote['usd'],
            'price' => $ngn,
            'available' => $quote['available'] ?? null,
        ]);
    }

    protected function heroRentApi(
        Request $request,
        HeroCatalogService $catalog,
        PricingService $pricing,
        string $providerKey,
        string $enabledKey,
        string $label,
        int $settingId,
    ): JsonResponse|array {
        $user = $this->authUser($request);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $request->validate([
            'country' => 'required|string',
            'service' => 'required|string',
        ]);

        if (!$this->config->getBool($enabledKey)) {
            return response()->json(['success' => false, 'message' => $label.' is not enabled.'], 503);
        }

        $quote = $catalog->quote($providerKey, $request->country, $request->service);
        if (!$quote) {
            return response()->json(['success' => false, 'message' => 'Not available right now.'], 422);
        }

        $apiCost = $quote['usd'];
        $ngn = $pricing->ngnFromUsd($apiCost, $settingId, $user);
        $maxPrice = sprintf('%.4f', $apiCost * 1.02);

        $result = $this->orders->orderHeroStyle(
            $user,
            $providerKey,
            $request->service,
            $request->country,
            $ngn,
            $apiCost,
            $maxPrice,
        );

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($this->rentResponse($result['verification']));
    }

    protected function server1UsdFromCatalog(string $country, string $operator, string $product): float
    {
        $response = Http::get('https://5sim.net/v1/guest/prices', ['country' => $country]);
        if (!$response->successful()) {
            return 0;
        }

        $data = $response->json();
        if (!is_array($data)) {
            return 0;
        }

        $countryBlock = $data[$country] ?? $data;
        $productBlock = $countryBlock[$product] ?? null;
        if (!is_array($productBlock)) {
            return 0;
        }

        $operatorBlock = $productBlock[$operator] ?? null;
        if (!is_array($operatorBlock)) {
            return 0;
        }

        return (float) ($operatorBlock['cost'] ?? 0);
    }
}
