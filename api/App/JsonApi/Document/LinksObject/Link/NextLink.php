<?php

namespace App\JsonApi\Document\LinksObject\Link;

use App\JsonApi\Document\LinksObject\Link;

class NextLink extends Link {

    public function type() {
        return 'next';
    }
}