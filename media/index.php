<?php

include "Router/Router.php";
include "Router/Route.php";

function show($fileName, $size = null) {
    header('Content-Type: image/jpeg');

    if ($size == null) {
        imagejpeg(imagecreatefromjpeg($fileName));
        return;
    }

    list($width, $height) = getimagesize($fileName);

    $newWidth = $size;
    $newHeight = ($size / $width) * $height;

    $thumb = imagecreatetruecolor($newWidth, $newHeight);

    imagecopyresampled(
        $thumb,
        imagecreatefromjpeg($fileName),
        0,
        0,
        0,
        0,
        $newWidth,
        $newHeight,
        $width,
        $height
    );

    imagejpeg($thumb);
}

$router = new Router();

$router->get(
    '/users/avatars/{id:[0-9]+}/tiny.jpeg',
    function($id) {
        show(
            'http://mangajap.000webhostapp.com/images/user/avatar/'.$id.'.jpg',
            40
        );
    }
);

$router->get(
    '/users/avatars/{id:[0-9]+}/small.jpeg',
    function($id) {
        show(
            'http://mangajap.000webhostapp.com/images/user/avatar/'.$id.'.jpg',
            64
        );
    }
);

$router->get(
    '/users/avatars/{id:[0-9]+}/medium.jpeg',
    function($id) {
        show(
            'http://mangajap.000webhostapp.com/images/user/avatar/'.$id.'.jpg',
            100
        );
    }
);

$router->get(
    '/users/avatars/{id:[0-9]+}/large.jpeg',
    function($id) {
        show(
            'http://mangajap.000webhostapp.com/images/user/avatar/'.$id.'.jpg',
            200
        );
    }
);

$router->get(
    '/users/avatars/{id:[0-9]+}/original.jpeg',
    function($id) {
        show(
            'http://mangajap.000webhostapp.com/images/user/avatar/'.$id.'.jpg'
        );
    }
);



$router->get(
    '/manga/cover/{slug:[a-z-]+}/tiny.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/cover/{$slug}.jpg",
            40
        );
    }
);

$router->get(
    '/manga/cover/{slug:[a-z-]+}/small.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/cover/{$slug}.jpg",
            64
        );
    }
);

$router->get(
    '/manga/cover/{slug:[a-z-]+}/medium.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/cover/{$slug}.jpg",
            100
        );
    }
);

$router->get(
    '/manga/cover/{slug:[a-z-]+}/large.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/cover/{$slug}.jpg",
            200
        );
    }
);

$router->get(
    '/manga/cover/{slug:[a-z-]+}/original.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/cover/{$slug}.jpg"
        );
    }
);



$router->get(
    '/manga/banner/{slug:[a-z-]+}/tiny.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/banner/{$slug}.jpg",
            40
        );
    }
);

$router->get(
    '/manga/banner/{slug:[a-z-]+}/small.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/banner/{$slug}.jpg",
            64
        );
    }
);

$router->get(
    '/manga/banner/{slug:[a-z-]+}/medium.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/banner/{$slug}.jpg",
            100
        );
    }
);

$router->get(
    '/manga/banner/{slug:[a-z-]+}/large.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/banner/{$slug}.jpg",
            200
        );
    }
);

$router->get(
    '/manga/banner/{slug:[a-z-]+}/original.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/manga/banner/{$slug}.jpg"
        );
    }
);



$router->get(
    '/anime/cover/{slug:[a-z-]+}/tiny.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/anime/cover/{$slug}.jpg",
            40
        );
    }
);

$router->get(
    '/anime/cover/{slug:[a-z-]+}/small.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/anime/cover/{$slug}.jpg",
            64
        );
    }
);

$router->get(
    '/anime/cover/{slug:[a-z-]+}/medium.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/anime/cover/{$slug}.jpg",
            100
        );
    }
);

$router->get(
    '/anime/cover/{slug:[a-z-]+}/large.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/anime/cover/{$slug}.jpg",
            200
        );
    }
);

$router->get(
    '/anime/cover/{slug:[a-z-]+}/original.jpeg',
    function($slug) {
        show(
            "http://mangajap.000webhostapp.com/images/anime/cover/{$slug}.jpg"
        );
    }
);

$router->run($_GET['url']);