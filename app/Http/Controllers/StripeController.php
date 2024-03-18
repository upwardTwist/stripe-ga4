<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Contracts\Session\Session;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            return redirect()->route('payment.failure');
        }
    }
}
