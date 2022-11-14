<?php

namespace App\Curl\Src;

use App\Curl\Curl;

class CurlList implements \Iterator, \Countable {

    protected array $list = [];
    protected int $iteratorPosition = 0;
    protected Curl $parentCurl;

    public function __construct(Curl $parentCurl){
        $this->parentCurl = $parentCurl;
        $this->parentCurl->log('Creating a '.get_class( $this ).' instance');
    }

    public function __get( $keyName ){
        switch ( $keyName ){
            case 'parentCurl':
                return $this->parentCurl;
            default:
                return Null;
        }
    }

    public function getOne(){
        return $this->list[0] ?? Null;
    }

    public function getAll(){
        return $this->list;
    }

    public function item( int $index = 0 ){
        $this->parentCurl->log("Return the element {$index}");
        return $this->list[ $index ] ?? Null;
    }

    protected function log( $message, ... $sprintfOptions ){
        $this->parentCurl->log( $message, ... $sprintfOptions);
    }

    // Iterator interface mandatory functions to make it work with foreach
    public function rewind(): void { $this->iteratorPosition = 0; }
    public function current(){ return $this->list[ $this->iteratorPosition]; }
    public function key(): int { return $this->iteratorPosition; }
    public function next(): void { $this->iteratorPosition++; }
    public function valid(): bool { return isset( $this->list[ $this->iteratorPosition ]); }

    // Countable interface
    public function count(): int { return count( $this->list ); }

}

