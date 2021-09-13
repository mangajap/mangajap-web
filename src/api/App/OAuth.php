<?php

namespace App;

class OAuth {

    public static function getBearerToken() {

        $authorizationHeader = Header::getAuthorization();

        if (preg_match('@Bearer\s([a-zA-Z0-9-._~+/]{28,64})@', $authorizationHeader, $token)) {
          if (strlen($token[1]) === 64) {
            throw new Exception('Invalid version app');
          }
          
          return $token[1];
        }

        return null;
    }
}