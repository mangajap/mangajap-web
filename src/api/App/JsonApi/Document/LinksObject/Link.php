<?php

namespace App\JsonApi\Document\LinksObject;

abstract class Link {

    private $url;
    private $meta;

    public function __construct($url, $meta = null) {
        $this->url =  'https://mangajap.000webhostapp.com/api' . $url;
    }

    public function toArray() {
        if (!isset($this->meta)) {
            return [
                $this->type() => $this->url,
            ];
        }
        else {
            return [
                $this->type() => [
                    'href' => $this->url,
                    'meta' => $this->meta,
                ],
            ];
        }

    }

    abstract public function type();
}