<?php

namespace App\Http\Controllers;

use App\Blizzard\API\APIRequest;
use App\Blizzard\API\Helpers;
use App\Blizzard\API\Token;
use App\Blizzard\API\Url;
use App\Curl\Curl;
use App\Models\Wow\Character;
use App\Models\wow\Dungeon;
use App\Models\wow\Guild;
use App\RaiderIO\API\Url as RaiderIOURL;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UpdatingDatabaseController extends Controller
{
    // Updating Guild Informations
    function update_guild_informations (Request $request, $Guild_ID ){

        $Guild = Guild::findOrFail($Guild_ID);

        // Getting data from Blizzard
        $Curl = APIRequest::getFirst( 
            Url::guildInfos(
                $Guild->realmSlug, 
                Helpers::parseToSlug($Guild->name) 
            ) 
        );

        // Verifying status code of the request
        if ( $Curl->getStatus() !== 200 ){
            return "Status code for guild infos {$Curl->getStatus()}";
        }

        // Getting data
        $Data = $Curl->getJSON();

        $Guild->id = $Data->id;
        $Guild->name = $Data->name;
        $Guild->faction = $Data->faction->name;
        $Guild->realmSlug = $Data->realm->slug;

        return "Guild infos : {$Guild->save()}";

    }

    // Updating Roster information
    function update_guild_roster( Request $request, $Guild_ID ){

        $Guild = Guild::findOrFail($Guild_ID);
        $Url = Url::guildRoster( $Guild->realm->slug, Helpers::parseToSlug($Guild->name) );
        $Curl = APIRequest::getFirst( $Url );
        $Data = $Curl->getJSON();
        $Members = [];
        $Region =  Token::retrieve()->getRegion();

        if ( $Curl->getStatus() !== 200 ){
            return "Status {$Curl->getStatus()}";
        }
        
        // Ending if roster href is not avaible
        if ( empty( $Data->members ) ){
            return 'There is no roster';
        }
        
        // Wiping Guild Rank
        Character::where('guild_id', $Guild->id )->update(['rank' => Null]);

        foreach( $Data->members as $member ){

            // Skipping level below 60
            if ( $member->character->level < 60 ){
                continue;
            }

            $Members[] = [
                'id' => $member->character->id,
                'name' => $member->character->name,
                'region' => $Region,
                'guild_id' => $Guild->id,
                'rank' => $member->rank,
                'realm_id' => $member->character->realm->id,
                'playable_race_id' => $member->character->playable_race->id,
                'playable_class_id' => $member->character->playable_class->id,
                'level' => $member->character->level
            ];
        }

        // Updating database
        Character::upsert(
            $Members,
            ['id', 'region'],
            ['name', 'guild_id', 'rank', 'realm_id', 'playable_race_id', 'level']
        );
        
        return "Guild roster : " . count($Members) . " added/updated.";

    }

    function update_guild_raider_io( Request $request , $GuildID  ){

        // Setting vars
        $Guild = Guild::findOrFail($GuildID);    
        $Curl = new Curl();
        $fails = [];
        $upsertsData = [];

        // Looping through guild_members
        foreach( $Guild->members as $member ){
            $Curl->addUrl( RaiderIOURL::getCharacterInfos( $member->region, $member->realm->slug, $member->name) );
        }

        foreach( $Guild->tracked_characters as $member) {
            $Curl->addUrl( RaiderIOURL::getCharacterInfos( $member->region, $member->realm->slug, $member->name) );
        }

        // Executing Curl
        $Curl->autoDump()->execute();

        // Looping responses
        foreach( $Curl->responses() as $response ){

            if ( $response->getStatus() !== 200 ){
                $fails[] = $response->getUrl();
                continue;
            }

            $Data = $response->getJSON();
            $Character = Character::findByNameAndRealm( $Data->name, $Data->realm, false );

            // Looking for current week mythic +
            if (isset( $Data->mythic_plus_weekly_highest_level_runs ) && count( $Data->mythic_plus_weekly_highest_level_runs ) > 0 ){

                foreach( $Data->mythic_plus_weekly_highest_level_runs as $Dungeon ){

                    $upsertsData[] = [
                        'character_id' => $Character->id,
                        'dungeon' => $Dungeon->dungeon,
                        'short_name' => $Dungeon->short_name,
                        'mythic_level' => $Dungeon->mythic_level,
                        'completed_at' => Carbon::parse( $Dungeon->completed_at ),
                        'clear_time_ms' => $Dungeon->clear_time_ms,
                        'par_time_ms' => $Dungeon->par_time_ms,
                        'num_keystone_upgrades' => $Dungeon->num_keystone_upgrades,
                        'map_challenge_mode_id' => $Dungeon->map_challenge_mode_id,
                        'zone_id' => $Dungeon->zone_id,
                        'href' => $Dungeon->url,
                        'score' => $Dungeon->score,
                    ];

                }
                
            }

            // Looking for past week mythic +
            if (isset( $Data->mythic_plus_previous_weekly_highest_level_runs ) && count( $Data->mythic_plus_previous_weekly_highest_level_runs ) > 0 ){

                foreach( $Data->mythic_plus_previous_weekly_highest_level_runs as $Dungeon ){

                    $upsertsData[] = [
                        'character_id' => $Character->id,
                        'dungeon' => $Dungeon->dungeon,
                        'short_name' => $Dungeon->short_name,
                        'mythic_level' => $Dungeon->mythic_level,
                        'completed_at' => Carbon::parse( $Dungeon->completed_at ),
                        'clear_time_ms' => $Dungeon->clear_time_ms,
                        'par_time_ms' => $Dungeon->par_time_ms,
                        'num_keystone_upgrades' => $Dungeon->num_keystone_upgrades,
                        'map_challenge_mode_id' => $Dungeon->map_challenge_mode_id,
                        'zone_id' => $Dungeon->zone_id,
                        'href' => $Dungeon->url,
                        'score' => $Dungeon->score,
                    ];

                }
                
            }

            Dungeon::upsert(
                $upsertsData,
                ['character_id', 'clear_time_ms', 'dungeon']
            );
            
        }

        return "RaiderIO : " . count($upsertsData) . " added/updated and " . count($fails) . " errors";
    }
    
}
