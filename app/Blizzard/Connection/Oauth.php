<?php

namespace App\Blizzard\Connection;

use DateTimeInterface;

/**
 * Class : Oauth
 * Namespace : Bnet
 * 
 * This class manage the identification with blizzard and it will get the token asked for
 * There is two token type :
 *      client_credentials : Get very basic information (Used for exemple to retrieve server list.. )
 *      authorization_code : Get every information that we need like guild rank
 */
class Oauth {

    public static string $clientID = '2d18a1ffbc8845278d1e8e7abde4f49d';
    private string $clientSecret = 'HH1GYo4buL7TD15KhxxmpYTCMobHw8DD';
    private ?Token $Token;
    private string $grantType;
    private string $returnURI;

    public const DateTimeFormat = 'l, d-M-Y H:i:s e';
    public const AUTH_CODE = 'authorization_code'; // Private Data
    public const CLIENT_CODE = 'client_credentials'; // Public Access

    /**
     * Oauth2.0 Object
     * @param string $OauthType
     * @return void 
     */
    public function __construct($OauthType = 'client_credentials'){
        
        // Setting up the return path for Blizzard API
        $this->returnURI = route('BlizzardLandingPage');

        $this->Token = Token::retrieve();

        // On determine le type de jeton que le classe OAUTH va controler
        $this->grantType = ( $OauthType === Token::auth_authorization_code ) ? Token::auth_authorization_code : Token::auth_client_credentials;

    }

    
    /**
     * Function that will get the Access Token from blizzard to get information. There is two type of token
     * Adjusted for PHP8
     */
    public function getAccessToken( ?string $AuthCode = Null ): Token{     
        
        // Verifying if the token already exist
        if ( isset( $this->Token ) ){
            return $this->Token;
        }

        // 
        switch ( $this->grantType ){
            case 'authorization_code':
                
                if ( $AuthCode === Null ){
                    throw new Exception('A code has to be submitted');
                }
                
                // Data to be submitted with cUrl request
                $cUrl_PostData = [
                    'scope' => 'wow.api',
                    'grant_type' => Token::auth_authorization_code,
                    'code' => $AuthCode,
                    'redirect_uri' => $this->returnURI
                ];
                
                break;
                
            case 'client_credentials':
                $cUrl_PostData = ['grant_type' => Token::auth_client_credentials];
                break;
            }
                
        // Init cUrl
        $cUrl = curl_init();
        
        // Applying these settings
        \curl_setopt_array( $cUrl, [
            CURLOPT_URL => 'https://us.battle.net/oauth/token',
            CURLOPT_USERPWD => self::$clientID.':'.$this->clientSecret,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: multipart/form-data'],
            CURLOPT_POSTFIELDS => $cUrl_PostData,
            CURLOPT_SSL_VERIFYPEER => false            
        ]);
        
        $serverResponse = \curl_exec( $cUrl );
        $status = \curl_getinfo( $cUrl , CURLINFO_HTTP_CODE);
        
        if ( \curl_errno($cUrl) !== CURLE_OK ){
            throw new Exception('Erreur cUrl ' . \curl_errno($cUrl) . ' : ' . \curl_error( $cUrl ));
        }
       
        /* On regarde le code de retour
        *
        * 200       -> OK
        * Autres    -> Erreur
        */
        switch ($status){
            
            case 200:
                
                // Decoding data received
                $Data = json_decode($serverResponse);

                if ( $this->grantType == Oauth::AUTH_CODE ){
                    $this->Token = new Token($this->grantType, $Data->scope, $Data->access_token, $Data->expires_in);
                }

                if ( $this->grantType == Oauth::CLIENT_CODE ){
                    $this->Token = new Token($this->grantType, '', $Data->access_token, $Data->expires_in);
                }

                
                // Storing the token in the database

                Token::register( $this->Token );
                
                return $this->Token;
                
            default:

                $Data = json_decode($serverResponse);
                throw new \Exception('Error: ' . $status . ' ' . $Data->error_description);

                break;
                
        }
                
    }
                
    /**
     * Function that returns a correct URL for the Oauth process
     * @return string
     */
    public static function getAuthorizeURL(){
        
        $redirectUri = route('BlizzardLandingPage');
        $clientID = self::$clientID;

        return "https://us.battle.net/oauth/authorize?client_id={$clientID}&redirect_uri={$redirectUri}&response_type=code&scope=wow.profile";

    }
                 
    public function __get($varname){
        switch ( $varname ){
            case 'Token':
            case 'grantType':
                return $this->{$varname};
            default :
                return false;
        }
    }
}
            

class Exception extends \Exception {}