<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GA4EventService
{
    protected $measurementId;
    protected $apiSecret;

    public function __construct($measurementId, $apiSecret)
    {
        $this->measurementId = $measurementId;
        $this->apiSecret = $apiSecret;
    }

    public function sendEvent($jsonPayload)
    {
        $endpoint = "https://www.google-analytics.com/mp/collect?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}";

        $response = Http::post($endpoint, $jsonPayload);

        info($response);

        return $response->successful();
    }
}
