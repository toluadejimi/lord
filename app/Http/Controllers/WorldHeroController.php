<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesHeroStyleVerification;
use App\Services\AppConfigService;
use App\Support\VerificationLabels;
use App\Services\PricingService;
use App\Services\Sms\HeroCatalogService;
use App\Services\VerificationOrderService;
use Illuminate\Http\Request;

class WorldHeroController extends Controller
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

    public function catalogCountries(Request $request, HeroCatalogService $catalog)
    {
        return $this->heroCatalogCountries($request, $catalog);
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
        return 'hero';
    }

    protected function heroEnabledConfigKey(): string
    {
        return 'provider_hero_enabled';
    }

    protected function heroPricingSettingId(): int
    {
        return 5;
    }

    protected function heroVerificationType(): int
    {
        return 9;
    }

    protected function heroServerLabel(): string
    {
        return VerificationLabels::customerMenuLabelForServer(3);
    }

    protected function heroServerNumber(): int
    {
        return 3;
    }

    protected function heroCatalogRoutePrefix(): string
    {
        return 'world-sv2';
    }

    protected function heroOrderUrl(): string
    {
        return url('order-world-hero');
    }

    protected function heroPollUrl(): string
    {
        return 'get-smscode-hero';
    }
}
