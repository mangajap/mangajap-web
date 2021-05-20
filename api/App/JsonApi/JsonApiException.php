<?php

namespace App\JsonApi;

use App\JsonApi\Document\LinksObject;

class JsonApiException {

    private $id;
    private $links;
    private $status;
    private $code;
    private $title;
    private $detail;
    private $sourcePointer;
    private $sourceParameter;
    private $meta;

    public function __construct($id, $links, $status, $code, $title, $detail, $sourcePointer, $sourceParameter, $meta) {
        $this->id = $id;
        $this->links = $links;
        $this->status = $status;
        $this->code = $code;
        $this->title = $title;
        $this->detail = $detail;
        $this->sourcePointer = $sourcePointer;
        $this->sourceParameter = $sourceParameter;
        $this->meta = $meta;
    }

    private function hasId() {
        return isset($this->id);
    }

    public function getId() {
        return $this->id;
    }

    private function hasLinks() {
        return !empty($this->links);
    }

    public function getLinks(): LinksObject {
        return $this->links;
    }

    private function hasStatus() {
        return isset($this->status);
    }

    public function getStatus() {
        http_response_code((int) $this->status);
        return $this->status;
    }

    private function hasCode() {
        return isset($this->code);
    }

    private function hasTitle() {
        return isset($this->title);
    }

    public function getTitle() {
        return $this->title;
    }

    private function hasDetail() {
        return !empty($this->detail);
    }

    public function getDetail() {
        return $this->detail;
    }

    private function hasSourcePointer() {
        return !empty($this->sourcePointer);
    }

    public function getSourcePointer() {
        return $this->sourcePointer;
    }

    private function hasSourceParameter() {
        return !empty($this->sourceParameter);
    }

    public function getSourceParameter() {
        return $this->sourceParameter;
    }
    
    private function hasMeta() {
        return !empty($this->meta);
    }

    public function getMeta() {
        return $this->meta;
    }




    public function toArray() {
        $error = [];

        if ($this->hasId())
            $error['id'] = $this->getId();

        if ($this->hasLinks())
            $error['links'] = $this->getLinks()->toArray();

        if ($this->hasStatus())
            $error['status'] = $this->getStatus();

        if ($this->hasCode())
            $error['code'] = $this->getCode();

        if ($this->hasTitle())
            $error['title'] = $this->getTitle();

        if ($this->hasDetail())
            $error['detail'] = $this->getDetail();

        if ($this->hasSourcePointer())
            $error['source']['pointer'] = $this->getSourcePointer();

        if ($this->hasSourceParameter())
            $error['source']['parameter'] = $this->getSourceParameter();

        if ($this->hasMeta())
            $error['meta'] = $this->getMeta();

        return $error;
    }
}