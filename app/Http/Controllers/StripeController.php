<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\User;
use App\Models\UserApiKey;
use Illuminate\Contracts\Session\Session;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session as FacadesSession;
use Stripe\Charge;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function stripe()
    {
        return view('dashboard');
    }

    public function processPayment(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        try {
            DB::beginTransaction();
            $charge =  Charge::create([
                'amount' => 5000,
                'currency' => 'usd',
                'source' => $request->input('stripeToken'),
                'description' => 'Test Payment',
            ]);
            $now = Carbon::now();
            $oneMonthLater = $now->addMonth();
            $oneMonthLaterDate = $oneMonthLater->toDateString();
            Purchase::create([
                'user_id' => Auth::user()->id,
                'plan_name' => 'Standard Plan',
                'amount' => 50.00,
                'stripe_charge_id' => $charge->id,
                'expiry_date' => $oneMonthLaterDate,
                'status' => 2,
            ]);
            session()->flash('success', 'Payment successful!');
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function redirectToStripe()
    {
        $clientId =  'ca_Pg1hln8seqi6MPda8cIs4socPo1c1slH';
        $redirectUri = urlencode(route('stripe.callback')); // Ensure you name your route in web.php if using route names
        $scope = 'read_write';

        $stripeAuthUrl = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id={$clientId}&scope={$scope}&redirect_uri={$redirectUri}";

        return redirect($stripeAuthUrl);
    }

    public function handleCallback(Request $request)
    {
        $code = $request->code;

        if (!$code) {
            session()->flash('succes', 'The Stripe authorization code was not provided.');
            return redirect()->back();
        }

        $response = Http::asForm()->post('https://connect.stripe.com/oauth/token', [
            'client_secret' => config('services.stripe.secret'),
            'code'          => $code,
            'grant_type'    => 'authorization_code',
        ]);

        info('response' . $response);
        if ($response->successful()) {
            $data = $response->json();
            $this->createWebHook($data);
            session()->flash('success', 'Stripe webhook-url setup successfully');
            return Redirect::to('/dashboard');
        } else {
            session()->flash('succes', 'Failed to exchange authorization code for an access token.');
            return redirect()->back();
        }
    }

    public function createWebHook()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $response =  $stripe->webhookEndpoints->create([
                'enabled_events' => ['charge.succeeded', 'charge.failed', 'payment_intent.succeeded'],
                'url' => 'https://www.applytico.com/stripe/webhook?uuid=' . Auth::user()->id,
            ]);
            info($response);

            DB::beginTransaction();
            $user = UserApiKey::where('user_id', Auth::user()->id)->first();
            if ($user) {
                $user->stripe_webhook_secret = $response['secret'];
                $user->webhook_url = $response['url'];
                $user->enabled_events = $response['enabled_events'];
                $user->webhook_status = $response['status'] == 'enabled' ? true : false;
                $user->webhook_id = $response['id'];
                $user->save();
            } else {
                $user = new UserApiKey();
                $user->user_id = Auth::user()->id;
                $user->stripe_webhook_secret = $response['secret'];
                $user->webhook_url = $response['url'];
                $user->enabled_events = $response['enabled_events'];
                $user->webhook_status = $response['status'] == 'enabled' ? true : false;
                $user->webhook_id = $response['id'];
                $user->save();
            }
            DB::commit();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
