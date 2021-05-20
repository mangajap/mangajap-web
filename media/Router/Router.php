<?php

class Router {

    private $before;
    private $after;
    private $routes = [];

    public function get($path, $callable) {
        return $this->add($path, $callable, 'GET');
    }

    public function head($path, $callable) {
        return $this->add($path, $callable, 'HEAD');
    }

    public function post($path, $callable) {
        return $this->add($path, $callable, 'POST');
    }

    public function put($path, $callable) {
        return $this->add($path, $callable, 'PUT');
    }

    public function patch($path, $callable) {
        return $this->add($path, $callable, 'PATCH');
    }

    public function delete($path, $callable) {
        return $this->add($path, $callable, 'DELETE');
    }

    private function add($path, $callable, $method) {
        $route = new Route($path, $callable);
        $this->routes[$method][] = $route;
        return $route;
    }



    public function before($callable) {
        $this->before = $callable;
    }

    public function after($callable) {
        $this->after = $callable;
    }


    public function run($uri) {
        //      Normalement :
//        if(!isset($this->routes[$_SERVER['REQUEST_METHOD']]))
        // Mais pour les methodes PATCH, PUT, ..., il faut avoir 000webhost en premium

        // Et il faudra supprimer ca lorsque je serais premium
        if (empty($_POST['REQUEST_METHOD']))
            $_POST['REQUEST_METHOD'] = 'GET';

        if(!isset($this->routes[$_POST['REQUEST_METHOD']])) {
//            throw new ErrorDocument(
//                new Error(
//                    new Code(Error::INVALID_REQUEST_METHOD),
//                    new Detail('REQUEST_METHOD does not exist')
//                )
//            );
        }

        foreach($this->routes[$_POST['REQUEST_METHOD']] as $route) {
            if (!$route instanceof Route)
                continue;

            if($route->match($uri)) {
                return $route->call();
            }
        }

        return null;

//        throw new ErrorDocument(
//            new Error(
//                new Code(Error::NO_MATCHING_ROUTES),
//                new Detail('Invalid URI')
//            )
//        );
    }
}