<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        // If you maintain per-tenant plans, fetch them by tenant('id') here.
        $plans = config('plans');

        return view('plans.index', [
            'plans'   => $plans,
            'tenant'  => tenant('id'), // pass the slug for route() helpers
        ]);
    }
}
