<?php

namespace App\JsonApi\Document\LinksObject\Link;

use App\JsonApi\Document\LinksObject\Link;

class SelfLink extends Link {

    public function type() {
        return 'self';
    }
}