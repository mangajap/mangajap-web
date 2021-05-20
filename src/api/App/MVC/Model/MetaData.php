<?php
/**
 * Created by PhpStorm.
 * User: Stant
 * Date: 19/04/2020
 * Time: 13:14
 */

namespace App\MVC\Model;


class MetaData {

    private $model;

    private $source;
    private $columnMap = [];
    private $primaryKey;
    private $primaryKeys = [];
    private $attributes = [];
    private $dataTypes = [];

    private $attributesSkippedOnCreate = [];
    private $attributesSkippedOnUpdate = [];

    public function __construct($model) {
        $this->model = $model;

        return $this;
    }


    public function getSource() {
        if (!isset($this->source))
            $this->setSource(strtolower(get_class($this->model)));

        return $this->source;
    }

    public function setSource($source) {
        $this->source = $source;

        return $this;
    }

    public function getColumnMap() {
        return $this->columnMap;
    }

    public function getReverseColumnMap() {
        return array_flip($this->columnMap);
    }

    public function setColumnMap($columnMap) {
        $this->columnMap = $columnMap;

        return $this;
    }

    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    public function setPrimaryKey($primaryKey) {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    public function getAttributes() {
        if (!empty($this->attributes))
            return $this->attributes;

        $properties = get_object_vars($this->model);

        return array_keys($properties);
    }

    public function setAttributes(array $attributes) {
        $this->attributes = [];

        foreach ($attributes as $key => $value) {
            if (is_int($key))
                $this->attributes[] = $value;
            else {
                $this->attributes[] = $key;
                $this->dataTypes[$key] = $value;
            }
        }

        return $this;
    }

    public function getDataTypes() {
        return $this->dataTypes;
    }

    public function setDataTypes($dataTypes) {
        $this->dataTypes = $dataTypes;

        return $this;
    }

    public function getAttributesSkippedOnCreate() {
        return $this->attributesSkippedOnCreate;
    }

    public function setAttributesSkippedOnCreate($attributesSkippedOnCreate) {
        $this->attributesSkippedOnCreate = array_merge($this->attributesSkippedOnCreate, $attributesSkippedOnCreate);
    }

    public function getAttributesSkippedOnUpdate() {
        return $this->attributesSkippedOnUpdate;
    }

    public function setAttributesSkippedOnUpdate($attributesSkippedOnUpdate) {
        $this->attributesSkippedOnUpdate = array_merge($this->attributesSkippedOnUpdate, $attributesSkippedOnUpdate);
    }
}