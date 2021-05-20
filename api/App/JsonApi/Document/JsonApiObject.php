<?php

namespace App\JsonApi\Document;

class JsonApiObject {

    private $version;
    private $meta;

    public function __construct($version, $meta = null) {
        $this->version = $version;
        $this->meta = $meta;
    }


    public function hasVersion() {
        return !empty($this->version());
    }

    public function version() {
        return $this->version;
    }

    public function hasMeta() {
        return !empty($this->meta());
    }

    public function meta() {
        return $this->meta;
    }


    public function toArray() {
        $result = [];


        if ($this->hasVersion())
            $result['version'] = $this->version();

        if ($this->hasMeta())
            $result['meta'] = $this->meta();

        return $result;
    }
}