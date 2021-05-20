<?php

namespace App\JsonApi\Document\LinksObject\Link;

use App\JsonApi\Document\LinksObject\Link;

class PrevLink extends Link {

    public function type() {
        return 'prev';
    }
}