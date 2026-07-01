<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ApiDocsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $justGenerated = false;

        if (!$user->api_key) {
            $user->api_key = Str::random(48);
            $user->save();
            $justGenerated = true;
        }

        $user = $user->fresh();

        return view('api-docs', [
            'user' => $user,
            'maskedKey' => self::maskKey($user->api_key),
            'baseUrl' => rtrim(url('/api/v1'), '/'),
            'showFullKey' => $justGenerated || session('show_new_key'),
            'fullKey' => ($justGenerated || session('show_new_key')) ? $user->api_key : null,
        ]);
    }

    public function revealKey(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $user = Auth::user();
        $limitKey = 'api-docs-reveal:'.$user->id;

        if (RateLimiter::tooManyAttempts($limitKey, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many attempts. Try again later.',
            ], 429);
        }

        if (!Hash::check($request->password, $user->password)) {
            RateLimiter::hit($limitKey, 900);

            return response()->json(['success' => false, 'message' => 'Incorrect password.'], 403);
        }

        RateLimiter::clear($limitKey);

        return response()->json([
            'success' => true,
            'api_key' => $user->api_key,
        ]);
    }

    public function updateWebhook(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        if ($request->filled('webhook_url')) {
            $request->validate([
                'webhook_url' => 'url|max:500|starts_with:https://',
            ]);
        }

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Incorrect password. Webhook was not saved.');
        }

        $user->update(['webhook_url' => $request->webhook_url ?: null]);

        return back()->with('message', $request->webhook_url
            ? 'Webhook URL saved. Outbound deliveries are signed with your API key.'
            : 'Webhook URL removed.');
    }

    public function regenerateKey(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Incorrect password. API key was not changed.');
        }

        $user->api_key = Str::random(48);
        $user->save();

        return back()
            ->with('message', 'API key regenerated. Update your integrations immediately — the old key no longer works.')
            ->with('show_new_key', true);
    }

    public static function maskKey(?string $key): string
    {
        if (!$key || strlen($key) < 12) {
            return '••••••••••••••••';
        }

        return substr($key, 0, 8).str_repeat('•', max(20, strlen($key) - 12)).substr($key, -4);
    }
}
