<?php

namespace App\JsonApi\Document;

use App\JsonApi;
use App\JsonApi\Document\LinksObject\Link;
use App\JsonApi\Document\LinksObject\Link\FirstLink;
use App\JsonApi\Document\LinksObject\Link\LastLink;
use App\JsonApi\Document\LinksObject\Link\NextLink;
use App\JsonApi\Document\LinksObject\Link\PrevLink;

class LinksObject {

    private $links;

    public function __construct(...$links) {
        $this->links = $links;
    }


    public function toArray() {
        $result = [];

        foreach ($this->links as $link) {
            if ($link instanceof Link) {
                $result = array_merge($result, $link->toArray());
            }
        }

        return $result;
    }



    public static function pagination($count) {
        $limit = JsonApi::getLimit();
        $offset = JsonApi::getOffset();

        $url = '/' . $_GET['url'];
        unset($_GET['url']);

        $links[] = new FirstLink($url . '?' . http_build_query(array_merge($_GET, [
                'page' => [
                    'limit' => $limit,
                    'offset' => 0,
                ]
            ])));

        if ($offset > $limit)
            $links[] = new PrevLink($url . '?' . http_build_query(array_merge($_GET, [
                    'page' => [
                        'limit' => $limit,
                        'offset' => $offset-$limit,
                    ]
                ])));
        else if ($offset > 0)
            $links[] = new PrevLink($url . '?' . http_build_query(array_merge($_GET, [
                    'page' => [
                        'limit' => $limit,
                        'offset' => 0,
                    ]
                ])));

        if ($offset < $count-$limit)
            $links[] = new NextLink($url . '?' . http_build_query(array_merge($_GET, [
                    'page' => [
                        'limit' => $limit,
                        'offset' => $offset+$limit,
                    ]
                ])));

        if($count - $limit > 0)
            $links[] = new LastLink($url . '?' . http_build_query(array_merge($_GET, [
                    'page' => [
                        'limit' => $limit,
                        'offset' => $count-$limit,
                    ]
                ])));
        else
            $links[] = new LastLink($url . '?' . http_build_query(array_merge($_GET, [
                    'page' => [
                        'limit' => $limit,
                        'offset' => 0,
                    ]
                ])));

        return new LinksObject(...$links);
    }
}