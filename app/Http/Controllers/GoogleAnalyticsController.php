<?php

namespace App\Http\Controllers;

use App\Models\Ga4Connect;
use Exception;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Analytics;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoogleAnalyticsController extends Controller
{
    public function redirectToGoogleProvider()
    {
        $client = new Google_Client();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->setScopes(config('google.scopes'));
        $client->setAccessType(config('google.access_type'));
        $client->setPrompt(config('google.prompt'));

        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    public function handleProviderGoogleCallback(Request $request)
    {


        $user = Auth::user();

        try {

            $client = new Google_Client();
            $client->setClientId(config('google.client_id'));
            $client->setClientSecret(config('google.client_secret'));
            $client->setRedirectUri(config('google.redirect_uri'));
            $token = $client->fetchAccessTokenWithAuthCode($request->code);
            $client->setAccessToken($token);

            if ($token && !empty($token)) {
                Ga4Connect::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'access_token' => $token['access_token'],
                        'refresh_token' => $token['refresh_token'],
                        'scope' => $token['scope'],
                        'expires_in' => $token['expires_in'],

                    ]
                );
                return redirect()->to('/dashboard');
            }
        } catch (Exception $e) {

            throw $e;
        }
    }
}
