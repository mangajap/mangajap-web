<?php

namespace App\JsonApi\Exception;

use App\HTTP;
use App\JsonApi\JsonApiException;

class InvalidAttribute extends JsonApiException {

    private $code = 10;

    public function __construct($detail, $sourcePointer = null) {
        parent::__construct(
            null,
            null,
            HTTP::CODE_UNPROCESSABLE_ENTITY,
            $this->code,
            "Invalid attribute",
            $detail,
            $sourcePointer,
            null,
            null);
    }


}