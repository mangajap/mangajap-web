<?php

namespace App;

use App\HTTP\Request;
use App\MVC\Router;

class Application {

    private static $default;

    private $services = [];

    public function __construct(){
        if (!self::$default)
            self::$default = $this;
    }


    public static function getDefault() {
        return self::$default;
    }

    public static function setDefault(Application $default) {
        self::$default = $default;
    }

    public function reset() {
        self::$default = null;
    }


    public function get($name) {
        return $this->services[$name];
    }

    public function set($name, $callable) {
        $this->services[$name] = call_user_func($callable);

        return $this->services[$name];
    }

    public function setDatabase($name, $database) {
        $this->services[$name] = $database;

        return $this->services[$name];
    }

    public function setRouter($router) {
        $this->services['router'] = $router;

        return $this->services['router'];
    }


    public function getRequest(): Request {
        return new Request();
    }


    public function run() {
        $router = $this->get('router');

        // TODO: Faire un try catch qui convertira l'exception en jsonApi
        // Pour spécifier la reponse en sortie faire comme retrofit : addConverter(JsonApiEncoder..
        if ($router instanceof Router) {
            $response = $router->run($this->getRequest()->getURI());

            return $response;
        }

        return '';
    }
}