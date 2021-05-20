<?php

namespace App\Utils\JSON;

use App\Utils\JSON;
use Countable;

class JSONArray extends JSON implements Countable {

    private $obj = array();

    public function __construct($json = "") {
        $this->obj = JSON::decode($json);

        return $this;
    }


    public function has($index): bool {
        if (array_key_exists($index, $this->obj))
            return true;
        else
            return false;
    }


    public function opt($index, $fallback = 'null') {
        if ($this->has($index))
            return $this->obj[$index];
        else
            return $fallback;
    }

    public function optString($index, $fallback = 'null') {
        if ($this->has($index))
            return (string) $this->obj[$index];
        else
            return $fallback;
    }

    public function optInt($index, $fallback = 'null') {
        if ($this->has($index))
            return (int) $this->obj[$index];
        else
            return $fallback;
    }

    public function optBoolean($index, $fallback = 'null') {
        if ($this->has($index))
            return (bool) $this->obj[$index];
        else
            return $fallback;
    }

    public function optJSONObject($index, $fallback = null): JSONObject {
        if ($this->has($index))
            return new JSONObject(json_encode($this->obj[$index]));
        else
            return $fallback;
    }

    public function optJsonArray($index, $fallback = null):JSONArray {
        if ($this->has($index))
            return new JSONArray(json_encode($this->obj[$index]));
        else
            return $fallback;
    }


    public function put($value) {
        $this->obj[] = $value;

        return $this;
    }


    public function count() {
        return count($this->obj);
    }
}