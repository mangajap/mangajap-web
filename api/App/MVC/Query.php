<?php
/**
 * Created by PhpStorm.
 * User: Stant
 * Date: 17/04/2020
 * Time: 15:44
 */

namespace App\MVC;

use App\MVC\Query\Result;

class Query {

    const TYPE_SELECT = 0;
    const TYPE_INSERT = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    private $model;
    private $type;

    private $sql;
    private $bindParams;
    private $bindTypes;

    private $isUniqueRow = false;
    private $hydrateMode;

    public function __construct(Model $model) {
        $this->model = $model;
    }


    public function getSQL() {
        return $this->sql;
    }

    public function setSql($sql) {
        $this->sql = $sql;
    }

    public function getBindParams() {
        return $this->bindParams;
    }

    public function setBindParams($bindParams) {
        $this->bindParams = $bindParams;
    }

    public function getBindTypes() {
        return $this->bindTypes;
    }

    public function setBindTypes($bindTypes) {
        $this->bindTypes = $bindTypes;
    }


    public function isUniqueRow() {
        return $this->isUniqueRow;
    }

    public function setIsUniqueRow($isUniqueRow) {
        $this->isUniqueRow = $isUniqueRow;
    }

    public function setHydrateMode($hydrateMode) {
        $this->hydrateMode = $hydrateMode;
    }


    private function _executeSelect(): Result {
        $data = $this->model->getReadConnection()->query(
            $this->sql,
            $this->bindParams,
            $this->bindTypes
        );

        return new Result(
            $this->model,
            $data,
            $this->hydrateMode
        );
    }


    public function execute(): Result {
        switch ($this->type) {
            case self::TYPE_SELECT:
                $result = $this->_executeSelect();
                break;

            case self::TYPE_INSERT:
                return null;
                break;

            case self::TYPE_UPDATE:
                return null;
                break;

            case self::TYPE_DELETE:
                return null;
                break;

            default:
                return null;
                break;
        }


        return $result;
    }

}