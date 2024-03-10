<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Contracts\Session\Session;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
            $request->session()->flash('success', 'Payment successful!');
            return redirect()->route('payment.success');
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect()->route('payment.failure');
        }
    }
}
