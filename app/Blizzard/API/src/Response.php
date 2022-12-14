<?php

namespace App\Blizzard\API\src;

use App\Curl\Src\Response as curlResponse;


class Response extends curlResponse {

    public function hasBlizzardError(){

        if ( $this->getStatus() === 200 ){
            return false;
        }

        if ( isset( $this->getJSON()->code ) ){
            return true;
        }

        return false;
    }


    public function BlizzardError(): ?string{

        if ( $this->hasBlizzardError() ){
            return $this->getJSON()->detail;
        }

        return null;
    }
}
