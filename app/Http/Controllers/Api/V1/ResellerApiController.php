<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Verification;
use App\Services\AppConfigService;
use App\Services\PricingService;
use App\Services\Sms\HeroHandlerProvider;
use App\Services\Sms\SmsPoolProvider;
use App\Services\VerificationOrderService;
use Illuminate\Http\Request;

class ResellerApiController extends Controller
{
    public function __construct(
        protected AppConfigService $config,
        protected SmsPoolProvider $smsPool,
        protected HeroHandlerProvider $heroHandler,
        protected PricingService $pricing,
        protected VerificationOrderService $orders,
    ) {}

    protected function userFromApiKey(Request $request): ?User
    {
        $apiKey = $request->input('api_key') ?? $request->query('api_key');
        if (!$apiKey) {
            return null;
        }

        return User::where('api_key', $apiKey)->where('status', '!=', 9)->first();
    }

    protected function authUser(Request $request)
    {
        $user = $this->userFromApiKey($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid API key.'], 401);
        }

        return $user;
    }

    public function balance(Request $request)
    {
        $user = $this->authUser($request);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        return response()->json(['success' => true, 'balance' => (float) $user->wallet]);
    }

    public function getWorldCountries(Request $request)
    {
        $user = $this->authUser($request);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        return response()->json(['success' => true, 'data' => $this->smsPool->countries()]);
    }

    public function getWorldServices(Request $request)
    {
        $user = $this->authUser($request);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        return response()->json(['success' => true, 'data' => $this->smsPool->services()]);
    }

    public function checkWorldAvailability(Request $request)
    {
        $user = $this->authUser($request);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $country = $request->input('country');
        $service = $request->input('service');
        $price = $this->smsPool->price($country, $service);
        $usd = (float) ($price->price ?? $price->high_price ?? 0);
        $ngn = $this->pricing->ngnFromUsd($usd, 2, $user, true);

        return response()->json([
            'success' => true,
            'usd' => $usd,
            'price' => $ngn,
            'stock' => $price->stock ?? null,
        ]);
    }

    public function rentWorldNumber(Request $request)
    {
        $user = $this->authUser($request);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $country = $request->input('country');
        $service = $request->input('service');
        $priceData = $this->smsPool->price($country, $service);
        $usd = (float) ($priceData->price ?? $priceData->high_price ?? 0);
        $ngn = $this->pricing->ngnFromUsd($usd, 2, $user, true);

        $result = $this->orders->orderSmsPool($user, $country, $service, $ngn, $usd);
        if (!$result['success']) {
            return response()->json($result, 422);
        }

        $v = $result['verification'];

        return response()->json([
            'success' => true,
            'order_id' => $v->id,
            'phone' => $v->phone,
            'provider_order_id' => $v->order_id,
        ]);
    }

    public function getWorldSms(Request $request)
    {
        $user = $this->authUser($request);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $verification = Verification::where('id', $request->input('order_id'))
            ->where('user_id', $user->id)->first();

        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ((int) $verification->status === 1) {
            $this->orders->pollVerification($verification);
            $verification->refresh();
        }

        return response()->json([
            'success' => true,
            'status' => $verification->status,
            'code' => $verification->sms,
            'full_sms' => $verification->full_sms,
        ]);
    }

    public function cancelWorldSms(Request $request)
    {
        $user = $this->authUser($request);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $verification = Verification::where('id', $request->input('order_id'))
            ->where('user_id', $user->id)->first();

        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $result = $this->orders->cancelAndRefund($verification);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function usaServicesRetired()
    {
        return response()->json(['success' => false, 'message' => 'USA Server 1 is retired.'], 410);
    }

    public function rentUsaRetired()
    {
        return response()->json(['success' => false, 'message' => 'USA Server 1 is retired.'], 410);
    }

    public function getUsaSms(Request $request)
    {
        $user = $this->authUser($request);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $verification = Verification::where('id', $request->input('order_id'))
            ->where('user_id', $user->id)->where('type', 4)->first();

        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ((int) $verification->status === 1) {
            $this->orders->pollVerification($verification);
            $verification->refresh();
        }

        return response()->json([
            'success' => true,
            'status' => $verification->status,
            'code' => $verification->sms,
        ]);
    }

    public function cancelUsaSms(Request $request)
    {
        $user = $this->authUser($request);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $verification = Verification::where('id', $request->input('order_id'))
            ->where('user_id', $user->id)->where('type', 4)->first();

        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $result = $this->orders->cancelAndRefund($verification);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
