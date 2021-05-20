<?php

namespace App;

use App\JsonApi\Document\Errors;
use App\JsonApi\Encoder;
use App\JsonApi\JsonApiException;

class JsonApi {

    public static function encode($data) {
        $encoder = new Encoder();

        return $encoder
            ->encode($data);
    }


    public static function getLimit() {
        if (isset($_GET['page']['limit']))
            return (int) $_GET['page']['limit']; 
        
        return 10;
    }

    public static function getOffset() {
        if (isset($_GET['page']['offset']))
            return (int) $_GET['page']['offset'];

        return 0;
    }

    public static function getSort() {
        if (!isset($_GET['sort']))
            return null;

        $orderBy = [];

        foreach (explode(',', $_GET['sort']) as $sort) {
            preg_match('@^(-)?([a-zA-Z0-9-_]+)$@', $sort, $orderItem);

            if (!isset($orderItem[2])) {
                throw new Errors(
                    new JsonApiException(
                        null,
                        null,
                        HTTP::CODE_BAD_REQUEST,
                        null,
                        "Invalid sort criteria",
                        $sort." is not a valid sort criteria",
                        null,
                        null,
                        null
                    )
                );
            }

            $field = $orderItem[2];
            $order = (!empty($orderItem[1]) ? 'DESC' : 'ASC');

            switch ($field) {
                case 'random':
                    $field = 'RAND()';
                    $order = '';
                    break;
            }

            $orderBy[] = $field . ' ' . $order;
        }

        return $orderBy;
    }

    public static function getInclude() {
        if (isset($_GET['include']))
            return explode(',', $_GET['include']);

        return [];
    }

    public static function getFields() {
        if (!isset($_GET['fields']) || !is_array($_GET['fields']))
            return [];

        $fields = [];
        foreach ($_GET['fields'] as $key => $attributes) {
            $fields[$key] = explode(',', $attributes);
        }

        return $fields;
    }
    
    public static function getFilter() {
        if (!isset($_GET['filter']) || !is_array($_GET['filter']))
            return [];

        $filter = [];
        foreach ($_GET['filter'] as $key => $values) {
            $filter[$key] = explode(',', $values);
        }

        return $filter;
    }


    public static function getParameters() {
        $parameters = [];

        if (!empty(JsonApi::getLimit()))
            $parameters['limit'] = JsonApi::getLimit();
        if (!empty(JsonApi::getOffset()))
            $parameters['offset'] = JsonApi::getOffset();
        if (!empty(JsonApi::getSort()))
            $parameters['order'] = JsonApi::getSort();
        if (!empty(JsonApi::getFilter()))
            $parameters['filter'] = JsonApi::getFilter();

        return $parameters;
    }
}