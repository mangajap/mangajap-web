<?php
/**
 * Created by PhpStorm.
 * User: Stant
 * Date: 18/04/2020
 * Time: 19:18
 */

namespace App\MVC\Query;

use App\JsonApi\Document\PrimaryData\ResourceCollection;
use App\MVC\Model;
use Countable;
use Iterator;

class Result implements Iterator, Countable {

    const HYDRATE_MODEL = 0;
    const HYDRATE_OBJECT = 1;
    const HYDRATE_ARRAY = 2;

    private $index;

    private $model;
    private $data;
    private $count;

    private $hydrateMode;

    private $foundRows = 0;


    public function __construct(Model $model, $data, $hydrateMode) {
        $this->model = $model;
        $this->data = $data;
        $this->hydrateMode = $hydrateMode;

        $this->count = count($this->data);
    }

    public function getModel() {
        return $this->model;
    }

    public function getData() {
        return $this->data;
    }

    public function getHydrateMode() {
        return $this->hydrateMode;
    }

    public function setHydrateMode($hydrateMode) {
        $this->hydrateMode = $hydrateMode;
    }


    public function getFoundRows() {
        return (int) $this->foundRows;
    }

    public function setFoundRows($foundRows) {
        $this->foundRows = $foundRows;
    }


    public function getFirst() {
        if ($this->count == 0)
            return null;

        $this->seek(0);

        return $this->current();
    }

    public function get($index) {
        $this->seek($index);

        return $this->current();
    }

    public function getLast() {
        if ($this->count == 0)
            return null;

        $this->seek($this->count - 1);

        return $this->current();
    }


    public function seek($index) {
        $this->index = $index;
    }

    public function current() {
        $row = $this->data[$this->index];

        $reverseColumnMap = $this->model->getMetaData()->getReverseColumnMap();

        switch ($this->hydrateMode) {
            case self::HYDRATE_MODEL:
                return Model::cloneResult(
                    $this->model,
                    $row,
                    $reverseColumnMap
                );
                break;

            case self::HYDRATE_ARRAY:
                return Model::cloneResultArray(
                    $row,
                    $reverseColumnMap
                );
                break;

            case self::HYDRATE_OBJECT:
                return Model::cloneResultObject(
                    $row,
                    $reverseColumnMap
                );
                break;

            default:
                return Model::cloneResultObject(
                    $row,
                    $reverseColumnMap
                );
        }
    }

    public function next() {
        $this->seek($this->index + 1);
    }

    public function key() {
        if (!$this->valid())
            return null;

        return $this->index;
    }

    public function valid() {
        return isset($this->data[$this->index]);
    }

    public function rewind() {
        $this->seek(0);
    }


    public function count() {
        return $this->count;
    }



    public function toArray() : array {
        return iterator_to_array($this);
    }

    public function toJsonApi() {
        $records = [];

        $this->rewind();

        while ($this->valid()) {
            $current = $this->current();

            if ($current instanceof Model)
                $records[] = $current->toJsonApi();

            $this->next();
        }

        return new ResourceCollection(...$records);
    }
}