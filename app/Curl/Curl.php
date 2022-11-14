<?php

namespace App\Curl;

use App\Curl\Src\Handle;
use App\Curl\Src\handleList;
use App\Curl\Src\responseList;
use App\Curl\Src\Response;
use stdClass;

/**
 * Curl object that can handle multiple request or a single one
 * 
 * Functions : 
 *  __construct( ... Urls )
 *  addUrl( ... Urls )
 * 
 *  setOptions()
 * 
 * 
 * Exemple:
 * 
 * $Curl = new Curl( 'http://www.google.ca', 'http://www.microsoft.com' )
 * $Curl->setOptions( CURLOPT_HEADER, ['Header Setting'] )
 * $Curl->execute()
 * 
 * Retrieving data
 * $Curl->responseList->getOne()
 * 
 * Dumping Data
 * $Curl->dump();
 * 
 * @package Application
 */
class Curl {

    protected bool $throwException = true;
    protected \CurlMultiHandle $CurlMultiHandle;
    protected handleList $handleList;
    protected responseList $responseList;
    protected array $defaultCurlOptions = [
        \CURLOPT_RETURNTRANSFER => 1,
        \CURLOPT_SSL_VERIFYPEER => 0,
        \CURLOPT_CONNECTTIMEOUT => 5,
        \CURLOPT_TIMEOUT => 30                  
    ];
    protected array $curlOptions = [];
    protected array $Log = [];

    // Some settings ..
    protected int $timeout = 5;
    protected bool $dumpToFile = true;

    protected string $logFolder = '\curl\logs';
    protected string $logPath = '';


    /**
     * Creating a new Curl Object to handle Curl Request
     * @param string[] $Urls List of URL to be executed
     * @return void 
     */
    function __construct( string ... $Urls ){

        $this->log('Initializing a new Curl Object');

        // Adjusting the log folder
        $this->logPath = storage_path() . $this->logFolder;
        $this->log("Saving the log path under {$this->logPath}");
        $this->log('Initializing handleList');
        $this->handleList = new handleList($this, ... $Urls);
        $this->log('Initializing responseList');
        $this->responseList = new responseList( $this );
        $this->log('Initializing CurlMultiHandle');
        $this->CurlMultiHandle = curl_multi_init();
    }

    function __get( $keyName ){
        switch ( $keyName ){
            case 'throwException':
            case 'defaultCurlOptions':
            case 'handleList':
            case 'responseList':
            case 'timeout':
            case 'dumpToFile':
            case 'fileFolder':
                return $this->{$keyName};
            default :
                throw new \Exception('Invalid key name for this object');    
        }
    }

    /**
     * Add new Curl Handle inside the object
     * @param string[] $Urls 
     * @return void 
     */
    public function addUrl( string ... $Urls ): void {
        foreach( $Urls as $Url ){
            $this->log("Adding {$Url}");
        }
        $this->handleList->add( ... $Urls );
    }

    /**
     * Execute all cUrl requests
     * @return $this 
     * @throws Exception 
     */
    public function execute(){

        $this->log("Starting the execution of {$this->handleList->count()} requests");
        $this->log("Auto dump response to file is set to: {$this->dumpToFile}");
        $this->log("Timeout is set to: {$this->timeout}");
        $this->log("Watch for options value with ** because they could be another option");

        // Applying default options to all Curl Request
        $this->handleList->setOptions( $this->defaultCurlOptions );
        $this->handleList->setOptions( $this->curlOptions );

        if ( count( $this->handleList ) < 1 ){
            $this->log('There is no queries to be executed');
        }

        // Handling single request
        if ( $this->isSingleRequest() === true ){
            $CurlHandle = $this->handleList->item(0);
            $this->handleResponse( $CurlHandle );
        }

        // Handling multiple request
        if ( $this->isMultipleRequest() === true ){

            // Adding each request to the multi handler
            $this->log('Adding every curl request to the CurlMultiHandler');
            foreach( $this->handleList as $CurlHandle){
                \curl_multi_add_handle( $this->CurlMultiHandle, $CurlHandle->getHandle() );
            }
            
            $this->log('Starting the execution ..');
            // Execute the multiple queries
            do {
                // Getting the Status Code from cUrl, constant CURLM_OK should be equal to this
                $status = \curl_multi_exec($this->CurlMultiHandle, $active);
                    if ($active) {
                    \curl_multi_select($this->CurlMultiHandle, $this->timeout);
                }
            } while ($active && $status === CURLM_OK);

            // Check for errors
            if ($status !== CURLM_OK) {
                // Display error message
                $this->log("ERROR! " . curl_multi_strerror($status) );
                $this->dump();
            }else{
                //Handling each responses
                $this->log('Execution finished');
                foreach( $this->handleList as $CurlHandle ){
                    $this->handleResponse( $CurlHandle );
                }       
            }
        }

        if ( $this->dumpToFile === true ){
            $this->dump();
        }

        return $this;
    }

