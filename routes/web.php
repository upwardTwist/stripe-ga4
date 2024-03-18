<?php

use App\Http\Controllers\GoogleAnalyticsController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\UserApiKeyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\UserApiKey;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth2/authorize', function (Request $request) {
    return view('welcome');
});

Route::get('/dashboard', [PlansController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
// Route::get('/oauth2/authorize', [ProfileController::class, 'verify']);

Route::get('/payment', 'StripeController@stripe')->name('payment.form');
// Route::post('/process-payment', 'StripeController@processPayment')->name('process.payment');
Route::post('/process-payment', [StripeController::class, 'processPayment'])->name('process.payment')->middleware(['auth', 'verified']);

Route::get('/payment/success', function () {
    return view('payment-success');
})->name('payment.success');

Route::get('/payment/failure', function () {
    return view('payment-failure');
})->name('payment.failure');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleInvoicePaymentSucceeded']);
// Route::post('/webhook/stripe', 'StripeWebhookController@handleWebhook')->name('stripe.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::middleware('auth')->group(function () {
    Route::get('stripe', [StripeController::class, 'stripe']);
    Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');
    Route::get('/settings', [UserApiKeyController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [UserApiKeyController::class, 'update'])->name('settings.update');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/key', [UserApiKeyController::class, 'update'])->name('keys.update');

    Route::get('/google/login', [GoogleAnalyticsController::class, 'redirectToGoogleProvider']);
    Route::get('/google/callback', [GoogleAnalyticsController::class, 'handleProviderGoogleCallback']);
});

require __DIR__ . '/auth.php';
