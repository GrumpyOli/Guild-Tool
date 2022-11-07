<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Blizzard\Connection\Token;
use App\Blizzard\Connection\Oauth;

class LoginController extends Controller
{
    // Function that handle the beginning of the process
    function beginProcess( Request $Request ){

        // user Already logged in, redirect to log out
        if ( $Request->session()->has('activeUser') ){
            
        }

        return view('login', [
            'BlizzardLink' => Oauth::getAuthorizeURL()
        ]);
    }

    // Function that receives the code from blizzard (second step)
    function processingCode( Request $Request ){

        if ( !$Request->query('code') ){
            return redirect()->route('LoginPage')->withErrors('Code is missing');
        }

        $Ouath = new Oauth( Token::auth_authorization_code );
        $Ouath->getAccessToken( $Request->query('code') );

        return redirect()->route('HomePage');

        //dd($Request->query('code'));
    }

    function Logout( Request $Request ){

        $Request->session()->flush();

        return redirect()->route('HomePage');
        
    }
}
