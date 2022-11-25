<?php

namespace App\Curl\Src;

use App\Curl\Curl;
use App\Curl\Src\Handle;

class Handles extends CurlList {

    /**
     * Create a list of all Curl Handles Object
     * @param Curl $parentCurl A Curl Object
     * @param string $Urls 
     * @return void 
     */
    public function __construct( Curl $parentCurl, string ... $Urls ){
        parent::__construct( $parentCurl );
        $this->add( ... $Urls );
    }

    /**
     * Create a new handle for each urls provided and then attach it to the list
     * @param mixed $Urls 
     * @return void 
     */
    public function add( ... $Urls ): void{
        foreach ( $Urls as $Url ){
            $this->parentCurl->log("Adding URL: {$Url}");
            $this->list[] = new Handle( $Url );
        }
    }

    public function remove( Handle ... $CurlHandles ){
        foreach( $CurlHandles as $CurlHandle ){
            unset( $this->list[array_search($CurlHandle, $this->list, true )]);
        }
    }

    /**
     * Return the list of every effective url used in the cUrl List
     * @return null|array 
     */
    public function getAllUrls(): ?array {
        foreach ( $this->list as $CurlHandle ){
            $Urls[] = $CurlHandle->getInfo(\CURLINFO_EFFECTIVE_URL );
        }

        return $Urls ?? Null;
    }

    /**
     * Function used to log options provided when the setOptions function is called. These options are applied to all the list
     * @param mixed $arrayValue 
     * @param mixed $arrayIndex CURLOPT_ Constants
     * @return void 
     */
    protected function logOptions( $arrayValue, $arrayIndex ): void {
        if ( \is_array( $arrayValue ) ){
            $this->log('Applying options '.Curl::getOptionName($arrayIndex).' ('.$arrayIndex.') => ['.implode(';', $arrayValue).']');
        }else{
            $this->log('Applying options '.Curl::getOptionName($arrayIndex).' ('.$arrayIndex.') => '. $arrayValue  );
        }
    }

    public function setOption( int $cUrlOption, mixed $Value ){

        $this->logOptions( $Value, $cUrlOption );

        foreach ( $this->list as $CurlHandle ){
            $CurlHandle->setOptions( [ $cUrlOption => $Value] );
        }    
    }

    /**
     * Applying an array of optons to the entire list
     * @param null|array $Options [ CURLOPT_**** => VALUE ]
     * @return void 
     */ 
    public function setOptions( ?array $Options ): void {

        if ( count($Options) === 0 ){
            return;
        }

        $this->log('Applying '.count( $Options ).' options values to the list');

        \array_walk( $Options, [ $this, 'logOptions'] );

        foreach ( $this->list as $CurlHandle ){
            $CurlHandle->setOptions( $Options );
        }

    }

}

