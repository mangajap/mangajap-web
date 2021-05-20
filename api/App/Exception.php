<?php

namespace App;

class Exception extends \Exception {

    private $error;

    public function __construct($error) {
        $this->error = $error;

        \Exception::__construct();
    }


    public function getError() {
        return $this->error;
    }
}