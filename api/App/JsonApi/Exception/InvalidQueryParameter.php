<?php

namespace App\JsonApi\Exception;

use App\HTTP;
use App\JsonApi\JsonApiException;

class InvalidQueryParameter extends JsonApiException {

    private $code = 14;

    public function __construct($detail, $sourceParameter = null) {
        parent::__construct(
            null,
            null,
            HTTP::CODE_BAD_REQUEST,
            $this->code,
            "Invalid query parameter",
            $detail,
            null,
            $sourceParameter,
            null);
    }
}