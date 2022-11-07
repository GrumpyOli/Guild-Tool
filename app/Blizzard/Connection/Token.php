<?php

namespace App\Blizzard\Connection;

use \Datetime;

class Token {

    const auth_authorization_code = 'authorization_code';
    const auth_client_credentials = 'client_credentials';

    protected \Datetime $expiresDateTime;
    protected string $region;

    function __construct(
        protected string $granType,
        protected string $scope,
        protected string $accessToken,
        protected int $expiresIn
    ){
        // Finding the region according to the token
        $this->region = \substr($this->accessToken, 0, 2);

        // Setting the DateTime Object
        $Time = time() + $this->expiresIn;
        $this->expiresDateTime = new DateTime( 'now' , new \DateTimeZone('America/New_York'));
        $this->expiresDateTime->modify("+{$this->expiresIn} seconds");
    }
    
    public function getRegion(): string { return $this->region; }

    /**
     * Return the type of token. Could be authorization_code or client_credentials 
     * @return string 
     */
    public function getGrantType(): string { return $this->granType; }

    /**
     * Return the scope of the token
     * @return string 
     */
    public function getScope(): string { return $this->scope; }

    /**
     * Return the access token number. This is used for identification
     * @return string 
     */
    public function getAccessToken(): string { return $this->accessToken; }

    /**
     * Return the token expiration timer
     * @return int 
     */
    public function getExpiresIn(): int { return $this->ExpiresIn; }

    /**
     * Return the DateTime object of the expires timestamp
     * @return DateTime 
     */
    public function getExpiresDateTime(): string { return $this->expiresDateTime->format( Oauth::DateTimeFormat ); }

    /**
     * Return true if the token is no longer valid and need to be re-evaluated
     * @return bool 
     */
    public function isExpired() : bool { return ( $this->expiresDateTime->getTimestamp() < time() ) ? true : false; }

    /**
     * Retrieve a token register in the PHP Session by his index. Index at 0 will return the first token generated.
     * @param int $index Index number in the array of token
     * @return Token|false 
     */
    static public function retrieve( int $index = 0 ): Token|Array|Null {
        // return $_SESSION['BattleNet']['Token'][$index] ?? false;
        return session('BlizzardToken');
    }

    static public function flush(){
        session(['BlizzardToken' => Null]);
    }

    static public function register( Token $Token ){
        session(['BlizzardToken' => $Token]);
    }
/*
    static public function getInfos( Token $Token, $Region = 'us', bool $Validate = true ){

        if ( $Validate && !self::validate( $Token ) ) {
            throw new \Exception('Invalid Token');
        }

        $Url = \Bnet\API::buildOAuthUrl($Region, \Bnet\API::PATH_USER_INFO );
        

        $Curl = new \Application\Curl($Url);
        $Curl->handleList->setOption(\CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $Token->getAccessToken() ] );
        $Curl->execute();

        $Response = $Curl->responseList->getOne();

        return $Response->getJSON();

    }

    static public function validate( Token $Token, $Region = 'us' ): array|object {

        $Url = \Bnet\API::buildOAuthUrl($Region, \Bnet\API::PATH_TOKEN_VALIDATION );

        $Curl = new \Application\Curl($Url);
        $Curl->handleList->setOptions( [\CURLOPT_POSTFIELDS => ['token' => $Token->getAccessToken() ] ] );
        $Curl->execute();

        $Response = $Curl->responseList->getOne();

        //var_dump( $Response->getJSON() );

        if ( $Response->getStatus() !== 200 ){
            return false;
        }else{
            return $Response->getJSON();
        }

    }
*/
}