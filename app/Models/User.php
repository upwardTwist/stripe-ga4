<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function keys()
    {
        return $this->hasMany(UserApiKey::class);
    }

    public function g4Connections()
    {
        return $this->hasMany(Ga4Connect::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function hasActivePlan()
    {
        return $this->purchases()->where('expiry_date', '>', Carbon::now())->exists();
    }

    public function hasEnabledWebhookUrl()
    {
        return $this->keys()->where('webhook_status', true)->exists();
    }

    public function hasEnabledG4Keys()
    {
        return $this->keys()->where('ga4_measurement_id', '!=', null)->Where('ga4_api_secret', '!=', null)->exists();
    }
    public function hasGoogleAccessToken()
    {
        return $this->g4Connections()->where('access_token', '!=', null)->exists();
    }
}
