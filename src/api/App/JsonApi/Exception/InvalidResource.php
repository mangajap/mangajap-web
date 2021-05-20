<?php

namespace App\JsonApi\Exception;

use App\HTTP;
use App\JsonApi\JsonApiException;

class InvalidResource extends JsonApiException {

    private $code = 15;

    public function __construct($type) {
        parent::__construct(
            null,
            null,
            HTTP::CODE_BAD_REQUEST,
            $this->code,
            "Invalid resource",
            $type ." is not a valid resource.",
            null,
            null,
            null);
    }

}