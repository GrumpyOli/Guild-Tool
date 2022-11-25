<?php

namespace App\Http\Controllers;

use App\Blizzard\API\Token;
use Illuminate\Http\Request;
use App\Blizzard\API\APIRequest;
use App\Blizzard\API\Helpers;
use App\Blizzard\API\Url;
use App\Curl\Curl;
use App\Models\Wow\Character;
use App\Models\wow\Dungeon;
use App\Models\wow\Guild;
use App\Models\wow\Realm;
use App\RaiderIO\API\Url as RaiderIOURL;
use Carbon\Carbon;

class AdminController extends Controller
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

    public function current_guild_update( Request $request ){

        // Retrieving Guild
        $Guild = Guild::session_retrieve();

        // Calling controller for updating guild infos
        var_dump( (new UpdatingDatabaseController )->update_guild_informations( $request, $Guild->id ) );
        var_dump( (new UpdatingDatabaseController )->update_guild_roster( $request, $Guild->id ) );
        var_dump( (new UpdatingDatabaseController )->update_guild_raider_io( $request, $Guild->id ) );

        $Guild->refresh();





    }


    // Custom API Request Page
    public function api_request(Request $request){

        $JSON = Null;

        $Url = $request->input('url') ?? Url::guildInfos('zuljin', 'westfall-brewing-company');

        var_dump( $Url );

        if ( $request->input('url') ){
            $APIRequest = new APIRequest( $request->input('url') );
            $APIRequest->execute();
            $JSON = $APIRequest->getFirst();
        }

        return view('admin/api_request', ['DataObject' => $JSON]);
        
    }

    public function fetch_realm_data(){

        $Data = APIRequest::getFirstJSON( Url::realmIndex() );

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

    public function testingRaiderIOAPI(){


        

    }
}
