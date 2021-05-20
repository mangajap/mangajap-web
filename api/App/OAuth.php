<?php

namespace App;

class OAuth {

    public static function getBearerToken() {

        $authorizationHeader = Header::getAuthorization();

        if (preg_match('@Bearer\s([a-zA-Z0-9-._~+/]{64})@', $authorizationHeader, $token)) {
            return $token[1];
        }

        return null;
    }
}