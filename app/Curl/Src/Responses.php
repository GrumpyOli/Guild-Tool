<?php

namespace App\Curl\Src;

use App\Curl\Curl;

class Responses extends CurlList {

    public function __construct( Curl $parentCurl, Response ... $Responses ){
        parent::__construct( $parentCurl );
        $this->add( ... $Responses );
    }

    public function add( Response ... $Responses ){
        foreach ( $Responses as $Response ){
            $this->list[] = $Response;
        }
    }

    /**
     * Return the first item in the list. If JSON is set to true, it will return data
     * @param bool $DataAsJSON 
     * @return mixed 
     */
    public function first( ){
        return $this->list[0] ?? Null;
    }

    /**
     * Return all items
     * @return $this 
     */
    public function all( $DataAsJSON = true ){

        // JSON is true
        if ( $DataAsJSON ){
            $Data = [];
            foreach( $this as $response ){
                $Data[] = $response->getJSON();
            }
            return $Data;
        }

        // JSON is false
        return $this;
    }

}

