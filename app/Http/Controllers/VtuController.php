<?php

namespace App\Http\Controllers;

use App\Services\AppConfigService;
use App\Services\Payment\SprintPayVasClient;
use App\Services\VtuWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response as HttpResponse;

class VtuController extends Controller
{
    public function __construct(
        protected SprintPayVasClient $vas,
        protected VtuWalletService $vtuWallet,
        protected AppConfigService $config,
    ) {}

    public function index()
    {
        if (!$this->moduleEnabled()) {
            return redirect('/')->with('error', 'Bills & VTU is not available right now.');
        }

        return view('vas.index', [
            'services' => $this->enabledServices(),
            'wallet' => (float) Auth::user()->wallet,
            'vasConfigured' => $this->vas->configured(),
        ]);
    }

    public function airtime()
    {
        if (!$this->moduleEnabled()) {
            return redirect()->route('vas.index')->with('error', 'Bills & VTU is not available.');
        }

        return view('vas.airtime', $this->pageData([
            'networks' => $this->loadAirtimeNetworks(),
        ]));
    }

    public function data()
    {
        if (!$this->moduleEnabled()) {
            return redirect()->route('vas.index')->with('error', 'Bills & VTU is not available.');
        }

        return view('vas.data', $this->pageData([
            'networks' => config('vtu.networks', []),
        ]));
    }

    public function cable()
    {
        if (!$this->moduleEnabled()) {
            return redirect()->route('vas.index')->with('error', 'Bills & VTU is not available.');
        }

        return view('vas.cable', $this->pageData());
    }

    public function electricity()
    {
        if (!$this->moduleEnabled()) {
            return redirect()->route('vas.index')->with('error', 'Bills & VTU is not available.');
        }

        return view('vas.electricity', $this->pageData([
            'discos' => config('vtu.discos', []),
        ]));
    }

    public function catalogDataVariations(Request $request)
    {
        if (!$this->vas->configured()) {
            return response()->json(['message' => 'VTU provider is not configured.'], 503);
        }

        $request->validate(['network' => 'required|string']);

        $response = $this->vas->getPublic('/get-data-variations', [
            'network' => strtolower($request->query('network')),
        ]);

        return response()->json($response->json() ?? [], $response->status());
    }

    public function catalogCablePlans(Request $request)
    {
        if (!$this->vas->configured()) {
            return response()->json(['message' => 'VTU provider is not configured.'], 503);
        }

        $query = [];
        if ($request->filled('service_id')) {
            $query['service_id'] = strtolower($request->query('service_id'));
        }

        $response = $this->vas->getPublic('/cable-plan', $query);

        return response()->json($response->json() ?? [], $response->status());
    }

    public function catalogElectricityVariations(Request $request)
    {
        if (!$this->vas->configured()) {
            return response()->json(['message' => 'VTU provider is not configured.'], 503);
        }

        $request->validate(['serviceID' => 'required|string']);

        $response = $this->vas->getPublic('/get-electricity-variations', [
            'serviceID' => strtolower($request->query('serviceID')),
        ]);

        return response()->json($response->json() ?? [], $response->status());
    }

    public function validateCable(Request $request)
    {
        if (!$this->vas->configured()) {
            return response()->json(['status' => false, 'message' => 'VTU provider is not configured.'], 503);
        }

        $request->validate([
            'service_id' => 'required|string',
            'billersCode' => 'required|string',
        ]);

        $response = $this->vas->getMerchantVas('/merchant/vas/validate-cable', [
            'service_id' => strtolower($request->input('service_id')),
            'billersCode' => preg_replace('/\s+/', '', $request->input('billersCode')),
        ]);

        return response()->json($response->json() ?? ['message' => $this->vas->extractMessage($response)], $response->status());
    }

    public function validateElectricity(Request $request)
    {
        if (!$this->vas->configured()) {
            return response()->json(['status' => false, 'message' => 'VTU provider is not configured.'], 503);
        }

        $request->validate([
            'service_id' => 'required|string',
            'billersCode' => 'required|string',
            'type' => 'required|in:prepaid,postpaid',
        ]);

        $response = $this->vas->getMerchantVas('/merchant/vas/validate-electricity-meter', [
            'service_id' => strtolower($request->input('service_id')),
            'billersCode' => preg_replace('/\s+/', '', $request->input('billersCode')),
            'type' => $request->input('type'),
        ]);

        return response()->json($response->json() ?? ['message' => $this->vas->extractMessage($response)], $response->status());
    }

    public function buyAirtime(Request $request)
    {
        if (!$this->serviceAllowed('airtime')) {
            return back()->with('error', 'Airtime is not available.');
        }

        if (!$this->vas->configured()) {
            return back()->with('error', 'VTU provider is not configured. Contact admin.');
        }

        $request->validate([
            'service_id' => 'required|string',
            'phone' => 'required|regex:/^[0-9]{11}$/',
            'amount' => 'required|numeric|min:'.config('vtu.airtime.min_amount').'|max:'.config('vtu.airtime.max_amount'),
        ]);

        $serviceId = strtolower($request->input('service_id'));
        $amount = (float) $request->input('amount');
        $phone = $request->input('phone');

        if ($serviceId === 'airtel' && $amount > config('vtu.airtime.airtel_max')) {
            return back()->with('error', 'Airtel airtime is limited to ₦'.number_format(config('vtu.airtime.airtel_max')).' per transaction.');
        }

        return $this->executePurchase(
            $amount,
            'AIR-',
            '/merchant/vas/buy-ng-airtime',
            [
                'service_id' => $serviceId,
                'amount' => $amount,
                'phone' => $phone,
            ],
            'airtime',
            'Airtime request completed. If debited, your line should receive the top-up shortly.'
        );
    }

