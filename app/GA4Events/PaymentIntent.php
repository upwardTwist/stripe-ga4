<?php

namespace App\Analytics\Events;

use App\Analytics\GA4EventInterface;

class LoginEvent implements GA4EventInterface
{
    protected $clientId;
    protected $method;

    public function __construct($clientId, $method = 'Google')
    {
        $this->clientId = $clientId;
        $this->method = $method;
    }

    public function getPayload(): array
    {
        return [
            "client_id" => $this->clientId,
            "events" => [[
                "name" => "login",
                "params" => [
                    "method" => $this->method
                ]
            ]]
        ];
    }
}
