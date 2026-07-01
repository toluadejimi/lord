<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesHeroStyleVerification;
use App\Services\AppConfigService;
use App\Services\PricingService;
use App\Services\Sms\HeroCatalogService;
use App\Services\VerificationOrderService;
use Illuminate\Http\Request;

class WorldSv3Controller extends Controller
{
    use ManagesHeroStyleVerification;

    public function index(AppConfigService $config)
    {
        return $this->heroIndex($config);
    }

    public function order(
        Request $request,
        VerificationOrderService $orders,
        HeroCatalogService $catalog,
        PricingService $pricing,
    ) {
        return $this->heroOrder($request, $orders, $catalog, $pricing);
    }

    public function pollSms(Request $request, VerificationOrderService $orders)
    {
        return $this->heroPollSms($request, $orders);
    }

    public function catalogCountries(HeroCatalogService $catalog)
    {
        return $this->heroCatalogCountries($catalog);
    }

    public function catalogServices(HeroCatalogService $catalog)
    {
        return $this->heroCatalogServices($catalog);
    }

    public function catalogPrice(Request $request, HeroCatalogService $catalog, PricingService $pricing)
    {
        return $this->heroCatalogPrice($request, $catalog, $pricing);
    }

    protected function heroProviderKey(): string
    {
        return 'sv3';
    }

    protected function heroEnabledConfigKey(): string
    {
        return 'provider_sv3_enabled';
    }

    protected function heroPricingSettingId(): int
    {
        return 6;
    }

    protected function heroVerificationType(): int
    {
        return 10;
    }

    protected function heroServerLabel(): string
    {
        return 'Server 4';
    }

    protected function heroCatalogRoutePrefix(): string
    {
        return 'world-sv3';
    }

    protected function heroOrderUrl(): string
    {
        return url('order-world-sv3');
    }

    protected function heroPollUrl(): string
    {
        return 'get-smscode-sv3';
    }
}
