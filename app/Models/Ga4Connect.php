<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ga4Connect extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'access_token', 'refresh_token', 'scope', 'expires_in'];

}
