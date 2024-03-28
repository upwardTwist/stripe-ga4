<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HubspotWebhookController extends Controller
{
    public function handleEvents(Request $request)
    {
        $data = $request->all();
        // Process the data, e.g., log it or store it in your database
        Log::info('Webhook received:', $data);
    
        return response()->json(['message' => 'Success'], 200);
    }
    
}
