<?php

namespace App\Utils;

class JSON {

    const FETCH_JSON_OBJECT = 0;
    const FETCH_OBJ = 1;
    const FETCH_ASSOC = 2;

    public static function isValid($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function encode($value) {
        return json_encode($value);
    }

    public static function decode($json, $fetch_style = null) {
        // 502 Bad Gateway, avec ça (je sais pas pourquoi)
//        if ($fetch_style == self::FETCH_JSON_OBJECT)
//            return new JSONObject($json);

        if ($fetch_style == self::FETCH_OBJ)
            return json_decode($json);

        elseif ($fetch_style == self::FETCH_ASSOC)
            return json_decode($json, true);

        return json_decode($json);
    }
}