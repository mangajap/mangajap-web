<?php

class Route {

    private $path;
    private $callable;
    private $matches;

    public function __construct($path, $callable){
        $this->path = trim($path, '/');  // On retire les / inutiles
        $this->callable = $callable;
    }


    public function match($url){
        $url = trim($url, '/');

        $path = preg_replace_callback('@{([\w]+):?(.*?)}@', [$this, 'paramWatch'], $this->path);
        $regex = "#^$path$#i";

        if(!preg_match($regex, $url, $matches)){
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;  // On sauvegarde les paramètre dans l'instance pour plus tard
        return true;
    }

    private function paramWatch($match) {
        if(!empty($match[2])) {
            return '(' . $match[2] . ')';
        }

        return '([^/]+)';
    }

    public function call(){
        return call_user_func_array($this->callable, $this->matches);
    }
}