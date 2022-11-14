<?php

namespace App\Http\Controllers;

use App\Blizzard\API\Helpers;
use App\Blizzard\API\APIRequest;
use App\Blizzard\API\Token;
use App\Blizzard\API\Url;
use App\Models\wow\Guild;
use App\Models\wow\Realm;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuildController extends Controller
{
    // Page that brings the form to select a guild to continu
    function guildSelection( Request $request ){

        $Regions = Helpers::Regions;
        $Realms = Realm::all()->where('region', '=', 'US')->sortBy('name');

        return view('guild_selection', [
            'Regions' => $Regions,
            'Realms' => $Realms
        ]);
    }

    // Page that manage information submitted by guildSelect function
    function formSubmitted( Request $request ){

        $validatedData = $request->validate([
            'region' => ['required'],
            'realmSlug' => ['required'],
            'name' => ['required']
        ]);

        $Guild = Guild::firstWhere([
             'realmSlug' => $validatedData['realmSlug'],
             'name' => $validatedData['name']
         ]);
        
         if ( $Guild ){
            Guild::session_register( $Guild );
            return redirect()->route('HomePage');
         }


        if ( !$Guild ){
           $Url = Url::guildInfos( $validatedData['realmSlug'], Helpers::parseToSlug($validatedData['name']) );
           $APIRequest = new APIRequest( $Url );
           $APIRequest->execute();
           $APIRequestStatusCode = $APIRequest->getOne()->getStatus();
   
           if ( $APIRequestStatusCode !== 200 ){
               return redirect()->back()->withErrors($APIRequest->getOne()->BlizzardError() );
           }
   
           $Data = $APIRequest->getOne()->getJSON();
           $Guild = new Guild;
           $Guild->id = $Data->id;
           $Guild->name = $Data->name;
           $Guild->faction = $Data->faction->name;
           $Guild->realmSlug = $Data->realm->slug;
           $Guild->region = Token::retrieve()->getRegion();
           $Guild->save();
   
           Guild::session_register( $Guild );
           
           return redirect()->route('HomePage');
        }

               


        // return redirect()->back()->withInput()->withErrors([
        //     'Guild could not be found'
        // ]);
    }

    function viewRoster( Request $request ){

        return view('Roster');

    }
}
