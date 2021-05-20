<?php

namespace App\JsonApi\Document\LinksObject\Link;

use App\JsonApi\Document\LinksObject\Link;

class LastLink extends Link {

    public function type() {
        return 'last';
    }
}