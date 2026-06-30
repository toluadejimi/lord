<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Verification;
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

    public function airtime()
    {
        if (!$this->vtuAllowed('vtu_airtime_enabled')) {
            return redirect('/')->with('error', 'Service is not enabled.');
        }

        return view('vas.form', ['title' => 'Airtime', 'categoryId' => $this->config->get('VTU_CAT_AIRTIME')]);
    }

    public function data()
    {
        if (!$this->vtuAllowed('vtu_data_enabled')) {
            return redirect('/')->with('error', 'Service is not enabled.');
        }

        $categoryId = $this->config->get('VTU_CAT_DATA');
        $variations = $categoryId ? $this->vas->variations($categoryId) : [];

        return view('vas.form', ['title' => 'Data', 'categoryId' => $categoryId, 'variations' => $variations]);
    }

    public function cable()
    {
        if (!$this->vtuAllowed('vtu_cable_enabled')) {
            return redirect('/')->with('error', 'Service is not enabled.');
        }

        return view('vas.form', ['title' => 'Cable TV', 'categoryId' => $this->config->get('VTU_CAT_CABLE_TV')]);
    }

    public function electricity()
    {
        if (!$this->vtuAllowed('vtu_electricity_enabled')) {
            return redirect('/')->with('error', 'Service is not enabled.');
        }

        return view('vas.form', ['title' => 'Electricity', 'categoryId' => $this->config->get('VTU_CAT_ELECTRICITY')]);
    }

    public function purchase(Request $request)
    {
        if (!$this->config->getBool('provider_vtu_enabled', true)) {
            return back()->with('error', 'Service is not enabled.');
        }
        $request->validate([
            'amount' => 'required|numeric|min:50',
            'category_id' => 'required',
            'phone' => 'nullable',
            'billersCode' => 'nullable',
            'variation_code' => 'nullable',
        ]);

        foreach (config('platform.admin_vtu_services', []) as $meta) {
            if ((string) $this->config->get($meta['category_key']) === (string) $request->category_id) {
                if (!$this->vtuAllowed($meta['enabled_key'])) {
                    return back()->with('error', 'Service is not enabled.');
                }
                break;
            }
        }

        $user = Auth::user();
        $amount = (float) $request->amount;

        if ((float) $user->wallet < $amount) {
            return back()->with('error', 'Insufficient wallet balance.');
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
            return back()->with('error', $result['message'] ?? 'VTU purchase failed.');
        }

        if (!$this->wallet->debit($user, $amount, $ref, 4)) {
            return back()->with('error', 'Wallet debit failed.');
        }

        return back()->with('message', 'VTU purchase successful.');
    }

    public function validateBill(Request $request)
    {
        if (!$this->config->getBool('provider_vtu_enabled', true)) {
            return response()->json(['status' => false, 'message' => 'Service is not enabled.']);
        }

        $categoryId = $request->input('category_id');
        $type = $request->input('type', 'cable');

        return response()->json(
            $this->vas->validate($categoryId, $request->input('billersCode'), $type)
        );
    }

    protected function vtuAllowed(string $enabledKey): bool
    {
        return $this->config->getBool('provider_vtu_enabled', true)
            && $this->config->getBool($enabledKey, true);
    }
}
