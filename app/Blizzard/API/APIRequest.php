<?php

namespace App\Blizzard\API;

use App\Blizzard\API\src\Response;
use App\Curl\Curl;
use App\Blizzard\API\Token;


/**
 * RequestV2 Extends the Curl object
 * @package Bnet
 */
class APIRequest extends Curl {

    // Default locale to be retrieved
    const defaultLocale = 'en_US';

    // Token
    protected Token $Token;
    
    public function __construct( string ... $Urls ){

        // Applying the locale in the url
        $this->log('Applying all locale to url received');
        $Urls = array_map( [$this, 'setLocalInUrl'], $Urls);

        // Calling parent constructor
        parent::__construct( ... $Urls );

        // Getting the token
        $this->log('Getting and storing Bnet Token');
        if ( Token::retrieve() ){
            $this->log('Token found and store to the cUrl Object.');
            $this->setToken( Token::retrieve() );
        }else{
            $this->log('Token not found');
        }

    }

    /**
     * Assign a token to the object. By default, the object will get for the first token registered in the session
     * @param Token $Token 
     * @return void 
     */
    public function setToken( Token $Token ){
        $this->Token = $Token;
    }

    public function isTokenValid(): bool {
        if ( isset($this->Token) && $this->Token->isExpired() == false ){
            return true;
        }else{
            return false;
        }
    }

    public function execute(){

        if ( !$this->isTokenValid() ){
            $this->log('Cannot execute request because the token is invalid');
        }
        
        // Setting the token to all curl headers
        $this->log('Applying the token to all headers');
        $this->handleList->setOption(CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->Token->getAccessToken() ] );

        // Continue with the Curl Object
        parent::execute();
    }

    protected function addResponse( \CurlHandle $CurlHandle, string $rawResponse ){
        $this->responseList->add( new Response( $CurlHandle, $rawResponse ) );
    }



// Standalone function that can be call statically

    /**
     * It is a shortcut function to execute a request and return the object already executed.
     * @param string|array $request Url(s) to be sent to blizzard
     * @param bool $throwException If true, it will throws Exception on every error
     * @return Request 
     * @throws Exception 
     * @throws UnauthorizedAccessException 
     */
    static function quickExecute ( string ... $Urls ): Request {
        $Object = new self(... $Urls);
        $Object->execute();
        return $Object;
    }

    /**
     * Function that format a url to add locale if it is missing
     * @param string $url Blizzard api's Battle.net
     * @return string Parsed URL
     */
    static public function setLocalInUrl( string $url, string $locale = 'default' ){

        // Adding the url to the current URL if it is not already set
        if ( strpos($url, 'locale') === false ){

            if ( $locale = 'default'){
                // Setting default locale
                $locale = self::defaultLocale;
            }

            // Adding the locale variable to the request
            if ( \strpos($url, '?') === false ){
                $url .= '?locale=' . $locale;
            }else{
                $url .= '&locale=' . $locale;
            }
        }
        return $url;
    }    
}
