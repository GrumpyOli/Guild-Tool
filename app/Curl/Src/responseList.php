<?php

namespace App\Curl\Src;

use App\Curl\Curl;

class responseList extends CurlList {

    public function __construct( Curl $parentCurl, Response ... $Responses ){
        parent::__construct( $parentCurl );
        $this->add( ... $Responses );
    }

    public function add( Response ... $Responses ){
        foreach ( $Responses as $Response ){
            $this->list[] = $Response;
        }
    }

}

