<?php

namespace App\Http\Controllers;

use App\Analytics\Events\LoginEvent;
use App\Analytics\Events\PaymentIntent;
use App\Models\UserApiKey;
use App\Services\GA4EventService;
use App\Services\StripeEventService;
use Daikazu\GA4EventTracking\Facades\GA4;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class StripeWebhookController extends CashierController
{
    public function handleInvoicePaymentSucceeded(Request $payload)
    {
        $id = $payload->userId;
        $key = UserApiKey::where('user_id', $id)->first();

        $measurementId = $key->ga4_measurement_id;
        $apiSecret = $key->ga4_api_secret;
        $ga4Service = new GA4EventService($measurementId, $apiSecret);
        $clientId = 'phgCQrHnQb-DMaTMYksjDg' . $key->user_id;

        $stripe = new \Stripe\StripeClient($key->stripe_secret);
        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = $key->stripe_webhook_secret;

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }
        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $data =  new PaymentIntent($clientId, $event->data->object);
                $jsonPayload = $data->getPayload();
                $success = $ga4Service->sendEvent($jsonPayload);
            // case 'login':
            //     $data =  new LoginEvent($clientId);
            //     $jsonPayload = $data->getPayload();
            //     $success = $ga4Service->sendEvent($jsonPayload);
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        http_response_code(200);
    }


}
