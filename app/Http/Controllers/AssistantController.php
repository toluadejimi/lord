<?php

namespace App\Http\Controllers;

use App\Services\VerificationOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssistantController extends Controller
{
    public function command(Request $request, VerificationOrderService $orders)
    {
        $text = strtolower(trim($request->input('text', '')));
        $user = Auth::user();

        if (str_contains($text, 'balance')) {
            return response()->json(['reply' => 'Your wallet balance is NGN '.number_format((float) $user->wallet, 2)]);
        }

        if (str_contains($text, 'contact') || str_contains($text, 'support')) {
            return response()->json(['reply' => 'Contact support via the FAQ page or admin Telegram.']);
        }

        if (str_contains($text, 'vtu') && str_contains($text, 'airtime')) {
            return response()->json(['reply' => 'Open /vas/airtime to buy airtime with your wallet.']);
        }

        if (str_contains($text, 'order usa')) {
            return response()->json(['reply' => 'Use /usa2 for USA Server 2 numbers. USA Server 1 is retired.']);
        }

        if (str_contains($text, 'order world')) {
            return response()->json(['reply' => 'Use /world for SMSPool, /world-sv2 for Hero, or /world-sv3 for SMS Bower.']);
        }

        return response()->json(['reply' => 'Try: balance, order usa whatsapp, order world telegram, vtu airtime, contact support']);
    }
}
