<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Services\VerificationOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationOrderController extends Controller
{
    public function poll(int $id, VerificationOrderService $orders)
    {
        $verification = Verification::where('user_id', Auth::id())->findOrFail($id);

        if ((int) $verification->status === 1) {
            $orders->pollVerificationIfDue($verification, 5);
            $verification->refresh();
        }

        return response()->json([
            'status' => (int) $verification->status,
            'sms' => $verification->sms,
            'message' => $verification->sms ?: 'waiting for sms',
            'next_poll_seconds' => 10,
        ]);
    }

    public function cancel(Request $request, int $id, VerificationOrderService $orders)
    {
        $verification = Verification::where('user_id', Auth::id())->findOrFail($id);
        $result = $orders->cancelAndRefund($verification);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return back()->with($result['success'] ? 'message' : 'error', $result['message']);
    }
}
