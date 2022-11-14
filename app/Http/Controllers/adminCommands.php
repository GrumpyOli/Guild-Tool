<?php

namespace App\Http\Controllers;

use App\Blizzard\API\Token;
use Illuminate\Http\Request;
use App\Blizzard\API\APIRequest;
use App\Blizzard\API\Url;
use App\Models\wow\Guild;
use App\Models\wow\Realm;

class adminCommands extends Controller
{
    // Data Page
    public function viewData( Request $request ){

        $Token = Token::retrieve();
        $Guild = Guild::session_retrieve();

        return view('admin.Data',[
                'Token_Region' => $Token->getRegion(),
                'Token_GrantType' => $Token->getGrantType(),
                'Token_Scope' => $Token->getScope(),
                'Token_Number' => $Token->getAccessToken(),
                'Token_Expires' => $Token->getExpiresDateTime(),
                'Langugage' => 'en_US',
                'Guild' => $Guild
        ]);
    }

    public function api_request(Request $request){

        $JSON = Null;

        $Url = Url::guildInfos('zuljin', 'westfall-brewing-company');
        var_dump( $request->input('url') );

        if ( $request->input('url') ){
            $APIRequest = new APIRequest( $request->input('url') );
            $APIRequest->execute();
            $JSON = $APIRequest->getFirst();
        }

        return view('admin/api_request', ['DataObject' => $JSON]);
        
    }

    public function fetch_realm_data(){

        $Url = Url::realmIndex();
        $Request = new APIRequest( $Url );
        $Request->execute();
        $Data = $Request->getFirst();
        $Region = Token::retrieve()->getRegion();

        foreach( $Data->realms as $realm ){
            Realm::updateOrCreate(
                [
                    'id' => $realm->id,
                    'region' => $Region
                ],
                [
                    'name' => $realm->name,
                    'slug' => $realm->slug,
                ]);
        }

        return redirect()->route('admin.data')->with('status', 'Realms updated!');
    }
}
