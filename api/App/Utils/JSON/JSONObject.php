<?php

namespace App\Utils\JSON;

use App\Utils\JSON;

class JSONObject extends JSON {

    private $obj;

    public function __construct($json = "") {
        $this->obj = JSON::decode($json);

        return $this;
    }


    public function has($name): bool {
        if (property_exists($this->obj, $name))
            return true;
        else
            return false;
    }


    public function opt($name, $fallback = 'null') {
        if ($this->has($name))
            return $this->obj->{$name};
        else
            return $fallback;
    }

    public function optString($name, $fallback = 'null') {
        if ($this->has($name))
            return (string) $this->obj->{$name};
        else
            return $fallback;
    }

    public function optInt($name, $fallback = 'null') {
        if ($this->has($name))
            return (int) $this->obj->{$name};
        else
            return $fallback;
    }

    public function optBoolean($name, $fallback = 'null') {
        if ($this->has($name))
            return (bool) $this->obj->{$name};
        else
            return $fallback;
    }

    public function optArray($name, $fallback = 'null') {
        if ($this->has($name))
            return (array) $this->obj->{$name};
        else
            return $fallback;
    }

    public function optJSONObject($name, $fallback = null): JSONObject {
        if ($this->has($name))
            return new JSONObject(json_encode($this->obj->{$name}));
        else
            return $fallback;
    }

    public function optJsonArray($name, $fallback = null): JSONArray {
        if ($this->has($name))
            return new JSONArray(json_encode($this->obj->{$name}));
        else
            return $fallback;
    }


    public function put($name, $value) {
        $this->obj->{$name} = $value;

        return $this;
    }


    public function keys() {
        return array_keys((array) $this->obj);
    }
}