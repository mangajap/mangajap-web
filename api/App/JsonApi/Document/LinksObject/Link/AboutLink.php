<?php

namespace App\JsonApi\Document\LinksObject\Link;

use App\JsonApi\Document\LinksObject\Link;

class AboutLink extends Link {

    public function type() {
        return 'about';
    }
}