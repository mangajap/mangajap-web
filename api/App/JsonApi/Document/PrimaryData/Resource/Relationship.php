<?php

namespace App\JsonApi\Document\PrimaryData\Resource;

use App\JsonApi\Document\LinksObject;
use App\JsonApi\Document\PrimaryData;
use App\JsonApi\Document\PrimaryData\ResourceCollection;
use App\JsonApi\Document\PrimaryData\ResourceObject;
use App\MVC\Model;
use App\MVC\Query\Result;

class Relationship {

    const LINKS_SELF = 0;
    const LINKS_RELATED = 1;
    const DATA = 2;
    const META = 3;

    private $name;
    private $links;
    private $data;
    private $meta;


    public function __construct($name, $links, $data, $meta) {
        $this->name = $name;
        $this->links = $links;
        $this->data = $data;
        $this->meta = $meta;
    }


    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function hasLinks() {
        return !empty($this->links);
    }

    public function getLinks(): LinksObject {
        return $this->links;
    }

    public function setLinks($links) {
        $this->links = $links;
    }

    public function hasData() {
        return isset($this->data);
    }

    public function getData(): PrimaryData {
        return $this->data;
    }

    public function setData($data) {
        if ($data instanceof Model)
            $data = $data->toJsonApi()->identifiers();
        elseif ($data instanceof Result)
            $data = $data->toJsonApi()->identifiers();

        elseif ($data instanceof ResourceObject)
            $data = $data->identifiers();
        elseif ($data instanceof ResourceCollection)
            $data = $data->identifiers();

        $this->data = $data;
    }

    public function hasMeta() {
        return !empty($this->meta);
    }

    public function getMeta() {
        return $this->meta;
    }

    public function setMeta($meta) {
        $this->meta = $meta;
    }


    public function toArray() {
        $result = [];

        if ($this->hasLinks())
            $result['links'] = $this->getLinks()->toArray();

        if ($this->hasData())
            $result['data'] = $this->getData()->toArray();

        if ($this->hasMeta())
            $result['meta'] = $this->getMeta();

        return $result;
    }
}