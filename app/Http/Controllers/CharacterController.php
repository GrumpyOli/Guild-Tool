<?php

namespace App\Http\Controllers;

use App\Blizzard\API\APIRequest;
use App\Blizzard\API\Helpers;
use App\Blizzard\API\Token;
use App\Blizzard\API\Url;
use App\Models\Wow\Character;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    // Store a Character
    function store( Request $request, $realmSlug, $characterName ){

        $Curl = APIRequest::getFirst( 
            Url::character( 
                Helpers::parseToSlug($realmSlug), 
                Helpers::parseToSlug($characterName)
            ) );

        $Data = $Curl->getJSON();
            
        switch ( $Curl->getStatus() ){
            case 200 :

                if ( $Data->level < 60 ){
                    return ['Error', 'The characted is not level 60'];
                }
        
                
                $Character = Character::updateOrCreate(
                    [
                        'id' => $Data->id
                    ],
                    [
                        'name' => $Data->name,
                        'region' => Token::retrieve()->getRegion(),
                        'guild_id' => $Data->guild->id,
                        'realm_id' => $Data->realm->id,
                        'playable_race_id' => $Data->race->id,
                        'playable_class_id' => $Data->character_class->id,
                        'level' => $Data->level
                    ]
                );

                return ['OK', $Character];

            case 404 :
                return ['Error', 'Character not found'];

        }

        
    }
}
