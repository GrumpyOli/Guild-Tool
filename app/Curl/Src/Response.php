<?php

namespace App\Curl\Src;

class Response {

    function __construct(
        protected \CurlHandle $CurlHandle,
        protected string $response
    ){}

    public function getErrNo(): int { return \curl_errno($this->CurlHandle); }
    public function getErrorMessage(): string { return \curl_strerror( $this->getErrNo() ); }
    public function getJSON(): mixed{ return \json_decode( $this->response ); }
    public function getStatus(): int { return \curl_getinfo($this->CurlHandle, CURLINFO_HTTP_CODE); }
    public function getUrl(): string { return \curl_getinfo($this->CurlHandle, CURLINFO_EFFECTIVE_URL); }
    public function getResponse(): string { return $this->response; }
    public function Dump( bool $includeData = true ): array {
        $Output = [
            'url' => $this->getUrl(),
            'errorNo' => $this->getErrNo(),
            'errorMessage' => $this->getErrorMessage(),
            'status' => $this->getStatus()
        ];

        if ( $includeData ){
            $Output['data'] = $this->getJSON();
        }

        return $Output;
    }

}
