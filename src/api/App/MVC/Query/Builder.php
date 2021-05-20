<?php

namespace App\MVC\Query;

use App\MVC\Model;
use App\MVC\Query;

class Builder {

    private $columns;
    private $table;
    private $joins = [];
    private $conditions;
    private $groupBy;
    private $having;
    private $orderBy;
    private $limit;
    private $offset;

    private $insertData = [];

    private $setClause = [];

    private $type;

    private $bindParams = [];
    private $bindTypes = [];

    private $model;


    public function __construct($params = null) {
        $this->type = Query::TYPE_SELECT;

        if (is_array($params)) {

            /*
             * Assign Conditions clause
             * */
            if (isset($params[0]))
                $this->conditions = $params[0];
            elseif (isset($params['conditions']) && is_string($params['conditions']))
                $this->conditions = $params['conditions'];
            elseif (isset($params['conditions']) && is_array($params['conditions']))
                $this->conditions = implode(' AND ', $params['conditions']);


            /*
             * Assign Bind
             * */
            if (isset($params['bind']))
                $this->bindParams = $params['bind'];

            if (isset($params['bindTypes']))
                $this->bindTypes = $params['bindTypes'];


            /*
             * Assign Columns clause
             * */
            if (isset($params['columns']))
                $this->columns = $params['columns'];

            /*
             * Assign JOIN clause
             * */
            if (isset($params['joins']))
                $this->joins = $params['joins'];


            /*
             * Assign GROUP BY clause
             * */
            if (isset($params['group']))
                $this->groupBy = $params['group'];


            /*
             * Assign ORDER BY clause
             * */
            if (isset($params['order']))
                $this->orderBy = $params['order'];


            /*
             * Assign LIMIT, OFFSET clause
             * */
            if (isset($params['limit'])) {
                if (is_array($params['limit'])) {
                    $limit = $params['limit'][0];
                    if (is_int($limit))
                        $this->limit = $limit;

                    $offset = $params['limit'][1];
                    if (is_int($offset))
                        $this->offset = $offset;
                }
                else
                    $this->limit = $params['limit'];
            }


            /*
             * Assign OFFSET clause
             * */
            if (isset($params['offset']))
                $this->offset = $params['offset'];
        }
        elseif (is_string($params) && $params != "") {
            $this->conditions = $params;
        }
        elseif (is_int($params)) {
            $this->conditions = $params;
        }
    }


    public function getModel(): Model {
        return $this->model;
    }


    public function select() {
        $this->type = Query::TYPE_SELECT;

        return $this;
    }

    public function columns($columns) {
        $this->columns = $columns;

        return $this;
    }


    public function from($modelName, $alias = null) {
        $this->model = new $modelName;

        $this->table = $this->getModel()->getMetaData()->getSource();
        if (is_string($alias))
            $this->table .= " AS " . $alias;

        return $this;
    }

    public function fromModel($modelName, $alias = null) {
        $this->model = call_user_func($modelName.'::getInstance');

        $this->table = $this->getModel()->getMetaData()->getSource();
        if (is_string($alias))
            $this->table .= " AS " . $alias;

        return $this;
    }


    public function join($model, $conditions = null, $alias = null, $type = null) {
        $join = [
            'type' => $type,
            'model' => $model,
            'conditions' => $conditions,
            'alias' => $alias,
        ];

        $this->joins[] = $join;

        return $this;
    }

    public function innerJoin($model, $conditions = null, $alias = null) {
        $this->join($model, $conditions, $alias, 'INNER');

        return $this;
    }

    public function crossJoin($model, $conditions = null, $alias = null) {
        $this->join($model, $conditions, $alias, 'CROSS');

        return $this;
    }

    public function leftJoin($model, $conditions = null, $alias = null) {
        $this->join($model, $conditions, $alias, 'LEFT');

        return $this;
    }

    public function rightJoin($model, $conditions = null, $alias = null) {
        $this->join($model, $conditions, $alias, 'RIGHT');

        return $this;
    }

    public function fullJoin($model, $conditions = null, $alias = null) {
        $this->join($model, $conditions, $alias, 'FULL');

        return $this;
    }

    public function selfJoin($model, $conditions = null, $alias = null) {
        $this->join($model, $conditions, $alias, 'SELF');

        return $this;
    }

    public function naturalJoin($model, $conditions = null, $alias = null) {
        $this->join($model, $conditions, $alias, 'NATURAL');

        return $this;
    }

    public function unionJoin($model, $conditions = null, $alias = null) {
        $this->join($model, $conditions, $alias, 'UNION');

        return $this;
    }


    public function where($conditions, $bindParams = null, $bindTypes = null) {
        $this->conditions = $conditions;

        if (is_array($bindParams)) {
            if (is_array($this->bindParams))
                $this->bindParams = $this->bindParams + $bindParams;
            else
                $this->bindParams = $bindParams;
        }

        if (is_array($bindTypes)) {
            if (is_array($this->bindTypes))
                $this->bindTypes = $this->bindTypes + $bindTypes;
            else
                $this->bindTypes = $bindTypes;
        }

        return $this;
    }

    public function andWhere($conditions, $bindParams = null, $bindTypes = null) {
        if (!empty($this->conditions))
            $conditions = '(' . $this->conditions . ') AND (' . $conditions . ')';

        $this->where($conditions, $bindParams, $bindTypes);

        return $this;
    }

