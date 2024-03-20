<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlansController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        return view('dashboard', compact('plans'));
    }

    
}
