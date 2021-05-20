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

    $thumb = imagecreatetruecolor($size, $size);

    imagecopyresampled(
        $thumb,
        imagecreatefromjpeg($fileName),
        0,
        0,
        0,
        0,
        $size,
        $size,
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
            'http://mangajap.000webhostapp.com/img/user/avatar/'.$id.'.jpg',
            40
        );
    }
);

$router->get(
    '/users/avatars/{id:[0-9]+}/small.jpeg',
    function($id) {
        show(
            'http://mangajap.000webhostapp.com/img/user/avatar/'.$id.'.jpg',
            64
        );
    }
);

$router->get(
    '/users/avatars/{id:[0-9]+}/medium.jpeg',
    function($id) {
        show(
            'http://mangajap.000webhostapp.com/img/user/avatar/'.$id.'.jpg',
            100
        );
    }
);

$router->get(
    '/users/avatars/{id:[0-9]+}/large.jpeg',
    function($id) {
        show(
            'http://mangajap.000webhostapp.com/img/user/avatar/'.$id.'.jpg',
            200
        );
    }
);

$router->get(
    '/users/avatars/{id:[0-9]+}/original.jpeg',
    function($id) {
        show(
            'http://mangajap.000webhostapp.com/img/user/avatar/'.$id.'.jpg'
        );
    }
);

$router->run($_GET['url']);