<?php

namespace App\OAuth;

use Exception;

class OAuthException extends Exception {

    public function __construct($error, $error_description = '') {
        $result['error'] = $error;
        $result['error_description'] = $error_description;

        exit(json_encode($result));
    }
}