    public function buyData(Request $request)
    {
        if (!$this->serviceAllowed('data')) {
            return back()->with('error', 'Data bundles are not available.');
        }

        if (!$this->vas->configured()) {
            return back()->with('error', 'VTU provider is not configured. Contact admin.');
        }

        $request->validate([
            'service_id' => 'required|string',
            'variation_code' => 'required|string',
            'phone' => 'required|regex:/^[0-9]{11}$/',
            'amount' => 'required|numeric|min:'.config('vtu.data.min_amount').'|max:'.config('vtu.data.max_amount'),
        ]);

        return $this->executePurchase(
            (float) $request->input('amount'),
            'DATA-',
            '/merchant/vas/buy-data',
            [
                'service_id' => strtolower($request->input('service_id')),
                'phone' => $request->input('phone'),
                'variation_code' => $request->input('variation_code'),
                'amount' => (float) $request->input('amount'),
            ],
            'data',
            'Data bundle purchase completed. Your line should receive the bundle shortly.'
        );
    }

    public function buyCable(Request $request)
    {
        if (!$this->serviceAllowed('cable')) {
            return back()->with('error', 'Cable TV is not available.');
        }

        if (!$this->vas->configured()) {
            return back()->with('error', 'VTU provider is not configured. Contact admin.');
        }

        $request->validate([
            'service_id' => 'required|string',
            'billersCode' => 'required|string',
            'variation_code' => 'required|string',
            'amount' => 'required|numeric|min:'.config('vtu.cable.min_amount'),
            'phone' => 'nullable|regex:/^[0-9]{10,11}$/',
        ]);

        $body = [
            'service_id' => strtolower($request->input('service_id')),
            'billersCode' => preg_replace('/\s+/', '', $request->input('billersCode')),
            'variation_code' => $request->input('variation_code'),
            'amount' => (float) $request->input('amount'),
        ];

        if ($request->filled('phone')) {
            $body['phone'] = $request->input('phone');
        }

        return $this->executePurchase(
            (float) $request->input('amount'),
            'TV-',
            '/merchant/vas/buy-cable',
            $body,
            'cable',
            'Cable TV payment completed. Your subscription should be renewed shortly.'
        );
    }

