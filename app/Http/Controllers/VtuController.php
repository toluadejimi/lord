<?php

namespace App\Http\Controllers;

use App\Services\AppConfigService;
use App\Services\Payment\SprintPayVasClient;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VtuController extends Controller
{
    public function __construct(
        protected SprintPayVasClient $vas,
        protected WalletService $wallet,
        protected AppConfigService $config,
    ) {}

    public function index()
    {
        if (!$this->config->getBool('provider_vtu_enabled', true)) {
            return redirect('/')->with('error', 'Bills & VTU is not available right now.');
        }

        return view('vas.index', [
            'services' => $this->enabledServices(),
            'wallet' => (float) Auth::user()->wallet,
            'provider' => 'SprintPay',
        ]);
    }

    public function airtime()
    {
        return $this->serviceView('airtime');
    }

    public function data()
    {
        return $this->serviceView('data', true);
    }

    public function cable()
    {
        return $this->serviceView('cable');
    }

    public function electricity()
    {
        return $this->serviceView('electricity');
    }

    public function purchase(Request $request)
    {
        if (!$this->config->getBool('provider_vtu_enabled', true)) {
            return back()->with('error', 'Service is not enabled.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:50',
            'category_id' => 'required',
            'phone' => 'nullable|string',
            'billersCode' => 'nullable|string',
            'variation_code' => 'nullable|string',
        ]);

        foreach (config('platform.admin_vtu_services', []) as $meta) {
            if ((string) $this->config->get($meta['category_key']) === (string) $request->category_id) {
                if (!$this->vtuAllowed($meta['enabled_key'])) {
                    return back()->with('error', 'This service is not enabled.');
                }
                break;
            }
        }

        $user = Auth::user();
        $amount = (float) $request->amount;

        if ((float) $user->wallet < $amount) {
            return back()->with('error', 'Insufficient wallet balance. Please fund your wallet first.');
        }

        $ref = 'VTU-'.Str::upper(Str::random(12));
        $result = $this->vas->purchase([
            'category_id' => $request->category_id,
            'amount' => $amount,
            'phone' => $request->phone,
            'billersCode' => $request->billersCode,
            'variation_code' => $request->variation_code,
            'ref' => $ref,
        ]);

        if (!($result['status'] ?? false)) {
            return back()->with('error', $result['message'] ?? 'Purchase failed. Please try again.');
        }

        if (!$this->wallet->debit($user, $amount, $ref, 4)) {
            return back()->with('error', 'Wallet debit failed.');
        }

        return redirect()->route('vas.index')->with('message', 'Purchase successful! Reference: '.$ref);
    }

    public function validateBill(Request $request)
    {
        if (!$this->config->getBool('provider_vtu_enabled', true)) {
            return response()->json(['status' => false, 'message' => 'Service is not enabled.']);
        }

        $request->validate([
            'category_id' => 'required',
            'billersCode' => 'required|string',
            'type' => 'nullable|in:cable,electricity',
        ]);

        return response()->json(
            $this->vas->validate(
                $request->input('category_id'),
                $request->input('billersCode'),
                $request->input('type', 'cable')
            )
        );
    }

    protected function serviceView(string $slug, bool $loadVariations = false)
    {
        $meta = config("platform.admin_vtu_services.{$slug}");
        if (!$meta || !$this->vtuAllowed($meta['enabled_key'])) {
            return redirect()->route('vas.index')->with('error', 'This service is not available.');
        }

        $categoryId = $this->config->get($meta['category_key']);
        $variations = [];

        if ($loadVariations && $categoryId) {
            $variations = $this->normalizeVariations($this->vas->variations($categoryId));
        }

        return view('vas.form', [
            'slug' => $slug,
            'title' => $meta['label'],
            'description' => $meta['description'] ?? '',
            'icon' => $meta['icon'] ?? 'ti-receipt',
            'categoryId' => $categoryId,
            'variations' => $variations,
            'wallet' => (float) Auth::user()->wallet,
            'provider' => 'SprintPay',
            'configured' => !empty($categoryId),
        ]);
    }

    protected function enabledServices(): array
    {
        $services = [];

        foreach (config('platform.admin_vtu_services', []) as $slug => $meta) {
            if (!$this->vtuAllowed($meta['enabled_key'])) {
                continue;
            }

            $categoryId = $this->config->get($meta['category_key']);

            $services[] = array_merge($meta, [
                'slug' => $slug,
                'url' => route('vas.'.$slug),
                'configured' => !empty($categoryId),
            ]);
        }

        return $services;
    }

    protected function normalizeVariations(array $raw): array
    {
        $list = $raw['data'] ?? $raw['variations'] ?? $raw['content'] ?? $raw;

        if (!is_array($list)) {
            return [];
        }

        if (isset($list['data']) && is_array($list['data'])) {
            $list = $list['data'];
        }

        return array_values(array_filter($list, fn ($item) => is_array($item)));
    }

    protected function vtuAllowed(string $enabledKey): bool
    {
        return $this->config->getBool('provider_vtu_enabled', true)
            && $this->config->getBool($enabledKey, true);
    }
}