    /**
     * @deprecated
     * @param int $Options 
     * @param mixed $Value 
     * @return void 
     */
    public function setOptions( int $Options, mixed $Value){

        // Applying Options
        $this->curlOptions[ $Options ] = $Value;

        // Logging section
        if ( is_array( $Value ) ){
            $Value = '[' . implode(';', $Value) . ']';
        }

        $this->log("Adding curl options {".self::getOptionName($Options)."} {$Options} to value {$Value}");
    }

    public function getOne(): Response {
        return $this->responseList->getOne();
    }

    public function getAll(): array {
        return $this->responseList->getAll();
    }

    public function getFirst(): ?stdClass {
        return $this->responseList->getOne()->getJSON();
    }

    protected function handleResponse( Handle $CurlHandle ){

        // Extracting result
        if ( $this->isSingleRequest() === true ){ 
            $rawResponse = \curl_exec( $CurlHandle->getHandle() );
        }elseif ( $this->isMultipleRequest() === true ){
            $rawResponse = \curl_multi_getcontent( $CurlHandle->getHandle() );
        }

        // Logging the response status code
        $this->log('Response '.$CurlHandle->getInfo( \CURLINFO_HTTP_CODE ).' received for url: ' . $CurlHandle->getURL() );

        \curl_multi_info_read( $this->CurlMultiHandle, $queued_messages );

        // Adding the response to the list
        $this->addResponse( $CurlHandle->getHandle(), $rawResponse );

        // Removing the request
    //    $this->handleList->remove( $CurlHandle );


    }

    protected function addResponse( \CurlHandle $CurlHandle, string $rawResponse ){
        $this->responseList->add( new Response( $CurlHandle, $rawResponse ) );
    }

    public function log( string $message, ... $sprintfOptions){
        $this->Log[] = $message;
    }

    protected function isSingleRequest(): bool {
        return (bool) ( count( $this->handleList ) === 1 );
    }

    protected function isMultipleRequest(): bool {
        return (count( $this->handleList ) > 1 ) ? true : false ;
    }

    public function dump(): void {

        $fileName = $this->logPath . \DIRECTORY_SEPARATOR . 'Dump ' . time() . '.json';
        $DateTime = new \DateTime();

        $Dump = [
            'Time' => $DateTime->format( \DateTime::RFC850 ),
            'Log' => $this->Log
        ];

        foreach( $this->responseList as $Response ){
            $Dump['Response'][] = $Response->Dump();
        }

        file_put_contents( $fileName, json_encode($Dump, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES) );

    }

    /**
     * Returns the name of the CURLOPT integer name. Watch this function, there is some OPT that use the same INT
     * @param int $CurlOptInt 
     * @return int|string|null 
     */
    static public function getOptionName( int $CurlOptInt ){
        
        static $cUrlConstants;
        static $knownDouble = [10009, 10026, 10063, 50, 48, 119, 10026, 10102];

        if ( !$cUrlConstants ){

            $cUrlConstants = get_defined_constants(true)['curl'];

            foreach( $cUrlConstants as $Key => $Value ){
                if ( \strpos($Key, 'CURLOPT') === False ){
                    unset( $cUrlConstants[$Key]);
                }
            }

            $cUrlConstants = \array_flip( $cUrlConstants );
        //    sort( $cUrlConstants );

            foreach( $knownDouble as $Key ){
                if ( isset($cUrlConstants[$Key]) ){
                    $cUrlConstants[$Key].= ' **';
                }
            }

        }

        return $cUrlConstants[ $CurlOptInt ] ?? Null ;

    }

    /**
     * Developpement purpose
     * @return void 
     */
    public function deleteLogs(){
        // loop through the files one by one
        foreach(glob($this->logPath . '/*') as $file){
            // check if is a file and not sub-directory
            if(is_file($file)){
                // delete file
                unlink($file);
            }
        }

    }

}