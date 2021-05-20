<?php

namespace App\JsonApi\Exception;

use App\HTTP;
use App\JsonApi\JsonApiException;

class BadRequest extends JsonApiException {

    private $code = 400;

    public function __construct($detail) {
        parent::__construct(
            null,
            null,
            HTTP::CODE_BAD_REQUEST,
            $this->code,
            "Bad request",
            $detail,
            null,
            null,
            null);
    }


}