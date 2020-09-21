<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlanCollection;
use App\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{

    public function index()
    {
        $plan = Plan::get();

        return ( new PlanCollection($plan))
            ->response()
            ->setStatusCode(200);
    }
}
