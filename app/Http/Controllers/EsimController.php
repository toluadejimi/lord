<?php

namespace App\Http\Controllers;

use App\Services\Esim\PikaSimClient;
use App\Services\EsimOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EsimController extends Controller
{
    public function __construct(
        protected EsimOrderService $orders,
        protected PikaSimClient $client,
    ) {}

    public function index(Request $request)
    {
        if (!$this->orders->moduleEnabled()) {
            return redirect('/')->with('error', 'Esim is not available right now.');
        }

        $query = [];
        if ($request->filled('country')) {
            $query['country'] = strtoupper(trim((string) $request->input('country')));
        }
        if ($request->filled('type') && in_array($request->input('type'), ['data', 'phone', 'all'], true)) {
            $query['type'] = $request->input('type');
        }
        if ($request->filled('page')) {
            $query['page'] = max(1, (int) $request->input('page'));
        }

        $result = $this->orders->packagesForDisplay($query);

        return view('esim.index', [
            'wallet' => (float) Auth::user()->wallet,
            'packages' => $result['packages'],
            'pagination' => $result['pagination'] ?? [],
            'loadError' => $result['error'] ?? null,
            'configured' => $this->client->configured(),
            'filters' => [
                'country' => $query['country'] ?? '',
                'type' => $query['type'] ?? 'data',
            ],
        ]);
    }

    public function orders()
    {
        if (!$this->orders->moduleEnabled()) {
            return redirect('/')->with('error', 'Esim is not available right now.');
        }

        $orders = Auth::user()->esimOrders()
            ->latest()
            ->limit(50)
            ->get();

        return view('esim.orders', [
            'wallet' => (float) Auth::user()->wallet,
            'orders' => $orders,
            'configured' => $this->client->configured(),
        ]);
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'package_code' => 'required|string|max:100',
            'price_ngn' => 'required|numeric|min:1',
        ]);

        $result = $this->orders->purchase(
            Auth::user(),
            $request->input('package_code'),
            (float) $request->input('price_ngn'),
        );

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return redirect()
            ->route('esim.orders')
            ->with($result['success'] ? 'message' : 'error', $result['message'] ?? 'Request failed.');
    }
}
