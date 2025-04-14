<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        return view('tenants.dashboard');
    }
}
