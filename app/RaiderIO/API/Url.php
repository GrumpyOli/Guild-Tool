<?php

namespace App\RaiderIO\API;

class Url {

    const BASE_URL = 'https://raider.io/';

    static public function getCharacterInfos( $Region, $Realm, $Name, $Fields = [
        'mythic_plus_weekly_highest_level_runs', 
        'mythic_plus_previous_weekly_highest_level_runs'
        ] ){

            $Region = \strtolower( $Region );
            $Name = \urlencode( $Name );

            return self::BASE_URL . "api/v1/characters/profile?region={$Region}&realm={$Realm}&name={$Name}&fields=" . implode(',',$Fields);

        }
}