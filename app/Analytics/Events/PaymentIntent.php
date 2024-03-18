<?php

namespace App\Analytics\Events;

use App\Analytics\GA4EventInterface;

class PaymentIntent implements GA4EventInterface
{
    protected $clientId;
    protected $data;

    public function __construct($clientId, $data = [])
    {
        $this->clientId = $clientId;
        $this->data = $data;
    }

    public function getPayload(): array
    {
        $transactionId = $this->data['id'];
        $amountReceived = $this->data['amount_received'] / 100; // Convert from cents to dollars
        $currency = $this->data['currency'];
        $description = $this->data['description'];
        return [
            "client_id" => $this->clientId,
            "events" => [
                [
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
                ]
            ]
        ];
    }
}
