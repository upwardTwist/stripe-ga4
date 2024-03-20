<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserApiKey;
use Laravel\Cashier\Cashier;

class SetStripeConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $userApiKey = UserApiKey::where('user_id', Auth::id())->first();
            
            if ($userApiKey) {
                // Set Stripe API keys for Cashier dynamically
                Cashier::stripe()->setApiKey($userApiKey->stripe_secret);
            }
        }

        return $next($request);
    }
}
