<?php

namespace App\Services;

use Daikazu\GA4EventTracking\Facades\GA4;
use \Stripe\StripeClient;

class StripeEventService
{
    protected $ga4ClientId;
    protected $stripeClient;

    public function __construct($stripeSecretKey, $ga4ClientId)
    {
        $this->stripeClient = new StripeClient($stripeSecretKey);
        $this->ga4ClientId = $ga4ClientId;
        GA4::setClientId($this->ga4ClientId);
    }

    public function handlePaymentIntentSucceeded($eventData)
    {
        $transactionId = $eventData['id'];
        $amountReceived = $eventData['amount_received'] / 100; // Convert from cents to dollars
        $currency = $eventData['currency'];
        $description = $eventData['description'] ?? 'No description';

        $ga4Event = [
            'name' => 'purchase',
            'params' => [
                'transaction_id' => $transactionId,
                'value' => $amountReceived,
                'currency' => $currency,
                'items' => [
                    [
                        'item_id' => 'PlaceholderID', // Modify as needed
                        'item_name' => $description, // Or use a placeholder if description isn't suitable
                        'quantity' => 1, // Adjust based on your actual data or logic
                    ],
                ],
            ],
        ];

        $res =   GA4::enableDebugging()->sendEvent($ga4Event);
        info($res);
        // Log or handle the response as needed
    }
}