    public function buyElectricity(Request $request)
    {
        if (!$this->serviceAllowed('electricity')) {
            return back()->with('error', 'Electricity is not available.');
        }

        if (!$this->vas->configured()) {
            return back()->with('error', 'VTU provider is not configured. Contact admin.');
        }

        $request->validate([
            'service_id' => 'required|string',
            'billersCode' => 'required|string',
            'variation_code' => 'required|string',
            'amount' => 'required|numeric|min:'.config('vtu.electricity.min_amount').'|max:'.config('vtu.electricity.max_amount'),
            'phone' => 'required|regex:/^[0-9]{10,11}$/',
        ]);

        $amount = (float) $request->input('amount');

        $userId = Auth::id();
        $debit = $this->vtuWallet->tryDebitForVas($userId, $amount);

        if ($debit === null) {
            return back()->with('error', 'Insufficient wallet balance. Please fund your wallet first.');
        }

        $body = [
            'service_id' => strtolower($request->input('service_id')),
            'billersCode' => preg_replace('/\s+/', '', $request->input('billersCode')),
            'variation_code' => $request->input('variation_code'),
            'amount' => $amount,
            'phone' => $request->input('phone'),
        ];

        $response = $this->vas->postMerchantVas('/merchant/vas/buy-electricity', $body);

        if (!$this->vas->responseIndicatesSuccess($response)) {
            $this->logProviderFailure('electricity', '/merchant/vas/buy-electricity', $body, $response);
            $this->vtuWallet->refundVas($userId, $amount);

            return back()->with('error', $this->vas->extractMessage($response));
        }

        $this->vtuWallet->recordVasTransaction(
            $userId,
            $amount,
            $debit['old_balance'],
            $debit['new_balance'],
            'PWR-'
        );

        $token = $this->vas->extractElectricityToken($response);
        $message = $token
            ? 'Electricity purchase successful. Token: '.$token
            : 'Electricity purchase completed. Check your meter or SMS for the token.';

        return back()->with('message', $message);
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function assistantBuyAirtime(int $userId, string $serviceId, string $phone, float $amount): array
    {
        if (!$this->serviceAllowed('airtime') || !$this->vas->configured()) {
            return ['ok' => false, 'message' => 'Airtime is not available right now.'];
        }

        $serviceId = strtolower($serviceId);

        if ($serviceId === 'airtel' && $amount > config('vtu.airtime.airtel_max')) {
            return ['ok' => false, 'message' => 'Airtel max is ₦'.number_format(config('vtu.airtime.airtel_max')).' per transaction.'];
        }

        $debit = $this->vtuWallet->tryDebitForVas($userId, $amount);

        if ($debit === null) {
            return ['ok' => false, 'message' => 'Insufficient wallet balance.'];
        }

        $body = [
            'service_id' => $serviceId,
            'amount' => $amount,
            'phone' => $phone,
        ];

        $response = $this->vas->postMerchantVas('/merchant/vas/buy-ng-airtime', $body);

        if (!$this->vas->responseIndicatesSuccess($response)) {
            $this->logProviderFailure('airtime', '/merchant/vas/buy-ng-airtime', $body, $response);
            $this->vtuWallet->refundVas($userId, $amount);

            return ['ok' => false, 'message' => $this->vas->extractMessage($response)];
        }

        $this->vtuWallet->recordVasTransaction(
            $userId,
            $amount,
            $debit['old_balance'],
            $debit['new_balance'],
            'AIR-'
        );

        return ['ok' => true, 'message' => 'Airtime sent. Your line should receive the top-up shortly.'];
    }

    /**
     * @param  array<string, mixed>  $body
     */
    protected function executePurchase(
        float $amount,
        string $refPrefix,
        string $endpoint,
        array $body,
        string $type,
        string $successMessage,
    ) {
        $userId = Auth::id();
        $debit = $this->vtuWallet->tryDebitForVas($userId, $amount);

        if ($debit === null) {
            return back()->with('error', 'Insufficient wallet balance. Please fund your wallet first.');
        }

        $response = $this->vas->postMerchantVas($endpoint, $body);

        if (!$this->vas->responseIndicatesSuccess($response)) {
            $this->logProviderFailure($type, $endpoint, $body, $response);
            $this->vtuWallet->refundVas($userId, $amount);

            return back()->with('error', $this->vas->extractMessage($response));
        }

        $this->vtuWallet->recordVasTransaction(
            $userId,
            $amount,
            $debit['old_balance'],
            $debit['new_balance'],
            $refPrefix
        );

        return back()->with('message', $successMessage);
    }

    /**
     * @param  array<string, mixed>  $body
     */
    protected function logProviderFailure(string $type, string $endpoint, array $body, HttpResponse $response): void
    {
        $safeBody = $body;
        unset($safeBody['key']);

        Log::warning('SprintPay buy-'.$type.' failed', [
            'endpoint' => $this->vas->baseUrl().$endpoint,
            'request' => $safeBody,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    }

    protected function pageData(array $extra = []): array
    {
        return array_merge([
            'wallet' => (float) Auth::user()->wallet,
            'vasConfigured' => $this->vas->configured(),
        ], $extra);
    }

    protected function moduleEnabled(): bool
    {
        return $this->config->getBool('provider_vtu_enabled', true);
    }

    protected function serviceAllowed(string $slug): bool
    {
        if (!$this->moduleEnabled()) {
            return false;
        }

        $meta = config("platform.admin_vtu_services.{$slug}");

        return $meta && $this->config->getBool($meta['enabled_key'], true);
    }

    protected function enabledServices(): array
    {
        $services = [];

        foreach (config('platform.admin_vtu_services', []) as $slug => $meta) {
            if (!$this->serviceAllowed($slug)) {
                continue;
            }

            $services[] = array_merge($meta, [
                'slug' => $slug,
                'url' => route('vas.'.$slug),
            ]);
        }

        return $services;
    }

  /**
   * @return list<array{id: string, name: string}>
   */
    protected function loadAirtimeNetworks(): array
    {
        if (!$this->vas->configured()) {
            return config('vtu.networks', []);
        }

        $response = $this->vas->getPublic('/get-service');
        $parsed = $this->parseNetworkList($response->json() ?? []);

        return $parsed !== [] ? $parsed : config('vtu.networks', []);
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    protected function parseNetworkList(array $raw): array
    {
        $list = $raw['data'] ?? $raw['services'] ?? $raw['content'] ?? $raw;

        if (isset($list['data']) && is_array($list['data'])) {
            $list = $list['data'];
        }

        if (!is_array($list)) {
            return [];
        }

        $networks = [];

        if (array_is_list($list)) {
            foreach ($list as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $id = $item['service_id'] ?? $item['id'] ?? $item['code'] ?? null;
                $name = $item['name'] ?? $item['title'] ?? null;
                if ($id && $name) {
                    $networks[] = ['id' => strtolower((string) $id), 'name' => (string) $name];
                }
            }
        } else {
            foreach ($list as $id => $name) {
                if (is_array($name)) {
                    $networks[] = [
                        'id' => strtolower((string) ($name['service_id'] ?? $name['id'] ?? $id)),
                        'name' => (string) ($name['name'] ?? $name['title'] ?? $id),
                    ];
                } else {
                    $networks[] = ['id' => strtolower((string) $id), 'name' => (string) $name];
                }
            }
        }

        return $networks;
    }
}
