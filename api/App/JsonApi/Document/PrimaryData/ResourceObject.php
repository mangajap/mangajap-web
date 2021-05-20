<?php

namespace App\JsonApi\Document\PrimaryData;

use App\JsonApi\Document\LinksObject;
use App\JsonApi\Document\PrimaryData;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;

class ResourceObject extends PrimaryData {

    const LINKS_SELF = 10;

    private $id;
    private $type;
    private $links;
    private $attributes;
    private $relationships;
    private $meta;


    public function __construct($type, $id, $links, $attributes, $relationships, $meta) {
        $this->type = $type;
        $this->id = strval($id);
        $this->links = $links;
        $this->attributes = $attributes;
        $this->relationships = $relationships;
        $this->meta = $meta;
    }


    public function identifiers() {
        return new ResourceObject($this->getType(), $this->getId(), null, null, null, null);
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
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

    public function hasAttributes() {
        return !empty($this->attributes);
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function setAttributes($attributes) {
        $this->attributes = $attributes;
    }

    public function hasRelationships() {
        return !empty($this->relationships);
    }

    public function getRelationships() {
        return $this->relationships;
    }

    public function relationship($name): Relationship {
        return $this->relationships[$name];
    }

    public function setRelationships($relationships) {
        $this->relationships = $relationships;
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
        $data = [
            'type' => $this->getType(),
            'id' => $this->getId(),
        ];

        if ($this->hasLinks())
            $data["links"] = $this->getLinks()->toArray();

        if ($this->hasAttributes())
            $data["attributes"] = $this->getAttributes();

        if ($this->hasRelationships()) {
            $data["relationships"] = [];
            foreach ($this->getRelationships() as $name => $relationship) {
                $data["relationships"][$name] = $this->relationship($name)->toArray();
            }
        }

        if ($this->hasMeta())
            $data["meta"] = $this->getMeta();

        return $data;
    }

}