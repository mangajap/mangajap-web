<?php

namespace App\JsonApi\Document\LinksObject\Link;

use App\JsonApi\Document\LinksObject\Link;

class RelatedLink extends Link {

    public function type() {
        return 'related';
    }
}