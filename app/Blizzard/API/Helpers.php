<?php

namespace App\Blizzard\API;

class Helpers {

    /**
     * List of supported region for the application
     */
    const Regions = ['us'];

    /**
     * List of supported namespaces for battle.net request
     */
    const Namespaces = ['static', 'dynamic', 'profile'];

    // Default values
    const defaultRegion = 'us';
    const defaultLocale = 'en_US';
    

    /**
     * Check if the locale exists and is valid. This is case sensitive.
     * @param string $locale Locale name (Case Sensitive)
     * @return bool 
     */
    static public function isLocaleValid( string $locale ): bool{

        static $locales = [];

        if ( $locales === [] ){
            $locales = json_decode( file_get_contents('lang.json') );
        }
    
        return in_array( $locale, $locales );
    
    }

    /**
     * Return true if the region is valid
     * @param mixed $region 
     * @return bool 
     */
    static public function isRegionValid( string $region ): bool {
        return in_array( strtolower($region), self::Regions);
    }


    /**
     * Check if the namespace is valid. Must be provided with the region at the end. For exemple : dynamic-us
     * @param string $namespace 
     * @return bool 
     */
    static public function isNamespaceValid( string $namespace ): bool{

        // Separate the namespace and the region
        $Arg = array_map( 'strtolower', explode('-', $namespace));
        
        return ( count($Arg) != 2 || !in_array( $Arg[0], self::Namespaces) || !self::isRegionValid( $Arg[1] )) ? false : true;

    } 
    
    
    /**
     * Convert a string to his "slug" equivalent 
     * @param string $string Non-Slug version of the string
     * @return string 
     */
    static public function parseToSlug( string $string ): string {    
        return strtolower( str_replace([' ', '\''], ['-', ''], $string ));
    }

    static public function importGuild( string $region, string $realm, string $name){}


}