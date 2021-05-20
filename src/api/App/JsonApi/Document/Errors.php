<?php

namespace App\JsonApi\Document;

use App\Exception;
use App\JsonApi\JsonApiException;
use App\Utils\JSON;

class Errors extends Exception {

    public function __construct(JsonApiException ...$jsonApiExceptions) {
        $result['errors'] = [];

        foreach ($jsonApiExceptions as $jsonApiException) {
            $result['errors'][] = $jsonApiException->toArray();
        }

        exit(JSON::encode($result));
    }
}