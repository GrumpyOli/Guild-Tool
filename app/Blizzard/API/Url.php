<?php

namespace App\Blizzard\API;

/**
 * Class that build multiple urls for any purpose
 * @package Bnet
 */
class Url {

    private bool $OAuthHost = false;

    public function __construct( 
        private string $path,
        private string $namespace,
        private string $region = 'us',
        private string $locale = 'en_US'    
    ){
        // Construct function
        $this->namespace( $namespace );
        $this->locale( $locale );    
    }

    public function namespace( $namespace, $region = null ){
        $this->namespace = $namespace;
        return $this;
    }

    public function locale( $locale ){

    }

    public function region( $region ){
        $this->region = $region;
        return $this;
    }

    public function OAuthHost( $OAuth = true ){
        $this->OAuthHost = true;
    }

    public function parse(){

        // OAuth hostname
        if ($this->OAuthHost){
            return $this->parseOAuth($this->region);
        }

        // regulare API Path hostname
        return $this->parseHost( $this->region ).$this->parsePath().$this->parseQuery();
    }


    public function parsePath(){
        return $this->path;
    }

    public function parseOAuth( $region ){

        if ( !Helpers::isRegionValid($region) ){
            throw new Exception('Invalid region');
        }

        if ( \strtolower( $region ) === 'cn' ){
            $host =  'https://oauth.battlenet.com.cn/';
        }
        
        $host = 'https://oauth.battle.net'; 

        return $host . $this->parsePath();

    }

    /**
     * Return a base url for all data request
     * @param string $region 
     * @return string Base url
     * @throws Exception 
     * @throws Exception 
     */
    public function parseHost( $region ): string{
        
        if ( !Helpers::isRegionValid($region) ){
            throw new Exception('Invalid region');
        }

        if ( \strtolower( $region ) === 'cn' ){
            return 'https://gateway.battlenet.com.cn';
        }
        
        return 'https://' . \strtolower( $region ) . '.api.blizzard.com'; 

    }

    public function parseQuery(){

        $Query = '?';
        $Query .= "namespace={$this->namespace}-{$this->region}";
        $Query .= "&locale={$this->locale}";

        return $Query;

    }


    /**
     * 
     * Static helpers function
     * 
     * 
     * 
     */

    static public function wowProfil(){
        $Url = new Url('/profile/user/wow', 'profile', Token::retrieve()->getRegion() );
        return $Url->parse();
    }

    static public function userInfo(){
        $Url = new Url('/oauth/userinfo', 'profile', Token::retrieve()->getRegion() );
        $Url->OAuthHost( true );
        return $Url->parse();
    }

    static public function realmIndex(){
        $Url = new Url('/data/wow/realm/index', 'dynamic', Token::retrieve()->getRegion() );
        return $Url->parse();
    }

    static public function guildInfos( $realmSlug, $nameSlug ){
        $Url = new Url("/data/wow/guild/{$realmSlug}/{$nameSlug}", 'profile', Token::retrieve()->getRegion() );
        return $Url->parse();        
    }

    static public function guildRoster( $realmSlug, $nameSlug ){
        $Url = new Url("/data/wow/guild/{$realmSlug}/{$nameSlug}/roster", 'profile', Token::retrieve()->getRegion() );
        return $Url->parse();        
    }
}