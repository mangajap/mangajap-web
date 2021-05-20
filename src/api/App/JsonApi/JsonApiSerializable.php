<?php

namespace App\JsonApi;

interface JsonApiSerializable {

    public function JsonApi_type();

    public function JsonApi_id();

    public function JsonApi_attributes();

    public function JsonApi_relationships();


    public function JsonApi_filter();

}