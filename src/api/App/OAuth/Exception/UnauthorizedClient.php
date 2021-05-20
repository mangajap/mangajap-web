<?php

namespace App\OAuth\Exception;

use App\HTTP;
use App\OAuth\OAuthException;

class UnauthorizedClient extends OAuthException {

    public function __construct($error_description = null) {
        http_response_code(HTTP::CODE_BAD_REQUEST);
        parent::__construct('unauthorized_client', $error_description);
    }
}