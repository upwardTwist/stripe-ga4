<?php

namespace App\Http\Controllers;

use App\Models\Ga4Connect;
use Exception;
use Illuminate\Http\Request;
use Google_Client;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleAnalyticsService;

class GoogleAnalyticsController extends Controller
{

    protected $googleAnalyticsService;

    public function __construct(GoogleAnalyticsService $googleAnalyticsService)
    {
        $this->googleAnalyticsService = $googleAnalyticsService;
    }
    public function redirectToGoogleProvider()
    {
        $client = new Google_Client();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->setScopes('https://www.googleapis.com/auth/analytics.edit');
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

                $this->createDataStream($token['access_token']);
                return redirect()->to('/dashboard');
            }
        } catch (Exception $e) {

            throw $e;
        }
    }

    public function createDataStream($accessToken)
    {
        $accounts = $this->googleAnalyticsService->getAccounts($accessToken);
        if (!$accounts) {
            return redirect()->to('/dashboard')->with('google_message', 'error.');
        } else if (count($accounts) > 1) {
            return redirect()->to('/dashboard')->with('accounts', $accounts);
        } else {
            $this->createPropertyDirectly($accessToken, $accounts[0]);
        }
    }

    public function createProperty(Request $request)
    {
        try {
            $account = $request->accounts[0];
            $user =  Ga4Connect::where('user_id', Auth::user()->id)->first();
            $accessToken = $user->access_token;
            $property = $this->googleAnalyticsService->createProperty($accessToken, 'Stripe-ga4', $account);
            $stream = $this->googleAnalyticsService->createDataStream($property, $accessToken);
            $stream = $this->googleAnalyticsService->getMeasurementProtocolSecret($accessToken, $property, $stream);
            return redirect()->to('/dashboard')->with('success', 'Your Google Analytics keys has been setup sucessfully. YOu can view them in your profile section.');
        } catch (Exception $e) {
            return redirect()->to('/dashboard')->with('error', $e->getMessage());
        }
    }
    public function createPropertyDirectly($accessToken, $account)
    {
        try {
            $user =  Ga4Connect::where('user_id', Auth::user()->id)->first();
            $accessToken = $user->access_token;
            $property = $this->googleAnalyticsService->createProperty($accessToken, 'Stripe-ga4', $account);
            $stream = $this->googleAnalyticsService->createDataStream($property, $accessToken);
            $stream = $this->googleAnalyticsService->getMeasurementProtocolSecret($accessToken, $property, $stream);
            return redirect()->to('/dashboard')->with('success', 'Your Google Analytics keys has been setup sucessfully. YOu can view them in your profile section.');
        } catch (Exception $e) {
            return redirect()->to('/dashboard')->with('error', $e->getMessage());
        }
    }
}
