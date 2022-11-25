<?php

namespace App\Curl;

use App\Curl\Src\Handle;
use App\Curl\Src\Handles;
use App\Curl\Src\Responses;
use App\Curl\Src\Response;
use CurlMultiHandle;
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

    //--- Settings

    /**
     * Setting if the object has to throw exception or go silent
     * @var bool
     */
    protected bool $throwException = true;

    /**
     * Default timeout applied to Curl Object
     * @var int
     */
    protected int $timeout = 5;

    /**
     * If enabled, every cUrl request will be dump into a file
     * @var bool
     */
    protected bool $dumpToFile = false;

    /**
     * Default log folder (where the file will be dump)
     * @var string
     */
    protected string $logFolder = '\curl\logs';

    /**
     * To be determined
     * @var string
     */
    protected string $logPath = '';


    //--- Object private object and handler

    /**
     * Curl Multi Handler
     * @var CurlMultiHandle
     */
    protected \CurlMultiHandle $CurlMultiHandle;

    /**
     * Returns a collection of handles
     * @var handleList
     */
    protected Handles $handles;

    /**
     * Returns a collection of responses (after being executed)
     * @var Responses
     */
    protected Responses $responses;

    /**
     * Contains default curl options that will be applied to every curl request
     * @var array
     */
    protected array $defaultCurlOptions = [
        \CURLOPT_RETURNTRANSFER => 1,
        \CURLOPT_SSL_VERIFYPEER => 0,
        \CURLOPT_CONNECTTIMEOUT => 5,
        \CURLOPT_TIMEOUT => 30                  
    ];

    /**
     * Contains Curl Options to be applied AFTER the default options
     * @var array
     */
    protected array $curlOptions = [];

    /**
     * Contains all log
     * @var array
     */
    protected array $Log = [];
    
    /**
     * Timer for object (for logging purpose)
     * @var float
     */
    protected float $startingTime;

    /**
     * Curl Object will dump if it detects an error
     * @var bool
     */
    protected bool $dumpIfError = false;

    /**
     * Store the last response (called statically) into the object
     * @var mixed
     */
    static protected $lastRequest;


    //--- Class Start

    /**
     * Creating a new Curl Object to handle Curl Request
     * @param string[] $Urls List of URL to be executed
     * @return void 
     */
    function __construct( string ... $Urls ){

        $this->log('Initializing a new Curl Object');

        $this->dumpToFile = env('CURL_DUMP_TO_FILE', false);

        // Adjusting the log folder
        $this->logPath = storage_path() . $this->logFolder;

        $this->log("Dump to file value is {$this->dumpToFile}");
        $this->log("Saving the log path under {$this->logPath}");
        $this->log('Initializing handles object');
        $this->handles = new Handles($this, ... $Urls);
        $this->log('Initializing responses object');
        $this->responses = new Responses( $this );
        $this->log('Initializing CurlMultiHandle');
        $this->CurlMultiHandle = curl_multi_init();
    }

    /**
     * Return all responses
     * @param string $statusCode All | 200 | 404 | 500 ..
     * @return responseList 
     */
    public function responses( $statusCode = 'All' ){
        return $this->responses;
    }

    /**
     * Return all handles
     * @return handleList 
     */
    public function handles(){
        return $this->handles;
    }

    /**
     * Enable the option to dump if it detects an error
     * @param bool $bool True|False
     * @return $this 
     */
    public function dumpIfError( $bool = true){
        $this->dumpIfError = $bool;
        return $this;
    }

    /**
     * Adding url to the object
     * @param string[] $Urls 
     * @return $this 
     */
    public function addUrl( string ... $Urls ) {

        foreach( $Urls as $Url )
            $this->log("Adding {$Url}");
        
        $this->handles->add( ... $Urls );

        return $this;

    }

    /**
     * Execute all cUrl requests
     * @return $this 
     * @throws Exception 
     */
    public function execute(){

        $this->log("Starting the execution of {$this->handles->count()} requests");
        $this->log("Auto dump response to file is set to: {$this->dumpToFile}");
        $this->log("Timeout is set to: {$this->timeout}");
        $this->log("Watch for options value with ** because they could be another option");

        // Applying default options to all Curl Request
        $this->handles->setOptions( $this->defaultCurlOptions );
        $this->handles->setOptions( $this->curlOptions );

        if ( count( $this->handles ) < 1 ){
            $this->log('There is no queries to be executed');
        }

        // Handling single request
        if ( $this->isSingleRequest() === true ){
            $CurlHandle = $this->handles->item(0);
            $this->handleResponse( $CurlHandle );
        }

        // Handling multiple request
        if ( $this->isMultipleRequest() === true ){

            // Adding each request to the multi handler
            $this->log('Adding every curl request to the CurlMultiHandler');
            foreach( $this->handles as $CurlHandle){
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
                foreach( $this->handles as $CurlHandle ){
                    $this->handleResponse( $CurlHandle );
                }       
            }
        }

        if ( $this->dumpToFile === true ){
            $this->dumpToFile();
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
        $this->responses->add( new Response( $CurlHandle, $rawResponse ) );
    }

    public function log( string $message, ... $sprintfOptions){
        $this->Log[] = $message;
    }

    protected function isSingleRequest(): bool {
        return (bool) ( count( $this->handles ) === 1 );
    }

    protected function isMultipleRequest(): bool {
        return (count( $this->handles ) > 1 ) ? true : false ;
    }

    public function dumpToFile(): void {

        $fileName = $this->logPath . \DIRECTORY_SEPARATOR . 'Dump ' . time() . '.json';
        $DateTime = new \DateTime();

        $Dump = [
            'Time' => $DateTime->format( \DateTime::RFC850 ),
            'Log' => $this->Log
        ];

        foreach( $this->responses as $Response ){
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

    public function autoDump( bool $bool = true ){
        
        $this->dumpToFile = (bool) $bool;

        return $this;
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


    //--- Static Functions

    /**
     * Execute and get the first response
     * @param string $Url Url to query
     * @return mixed 
     */
    static public function getFirst( string $Url ): Response{

        $Object = new self( $Url );
        $Object->dumpIfError();
        $Object->execute();

        return $Object->responses()->first();

    }

    /**
     * Execute and return a JSON decoded object
     * @param mixed $Url Url to query
     * @return mixed 
     */
    static public function getFirstJSON( $Url ){
        return self::getFirst( $Url, true );
    }

}