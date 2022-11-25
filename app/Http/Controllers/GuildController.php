<?php

namespace App\Http\Controllers;

use App\Blizzard\API\Helpers;
use App\Blizzard\API\APIRequest;
use App\Blizzard\API\Token;
use App\Blizzard\API\Url;
use App\Models\guild_search_history;
use App\Models\wow\Guild;
use App\Models\wow\Realm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuildController extends Controller
{
    // Page that brings the form to select a guild to continu
    function guildSelection( Request $request ){

        $Regions = Helpers::Regions;
        $Realms = Realm::all()->where('region', '=', 'US')->sortBy('name');
        $SearchHistory = guild_search_history::all()->where('account_id', '=', Auth::user()->account->id )->take(5);

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

            // Creating an entry for guild search history
            guild_search_history::updateOrCreate([
                'guild_id' => $Guild->id,
                'account_id' => Auth::user()->account->id
            ]);

            return redirect()->route('HomePage');
         }


        if ( !$Guild ){
           $Url = Url::guildInfos( $validatedData['realmSlug'], Helpers::parseToSlug($validatedData['name']) );
           $APIRequest = new APIRequest( $Url );
           $APIRequest->execute();
           $APIRequestStatusCode = $APIRequest->responses()->first()->getStatus();
   
           if ( $APIRequestStatusCode !== 200 ){
               return redirect()->back()->withErrors( $APIRequest->responses()->first()->BlizzardError() );
           }
   
           $Data = $APIRequest->responses()->first()->getJSON();
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
