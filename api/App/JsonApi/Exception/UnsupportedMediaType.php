<?php

namespace App\JsonApi\Exception;

use App\HTTP;
use App\JsonApi\JsonApiException;

class UnsupportedMediaType extends JsonApiException {

    private $code = 17;

    public function __construct() {
        parent::__construct(
            null,
            null,
            HTTP::CODE_UNSUPPORTED_MEDIA_TYPE,
            $this->code,
            "Unsupported media type",
            "All requests that create or update must use the 'application/vnd.api+json' Content-Type. This request specified 'multipart/form-data'.",
            null,
            null,
            null);
    }

}