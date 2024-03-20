<?php

return [
    'client_id' => env('GOOGLE_CLIENT_ID'), // Your Google Cloud OAuth 2.0 Client ID
    'client_secret' => env('GOOGLE_CLIENT_SECRET'), // Your Google Cloud Client Secret
    'redirect_uri' => env('GOOGLE_REDIRECT_URI'), // Your OAuth redirect URI
    'scopes' => [\Google_Service_Analytics::ANALYTICS_READONLY],
    'access_type' => 'offline',
    'prompt' => 'select_account consent',
];
