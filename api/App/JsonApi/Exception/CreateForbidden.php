<?php

namespace App\JsonApi\Exception;

use App\HTTP;
use App\JsonApi\JsonApiException;

class CreateForbidden extends JsonApiException {

    private $code = 403;

    public function __construct($type) {
        parent::__construct(
            null,
            null,
            HTTP::CODE_FORBIDDEN,
            $this->code,
            "Create Forbidden",
            "You don't have permission to create this ". $type .".",
            null,
            null,
            null);
    }

}