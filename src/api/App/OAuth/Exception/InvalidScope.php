<?php

namespace App\OAuth\Exception;

use App\HTTP;
use App\OAuth\OAuthException;

class InvalidScope extends OAuthException {

    public function __construct($error_description = null) {
        http_response_code(HTTP::CODE_BAD_REQUEST);
        parent::__construct('invalid_scope', $error_description);
    }
}