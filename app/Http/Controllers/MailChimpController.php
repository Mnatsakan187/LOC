<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Newsletter;



class MailChimpController extends Controller
{
    public function store(Request $request)
    {
        if ( ! Newsletter::isSubscribed($request->user_email) ) {
            Newsletter::subscribe($request->user_email);
        }
    }
}
