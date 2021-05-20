<?php

namespace App\Security;


class Password {

    public static function hash($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function equals($password, $hash): bool {
        return password_verify($password, $hash);
    }
}