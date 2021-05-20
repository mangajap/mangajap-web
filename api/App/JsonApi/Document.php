<?php

namespace App\JsonApi;

use App\JsonApi\Document\JsonApiObject;
use App\JsonApi\Document\LinksObject;
use App\JsonApi\Document\PrimaryData;
use App\JsonApi\Document\PrimaryData\ResourceCollection;
use App\JsonApi\Document\PrimaryData\ResourceObject;
use App\MVC\Model;
use App\MVC\Query\Result;

class Document {

    private $jsonApi;
    private $data;
    private $included = [];
    private $links;
    private $meta;
    private $errors;


    public function __construct($jsonApi = null, $data = null, $included = [], $links = null, $meta = null, $errors = null) {
        $this->jsonApi = $jsonApi;
        $this->data = $data;
        $this->included = $included;
        $this->links = $links;
        $this->meta = $meta;
        $this->errors = $errors;
    }


    public function hasJsonApi() {
        return isset($this->jsonApi);
    }

    public function getJsonApi(): JsonApiObject {
        return $this->jsonApi;
    }

    public function setJsonApi($jsonApi) {
        $this->jsonApi = $jsonApi;
    }

    public function hasData() {
        return isset($this->data);
    }

    public function getData(): PrimaryData {
        return $this->data;
    }

    public function setData($data) {
        if ($data instanceof Model)
            $data = $data->toJsonApi();
        elseif ($data instanceof Result)
            $data = $data->toJsonApi();

        $this->data = $data;
    }

    public function hasIncluded() {
        return !empty($this->included);
    }

    public function getIncluded(): ResourceCollection {
        $this->included = array_values(array_unique($this->included, SORT_REGULAR));

        return new ResourceCollection(
            ...$this->included
        );
    }

    public function addResourceToIncluded($resource) {
        if ($resource instanceof Model) {
            $this->included[] = $resource->toJsonApi();
        }
        elseif ($resource instanceof Result) {
            foreach ($resource as $model) {
                $this->included[] = $model->toJsonApi();
            }
        }
        elseif ($resource instanceof ResourceObject)
            $this->included[] = $resource;
        elseif ($resource instanceof ResourceCollection)
            $this->included = array_merge($this->included, $resource->getResources());
    }

    public function setIncluded($included) {
        $this->included = $included;
    }

    public function hasLinks() {
        return isset($this->links);
    }

    public function getLinks(): LinksObject {
        return $this->links;
    }

    public function setLinks($links) {
        $this->links = $links;
    }

    public function hasMeta() {
        return isset($this->meta);
    }

    public function getMeta() {
        return $this->meta;
    }

    public function setMeta($meta) {
        $this->meta = $meta;
    }

    public function hasErrors() {
        return isset($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    public function setErrors($errors) {
        $this->errors = $errors;
    }





    public function toArray() {
        if ($this->hasErrors())
            return $content['errors'] = $this->getErrors();


        $content = [];

        if ($this->hasJsonApi())
            $content['jsonapi'] = $this->getJsonApi()->toArray();

        if ($this->hasData())
            $content['data'] = $this->getData()->toArray();

        if ($this->hasIncluded())
            $content['included'] = $this->getIncluded()->toArray();

        if ($this->hasMeta())
            $content['meta'] = $this->getMeta();

        if ($this->hasLinks())
            $content['links'] = $this->getLinks()->toArray();

        return $content;
    }

}