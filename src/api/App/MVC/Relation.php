<?php
/**
 * Created by PhpStorm.
 * User: Stant
 * Date: 20/04/2020
 * Time: 15:09
 */

namespace App\MVC;


class Relation {

    const HAS_ONE = 0;
    const HAS_MANY = 1;
    const HAS_MANY_TO_MANY = 2;
    const BELONGS_TO = 3;

    private $type;
    private $fields;
    private $intermediateModel;
    private $intermediateFields;
    private $intermediateReferenceFields;
    private $referenceModel;
    private $referenceFields;
    private $options;


    public function __construct($type, $fields, $referenceModel, $referenceFields, $options = null) {
        $this->type = $type;
        $this->fields = $fields;
        $this->referenceModel = $referenceModel;
        $this->referenceFields = $referenceFields;
        $this->options = $options;

        if (count($this->getFields()) != count($this->getReferenceFields()))
            return null; // throw error
    }

    public function setIntermediateRelation($intermediateModel, $intermediateFields, $intermediateReferenceFields) {
        $this->intermediateModel = $intermediateModel;
        $this->intermediateFields = $intermediateFields;
        $this->intermediateReferenceFields = $intermediateReferenceFields;

        if (count($this->getIntermediateFields()) != count($this->getFields()))
            return null; // throw error
    }


    public function getType() {
        return $this->type;
    }

    public function isMany() {
        return ($this->type == self::HAS_MANY);
    }

    public function isOne() {
        return ($this->type == self::HAS_ONE);
    }

    public function isManyToMany() {
        return ($this->type == self::HAS_MANY_TO_MANY);
    }

    public function isBelongsTo() {
        return ($this->type == self::BELONGS_TO);
    }

    public function isThrough() {
        return ($this->type == self::HAS_MANY_TO_MANY);
    }

    public function getFields() {
        if (is_array($this->fields))
            return $this->fields;
        else
            return [$this->fields];
    }

    public function getIntermediateModelName() {
        return $this->intermediateModel;
    }

    public function getIntermediateModel(): Model {
        return (new $this->intermediateModel);
    }

    public function getIntermediateFields() {
        if (is_array($this->intermediateFields))
            return $this->intermediateFields;
        else
            return [$this->intermediateFields];
    }

    public function getIntermediateReferenceFields() {
        if (is_array($this->intermediateReferenceFields))
            return $this->intermediateReferenceFields;
        else
            return [$this->intermediateReferenceFields];
    }

    public function getReferenceModelName() {
        return $this->referenceModel;
    }

    public function getReferenceModel(): Model {
        return (new $this->referenceModel);
    }

    public function getReferenceFields() {
        if (is_array($this->referenceFields))
            return $this->referenceFields;
        else
            return [$this->referenceFields];
    }


    public function getParams() {
        if (isset($this->options['params']))
            return $this->options['params'];
        else
            return [];
    }
}