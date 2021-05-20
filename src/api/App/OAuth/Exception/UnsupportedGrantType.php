<?php

namespace App\OAuth\Exception;

use App\HTTP;
use App\OAuth\OAuthException;

class UnsupportedGrantType extends OAuthException {

    public function __construct($error_description = "Grant type not supported") {
        http_response_code(HTTP::CODE_BAD_REQUEST);
        parent::__construct('unsupported_grant_type', $error_description);
    }
}