<?php 

namespace App\Curl\Src;

class Handle {
    
    private \CurlHandle $CurlHandle;

    public function __construct( ?string $Url ){
        $this->CurlHandle = \curl_init( $Url );
    }

    public function getInfo( ?int $Options = Null ): mixed {
        return \curl_getinfo( $this->CurlHandle, $Options );
    }

    public function getUrl(){
        return $this->getInfo( \CURLINFO_EFFECTIVE_URL );
    }

    public function setOptions( array $Options ): bool {
        return \curl_setopt_array( $this->CurlHandle, $Options );
    }

    public function execute(){
        return \curl_exec( $this->CurlHandle );
    }

    public function getHandle(): \CurlHandle{
        return $this->CurlHandle;
    }
} 

