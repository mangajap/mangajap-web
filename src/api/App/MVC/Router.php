<?php

namespace App\MVC;

use App\HTTP;
use App\JsonApi\Document\Errors;
use App\JsonApi\JsonApiException;
use App\MVC\Router\Route;
use App\MVC\Router\RouterGroup;


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


    public function mount(RouterGroup $routerGroup) {
        $this->routes = array_merge_recursive($this->routes, $routerGroup->getRoutes());
    }


    public function before($callable) {
        $this->before = $callable;
    }

    public function after($callable) {
        $this->after = $callable;
    }


    public function run($uri) {
        if (isset($_POST['REQUEST_METHOD'])) {
            $REQUEST_METHOD = $_POST['REQUEST_METHOD'];
        } else {
            $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
        }

        if(!isset($this->routes[$REQUEST_METHOD])) {
            throw new Errors(
                new JsonApiException(
                    null,
                    null,
                    HTTP::CODE_METHOD_NOT_ALLOWED,
                    null,
                    "REQUEST_METHOD does not exist",
                    "REQUEST_METHOD does not exist",
                    null,
                    null,
                    null
                )
            );
        }

        foreach($this->routes[$REQUEST_METHOD] as $route) {
            if (!$route instanceof Route)
                continue;

            if($route->match($uri)) {
                return $route->call();
            }
        }

        throw new Errors(
            new JsonApiException(
                null,
                null,
                HTTP::CODE_NOT_FOUND,
                null,
                "No matching routes",
                "Invalid URI",
                null,
                null,
                null
            )
        );
    }
}