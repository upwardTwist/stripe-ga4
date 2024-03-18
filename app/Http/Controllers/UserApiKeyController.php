<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserApiKey;
use Illuminate\Support\Facades\Auth;

class UserApiKeyController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $userApiKey = UserApiKey::firstOrCreate(['user_id' => $user->id]);

        return view('settings.edit', compact('userApiKey'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        UserApiKey::where('user_id', $user->id)->first();
        $request->validate([
            'stripe_key' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'ga4_measurement_id' => 'required|string',
            'ga4_api_secret' => 'required|string',
            'stripe_webhook_secret' => 'nullable|string',
            'ga4_measurement_protocol' => 'nullable|string',
        ]);
        UserApiKey::updateOrCreate(
            ['user_id' => $user->id],
            [
                'stripe_key' => $request->stripe_key,
                'stripe_secret' => $request->stripe_secret,
                'ga4_measurement_id' => $request->ga4_measurement_id,
                'ga4_api_secret' => $request->ga4_api_secret,
                'stripe_webhook_secret' => $request->stripe_webhook_secret,
                'ga4_measurement_protocol' => $request->ga4_measurement_protocol,
                'webhook_url' => url('/') . '/stripe/webhook?uuid=' . $user->id,
            ]
        );

        return redirect()->back()->with('success', 'API Keys updated successfully.');
    }
}