    public function orWhere($conditions, $bindParams = null, $bindTypes = null) {
        if (!empty($this->conditions))
            $conditions = '(' . $this->conditions . ') OR (' . $conditions . ')';

        $this->where($conditions, $bindParams, $bindTypes);

        return $this;
    }


    public function groupBy($groupBy) {
        $this->groupBy = $groupBy;

        return $this;
    }


    public function having($having) {
        $this->having = $having;

        return $this;
    }

    public function andHaving($having) {
        if (!empty($this->having))
            $having = '(' . $this->having . ') AND (' . $having . ')';

        $this->having($having);

        return $this;
    }

    public function orHaving($having) {
        if (!empty($this->having))
            $having = '(' . $this->having . ') OR (' . $having . ')';

        $this->having($having);

        return $this;
    }


    public function orderBy($orderBy) {
        $this->orderBy = $orderBy;

        return $this;
    }


    public function limit($limit, $offset = null) {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function offset($offset) {
        $this->offset = $offset;

        return $this;
    }



    public function insert($table) {
        $this->type = Query::TYPE_INSERT;

        $this->table = $table;

        return $this;
    }

    public function values($values) {
        $this->insertData = $values;

        return $this;
    }

    public function setValues($key, $value) {
        $this->insertData[$key] = $value;

        return $this;
    }



    public function update($table) {
        $this->type = Query::TYPE_UPDATE;

        $this->table = $table;

        return $this;
    }

    public function set($key, $value) {
        $this->setClause[$key] = $value;

        return $this;
    }



    public function delete($table) {
        $this->type = Query::TYPE_DELETE;

        $this->table = $table;

        return $this;
    }




    public function setParameter($key, $value, $type = null) {
        $this->bindParams[$key] = $value;

        if ($type != null)
            $this->bindTypes[$key] = $type;

        return $this;
    }



    public function getQuery(): Query {
        $query = new Query($this->model);

        $query->setSql($this->getSQL());

        if (is_array($this->bindParams))
            $query->setBindParams($this->bindParams);

        if (is_array($this->bindTypes))
            $query->setBindTypes($this->bindTypes);

        return $query;
    }


    private function getSQL() {
        switch ($this->type) {
            case Query::TYPE_SELECT:
                return $this->getSQL_SELECT();
                break;

            case Query::TYPE_INSERT:
                break;

            case Query::TYPE_UPDATE:
                break;

            case Query::TYPE_DELETE:
                break;
        }

        return "";
    }

    private function getSQL_SELECT() {
        $columnMap = $this->getModel()->getMetaData()->getColumnMap();

        $sql = 'SELECT ';

        if (is_array($this->columns)) {
            $selectedColumn = [];

            foreach ($this->columns as $columnAlias => $column) {
                $column = $columnMap[$column] ?? $column;

                if (is_int($columnAlias))
                    $selectedColumn[] = $column;
                else
                    $selectedColumn[] = $column . ' AS ' . $columnAlias;
            }

            $sql .= implode(', ', $selectedColumn);
        }
        elseif (is_string($this->columns))
            $sql .= $columnMap[$this->columns] ?? $this->columns;
        else
            $sql .= '* ';

        $sql .= ' FROM ' . $this->table;

        if (is_array($this->joins)) {
            foreach ($this->joins as $join) {
                $modelInstance = new $join['model'];
                if (!$modelInstance instanceof Model) return null;

                if (isset($join['type']))
                    $sql .= " " . $join['type'] . " JOIN " . $modelInstance->getMetaData()->getSource();
                else
                    $sql .= " JOIN " . $modelInstance->getMetaData()->getSource();

                if (isset($join['alias']))
                    $sql .= " AS " . $join['alias'];

                if (isset($join['conditions']))
                    $sql .= " ON " . $join['conditions'];
            }
        }

        if (is_numeric($this->conditions)) {
            $primaryKey = $this->getModel()->getMetaData()->getPrimaryKey();

            $primaryKeyField = $columnMap[$primaryKey] ?? $primaryKey;

            $this->conditions = $primaryKeyField . " = " . $this->conditions;
        }

        if (is_string($this->conditions) && !empty($this->conditions)) {
            $sql .= ' WHERE ' . $this->conditions;
        }

        if (is_array($this->orderBy)) {
            $orderBy = [];

            foreach ($this->orderBy as $column) {
                preg_match('@^(.+?)\s*?(DESC|ASC)?$@i', $column, $orderItem);

                if (isset($orderItem[1])) {
                    $columnField = $columnMap[$orderItem[1]] ?? $orderItem[1];

                    $orderBy[] = $columnField . ' ' . ($orderItem[2] ?? 'ASC');
                } else {
                    $orderBy[] = $column;
                }
            }

            $sql .= ' ORDER BY ' . implode(', ', $orderBy);
        }
        elseif (is_string($this->orderBy)) {
            preg_match('@^(.+?)\s*?(DESC|ASC)?$@i', $this->orderBy, $orderItem);

            if (isset($orderItem[1])) {
                $columnField = $columnMap[$orderItem[1]] ?? $orderItem[1];

                $orderBy = $columnField . ' ' . ($orderItem[2] ?? 'ASC');

                $sql .= ' ORDER BY ' . $orderBy;
            }
        }


        if (is_int($this->limit)) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        if (is_int($this->offset)) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    private function getSQL_INSERT() {

    }

    private function getSQL_UPDATE() {

    }

    private function getSQL_DELETE() {

    }
}