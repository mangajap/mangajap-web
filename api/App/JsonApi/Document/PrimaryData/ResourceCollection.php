<?php

namespace App\JsonApi\Document\PrimaryData;

use App\JsonApi\Document\PrimaryData;

class ResourceCollection extends PrimaryData {

    public $resources = [];

    public function __construct(ResourceObject ...$resources) {
        $this->resources = $resources;
    }

    public function getResources() {
        return $this->resources;
    }


    public function identifiers() {
        $identifiers = [];
        foreach ($this->resources as $resource) {
            if ($resource instanceof ResourceObject)
                $identifiers[] = $resource->identifiers();
        }

        return new ResourceCollection(
            ...$identifiers
        );
    }


    public function toArray() {
        $result = [];

        foreach ($this->resources as $resource) {
            if ($resource instanceof ResourceObject)
                $result[] = $resource->toArray();
        }

        return $result;
    }
}