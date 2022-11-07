<?php

namespace App\Http\Controllers;

use App\Blizzard\Connection\Token;
use Illuminate\Http\Request;

class DevAndAdminPagesController extends Controller
{
    // Data Page
    public function Data( Request $request ){

        $Token = Token::retrieve();

        return view('Data',[
                'Token_Region' => $Token->getRegion(),
                'Token_GrantType' => $Token->getGrantType(),
                'Token_Scope' => $Token->getScope(),
                'Token_Number' => $Token->getAccessToken(),
                'Token_Expires' => $Token->getExpiresDateTime(),
                'Langugage' => 'en_US'
        ]);
    }
}
