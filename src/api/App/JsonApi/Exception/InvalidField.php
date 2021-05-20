<?php

namespace App\JsonApi\Exception;

use App\HTTP;
use App\JsonApi\JsonApiException;

class InvalidField extends JsonApiException {

    private $code = 5;

    public function __construct($detail, $sourcePointer = null) {
        parent::__construct(
            null,
            null,
            HTTP::CODE_BAD_REQUEST,
            $this->code,
            "Invalid field",
            $detail,
            $sourcePointer,
            null,
            null);
    }

}