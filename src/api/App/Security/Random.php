<?php

namespace App\Security;

/**
 * Created by PhpStorm.
 * User: Stant
 * Date: 12/04/2020
 * Time: 14:44
 */
class Random {


    /**
     * Random constructor.
     */
    public function __construct() {
        return $this;
    }


    public static function bytes($len) {
        return random_bytes($len);
    }

    public static function hex($len) {
        return bin2hex(self::bytes($len/2));
    }

    public static function base64($len) {

    }

    public static function base64Safe() {

    }

    public static function number($n) {
        return random_int(0, $n);
    }
}