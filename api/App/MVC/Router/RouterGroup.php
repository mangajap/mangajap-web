<?php

namespace App\MVC\Router;

class RouterGroup {

    private $prefix;
    private $routes = [];


    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }


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
        $route = new Route($this->prefix . $path, $callable);
        $this->routes[$method][] = $route;
        return $route;
    }


    public function getRoutes() {
        return $this->routes;
    }

}