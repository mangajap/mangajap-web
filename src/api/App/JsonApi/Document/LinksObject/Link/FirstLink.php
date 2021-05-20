<?php

namespace App\JsonApi\Document\LinksObject\Link;

use App\JsonApi\Document\LinksObject\Link;

class FirstLink extends Link {

    public function type() {
        return 'first';
    }
}