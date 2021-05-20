<?php

namespace App\OAuth\Exception;

use App\HTTP;
use App\OAuth\OAuthException;

class InvalidGrant extends OAuthException {

    public function __construct($error_description = "The provided authorization grant is invalid, expired, revoked, does not match the redirection URI used in the authorization request, or was issued to another client.") {
        http_response_code(HTTP::CODE_BAD_REQUEST);
        parent::__construct('invalid_grant', $error_description);
    }

}