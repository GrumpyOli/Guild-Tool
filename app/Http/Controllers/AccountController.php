<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    //

    function summary( Request $request ){
        return view('account');
    }
}
