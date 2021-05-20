<?php

namespace App;

class Header {

    private static $Authorization;

    public static function getAuthorization() {
        if (self::$Authorization !== null)
            return self::$Authorization;

        $headers = apache_request_headers();

        if (!empty($headers['Authorization']))
            return $headers['Authorization'];

        if (!empty($headers['authorization']))
            return $headers['authorization'];

        if (!empty($_POST['Authorization']))
            return $_POST['Authorization'];

        return null;
    }

    public static function setAuthorization($authorization) {
        self::$Authorization = $authorization;
    }
}