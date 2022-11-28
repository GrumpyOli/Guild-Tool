<?php

namespace App\Http\Controllers;

use App\Blizzard\API\Helpers;
use App\Blizzard\API\APIRequest;
use App\Blizzard\API\Token;
use App\Blizzard\API\Url;
use App\Models\guild_search_history;
use App\Models\Linked_Character;
use App\Models\wow\Guild;
use App\Models\wow\guild\Rank;
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
            'Realms' => $Realms,
            'SearchHistory' => $SearchHistory
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

           for( $i = 0; $i < 10; $i++ ){
            $ranks[] = [
                'level' => $i,
                'name' => "Rank {$i}",
                'guild_id' => $Guild->id
            ];
           }

           Rank::insert( $ranks );
           
           return redirect()->route('HomePage');
        }

               


        // return redirect()->back()->withInput()->withErrors([
        //     'Guild could not be found'
        // ]);
    }

    function viewRoster( Request $request ){

        $Guild = Guild::session_retrieve()->refresh();
        $Ranks = $Guild->ranks->keyBy('level');

        $trackedCharactersID = $Guild->tracked_characters->pluck('id');
        // dd ( $trackedCharactersID );

        return view('Roster', [
            'Guild' => $Guild,
            'trackedCharactersID' => $trackedCharactersID,
            'Ranks' => $Ranks
        ]);

    }

    function viewLinkedCharacters( Request $request ){

        $Guild = Guild::session_retrieve()->refresh();
        $trackedCharactersID = $Guild->tracked_characters->pluck('id');
        $realms = Realm::all()->where('region', '=', 'US')->sortBy('name');

        ( $trackedCharactersID );

        return view('linked_characters', [
            'Guild' => $Guild,
            'trackedCharactersID' => $trackedCharactersID,
            'realms' => $realms
        ]);

    }

    function addLinkedCharacter( Request $request ){

        $Guild = Guild::session_retrieve();
        
        $validatedData = $request->validate([
            'characterName' => ['required'],
            'realmSlug' => ['required']
        ]);

        [$Status, $Data] = ( new CharacterController )->store( 
            $request, 
            $validatedData['realmSlug'],
            $validatedData['characterName']
        );

        if ( $Status == 'Error' ){
            return redirect()->route('guild.linked_characters')->withErrors( $Data );
        }

        if ( $Data->guild_id == $Guild->id ){
            return redirect()->route('guild.linked_characters')->withErrors(['He is already in your guild']);
        }

        $Link = Linked_Character::updateOrCreate([
            'guild_id' => $Guild->id,
            'character_id' => $Data->id
        ]);

                
        return redirect()->route('guild.linked_characters');
        // Linked_Character::where('name', $validatedData['name'])
        //                 ->where('realm')

    }

    // Page to manage guild settings
    function settings( Request $request ){

        $Guild = Guild::session_retrieve();
        $Ranks = $Guild->ranks->keyBy('level');

        return view('guild_settings', [
            'Ranks' => $Ranks
        ]);

    }

    function update_guild_rank( Request $request ){

        $validatedData = $request->validate([
            'rank0' => ['required'],
            'rank1' => ['required'],
            'rank2' => ['required'],
            'rank3' => ['required'],
            'rank4' => ['required'],
            'rank5' => ['required'],
            'rank6' => ['required'],
            'rank7' => ['required'],
            'rank8' => ['required'],
            'rank9' => ['required'],
        ]);
        
        $Ranks = Guild::session_retrieve()->ranks->keyBy('level') ;

        for( $i = 0; $i < 10; $i++ ){
            $Ranks[ $i ]->name = $validatedData["rank{$i}"];
            $Ranks[ $i ]->save();
        }

        return redirect()->back()->with('status', 'All ranks has been saved !');
    }

}
