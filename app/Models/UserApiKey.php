<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserApiKey extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'stripe_key', 'stripe_secret', 'ga4_measurement_id', 'ga4_api_secret', 'stripe_webhook_secret', 'ga4_measurement_protocol', 'webhook_url','ga4_property_id'];
    protected $casts = [
        'enabled_events' => 'json',
    ];
    public function user()
    {
        return $this->belongsTo(UserApiKey::class);
    }
}
