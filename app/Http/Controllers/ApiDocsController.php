<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiDocsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->api_key) {
            $user->api_key = Str::random(48);
            $user->save();
        }

        return view('api-docs', ['user' => $user->fresh()]);
    }

    public function updateWebhook(Request $request)
    {
        $request->validate(['webhook_url' => 'nullable|url|max:500']);

        Auth::user()->update(['webhook_url' => $request->webhook_url]);

        return back()->with('message', 'Webhook URL saved.');
    }

    public function regenerateKey()
    {
        $user = Auth::user();
        $user->api_key = Str::random(48);
        $user->save();

        return back()->with('message', 'API key regenerated.');
    }
}
