<?php

namespace App\Http\Controllers;

use App\Services\TelegramPremium\IStarClient;
use App\Services\TelegramPremiumOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramBlueTickController extends Controller
{
    public function __construct(
        protected TelegramPremiumOrderService $orders,
        protected IStarClient $istar,
    ) {}

    public function index()
    {
        if (!$this->orders->moduleEnabled()) {
            return redirect('/')->with('error', 'Telegram Blue Tick is not available right now.');
        }

        $packages = [];
        $loadError = null;

        try {
            if ($this->istar->configured()) {
                $packages = $this->orders->packagesForDisplay();
            } else {
                $loadError = 'Service is being configured. Please check back soon.';
            }
        } catch (\Throwable $e) {
            $loadError = 'Could not load packages. Please try again later.';
        }

        return view('telegram-blue-tick.index', [
            'wallet' => (float) Auth::user()->wallet,
            'packages' => $packages,
            'loadError' => $loadError,
            'configured' => $this->istar->configured(),
        ]);
    }

    public function orders()
    {
        if (!$this->orders->moduleEnabled()) {
            return redirect('/')->with('error', 'Telegram Blue Tick is not available right now.');
        }

        $orders = Auth::user()->telegramPremiumOrders()
            ->latest()
            ->limit(50)
            ->get();

        return view('telegram-blue-tick.orders', [
            'wallet' => (float) Auth::user()->wallet,
            'orders' => $orders,
            'configured' => $this->istar->configured(),
        ]);
    }

    public function searchRecipient(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:64',
            'months' => 'required|integer|in:3,6,12',
        ]);

        $result = $this->orders->lookupRecipient(
            Auth::user(),
            $request->input('username'),
            (int) $request->input('months'),
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:64',
            'recipient_hash' => 'required|string|max:128',
            'recipient_name' => 'nullable|string|max:128',
            'months' => 'required|integer|in:3,6,12',
            'price_ngn' => 'required|numeric|min:1',
        ]);

        $result = $this->orders->purchase(
            Auth::user(),
            $request->input('username'),
            $request->input('recipient_hash'),
            (int) $request->input('months'),
            (float) $request->input('price_ngn'),
            $request->input('recipient_name'),
        );

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return redirect()
            ->route('telegram-blue-tick.orders')
            ->with($result['success'] ? 'message' : 'error', $result['message'] ?? 'Request failed.');
    }
}
