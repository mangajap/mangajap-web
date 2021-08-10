<?php

namespace App\MVC;

use App\Application;
use App\Database;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\LinksObject;
use App\JsonApi\Document\LinksObject\Link\RelatedLink;
use App\JsonApi\Document\LinksObject\Link\SelfLink;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\Document\PrimaryData\ResourceObject;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model\MetaData;
use App\MVC\Query\Builder;
use App\MVC\Query\Result;
use App\Utils\JSON\JSONObject;
use App\Utils\URL;

abstract class Model {

    private $readConnection;
    private $writeConnection;

    private $relations = [];
    private $dirtyAttributes = [];
    private $dirtyRelated = [];

    private $defaultProperties = [];

    private $dynamicUpdate = false;

    private $metaData;


    public function __construct($data = null) {
        $this->initialize();
    }


    public function initialize() {
    }

    public static function getInstance() {
        $instance = new static;

        $instance->initialize();

        return $instance;
    }


    public function getApplication(): Application {
        return Application::getDefault();
    }


    public function setConnectionService($service) {
        $this->setReadConnectionService($service);
        $this->setWriteConnectionService($service);
    }

    public function setReadConnectionService($service) {
        $connection = $this->getApplication()->get($service);
        $this->setReadConnection($connection);
    }

    public function setWriteConnectionService($service) {
        $connection = $this->getApplication()->get($service);
        $this->setWriteConnection($connection);
    }

    public function setConnection(Database $connection) {
        $this->setReadConnection($connection);
        $this->setWriteConnection($connection);
    }

    public function getReadConnection(): Database {
        return $this->readConnection;
    }

    public function setReadConnection(Database $readConnection) {
        $this->readConnection = $readConnection;
    }

    public function getWriteConnection(): Database {
        return $this->writeConnection;
    }

    public function setWriteConnection(Database $writeConnection) {
        $this->writeConnection = $writeConnection;
    }


    public function getMetaData(): MetaData {
        if ($this->metaData == null)
            $this->metaData = new MetaData($this);

        return $this->metaData;
    }

    public function setSource($source) {
        $this->getMetaData()->setSource($source);

        return $this;
    }

    public function setColumnMap($columnMap) {
        $this->getMetaData()->setColumnMap($columnMap);

        return $this;
    }

    public function setPrimaryKey($primaryKey) {
        $this->getMetaData()->setPrimaryKey($primaryKey);

        return $this;
    }

    public function setAttributes(array $attributes) {
        $this->getMetaData()->setAttributes($attributes);

        return $this;
    }

    public function setDataTypes($dataTypes) {
        $this->getMetaData()->setDataTypes($dataTypes);

        return $this;
    }


    public function useDynamicUpdate(bool $dynamicUpdate) {
        $this->dynamicUpdate = $dynamicUpdate;
    }

    public function isUsingDynamicUpdate() {
        return $this->dynamicUpdate;
    }

    private function readAttribute($attribute) {
        $getter = 'get' . ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $attribute))));

        if (is_callable(array($this, $getter)))
            return $this->$getter();

        $getter = $attribute;
        if (is_callable(array($this, $getter)))
            return $this->$getter();

        $getter = 'is' . ucfirst($attribute);
        if (is_callable(array($this, $getter)))
            return $this->$getter();


        return $this->{$attribute};
    }

    public function writeAttribute($attribute, $value) {
        $this->{$attribute} = $value;
    }

    public function setAttribute($attribute, $value) {
        $setter = 'set' . ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $attribute))));

        if (is_callable(array($this, $setter)))
            $this->$setter($value);
        else
            $this->{$attribute} = $value;
    }


    public function skipAttributes(array $attributes) {
        $this->skipAttributesOnCreate($attributes);
        $this->skipAttributesOnUpdate($attributes);
    }

    public function skipAttributesOnCreate(array $attributes) {
        $this->getMetaData()->setAttributesSkippedOnCreate($attributes);
    }

    public function skipAttributesOnUpdate(array $attributes) {
        $this->getMetaData()->setAttributesSkippedOnUpdate($attributes);
    }


    public function dump() {
        return get_object_vars($this);
    }


    public static function query(): Builder {
        $builder = new Builder();

        $builder->fromModel(
            get_called_class()
        );

        return $builder;
    }


    public static function getList($params = null) {
        $query = static::getPreparedQuery($params);

        /*
         * Assign HydrateMode clause
         * */
        if (isset($params['hydration']))
            $query->setHydrateMode($params['hydration']);

        $result = $query->execute();

        unset($params['limit']);
        unset($params['offset']);
        $result->setFoundRows(static::count($params));

        return $result;
    }

    public static function get($params = null) {
        $query = static::getPreparedQuery($params);

        $query->setIsUniqueRow(true);

        /*
         * Assign HydrateMode clause
         * */
        if (isset($params['hydration']))
            $query->setHydrateMode($params['hydration']);

        $result = $query->execute();

        $model = $result->getFirst();

        if ($model instanceof Model) {
            $model->afterGet();
        }

        return $model;
    }

    public function afterGet() {
    }

    private static function processFilter($model, &$params) {
        if (isset($params['filter'])) {
            foreach ($params['filter'] as $field => $values) {
                $filters = $model->JsonApi_filter();

                if (isset($filters[$field])) {
                    $filter_first = array_merge_recursive($filters[$field], $params);
                    $jsonApi_first = array_merge_recursive($params, $filters[$field]);
                    $params = array_replace_recursive($params, $filters[$field]);
                    if (isset($jsonApi_first['conditions']))
                        $params['conditions'] = $jsonApi_first['conditions'];
                    if (isset($jsonApi_first['order']))
                        $params['order'] = $filter_first['order'];

                    if (!isset($params['bind'])) // self ne doit pas s'ajouter dans la requete
                        $params['bind'][$field] = implode('', $values);
                }

                elseif (in_array($field, $filters)) {
                    $columnMap = $model->getMetaData()->getColumnMap();

                    foreach ($values as $key => $value) {
                        $attributeField = $columnMap[$field] ?? $field;

                        $params['conditions'][] = $attributeField . ' = ' . ':'.$field.$key;
                        $params['bind'][$field.$key] = $value;
                    }
                }

                else
                    return null; // throw error
            }
        }
    }

    private static function getPreparedQuery(&$params): Query {
        $model = static::getInstance();
        if ($model instanceof JsonApiSerializable && $model instanceof Model && isset($params['filter'])) {
            self::processFilter($model, $params);
        }

        $builder = new Builder($params);

        $builder->fromModel(
            get_called_class()
        );

        $query = $builder->getQuery();

        return $query;
    }


    public static function count($params = null) {
        return static::_functionQuery('COUNT', 'rowcount', $params);
    }

    public static function maximum($params = null) {
        return static::_functionQuery('MAX', 'maximum', $params);
    }

    public static function minimum($params = null) {
        return static::_functionQuery('MIN', 'minimum', $params);
    }

    public static function sum($params = null) {
        return static::_functionQuery('SUM', 'sumatory', $params);
    }

    public static function average($params = null) {
        return static::_functionQuery('AVG', 'average', $params);
    }

    private static function _functionQuery($functionName, $alias, $parameters) {
        if (is_array($parameters)) {
            $params = $parameters;
        } else {
            if ($parameters != null)
                $params = [$parameters];
            else
                $params = [];
        }

        if (!isset($params['columns']))
            $params['columns'] = '*';

        if (isset($params['distinct']))
            $columns = $functionName . "(DISTINCT " . $params['distinct'] . ") AS " . $alias;
        elseif (isset($params['group']))
            $columns = $params['group'] . ', ' . $functionName . '(' . $params['columns'] . ') AS ' . $alias;
        else
            $columns = $functionName . '(' . $params['columns'] . ') AS ' . $alias;



        $builder = new Builder($params);

        $builder->columns($columns);

        $builder->fromModel(
            get_called_class()
        );

        $query = $builder->getQuery();

        $result = $query->execute();

        if (isset($params['group']))
            return $result;

        return $result->getFirst()->{$alias};
    }


    private function addRelation($type, $field, $referenceModel, $referenceFields, $options = null): Relation {
        $relation = new Relation(
            $type,
            $field,
            $referenceModel,
            $referenceFields,
            $options
        );

        $alias = $options['alias'] ?? $referenceModel;

        $this->relations[$alias] = $relation;

        return $relation;
    }

    public function hasOne($field, $referenceModel, $referenceFields, $options = null): Relation {
        return $this->addRelation(
            Relation::HAS_ONE,
            $field,
            $referenceModel,
            $referenceFields,
            $options
        );
    }

    public function hasMany($field, $referenceModel, $referenceFields, $options = null): Relation {
        return $this->addRelation(
            Relation::HAS_MANY,
            $field,
            $referenceModel,
            $referenceFields,
            $options
        );
    }

    public function hasManyToMany($field, $intermediateModel, $intermediateFields, $intermediateReferenceFields, $referenceModel, $referenceFields, $options = null): Relation {
        $relation = $this->addRelation(
            Relation::HAS_MANY_TO_MANY,
            $field,
            $referenceModel,
            $referenceFields,
            $options
        );
        $relation->setIntermediateRelation(
            $intermediateModel,
            $intermediateFields,
            $intermediateReferenceFields
        );

        return $relation;
    }

    public function belongsTo($field, $referenceModel, $referenceFields, $options = null): Relation {
        return $this->addRelation(
            Relation::BELONGS_TO,
            $field,
            $referenceModel,
            $referenceFields,
            $options
        );
    }

    public function getRelationByAlias($alias): Relation {
        if (!isset($this->relations[$alias])) {
            return null; // throw error
        }

        return $this->relations[$alias];
    }

    public function getRelated($alias, $params = []) {
        $relation = $this->getRelationByAlias($alias);

        self::processFilter($relation->getReferenceModel(), $params);
        $builder = new Builder(array_merge($relation->getParams(), $params));

        $metaData = $this->getMetaData();

        if ($relation->isOne()) {
            $fields = $relation->getFields();

            $referenceModelName = $relation->getReferenceModelName();
            $referenceFields = $relation->getReferenceFields();
            $referenceModel = $relation->getReferenceModel();
            $referenceColumnMap = $referenceModel->getMetaData()->getColumnMap();

            $builder->fromModel(
                $referenceModelName
            );

            $conditions = [];
            $bindParams = [];
            for ($i=0; $i<count($fields); $i++) {
                $field = $fields[$i];
                $referenceField = $referenceFields[$i];

                $referenceFieldAttribute = $referenceColumnMap[$referenceField] ?? $referenceField;

                $conditions[] = $referenceFieldAttribute . ' = ' . ':'.$field;
                $bindParams[$field] = $this->readAttribute($field);
            }

            $builder->andWhere(
                implode(' AND ', $conditions),
                $bindParams
            );

            $query = $builder->getQuery();
            $query->setIsUniqueRow(true);
        }
        elseif ($relation->isMany()) {
            $fields = $relation->getFields();

            $referenceModelName = $relation->getReferenceModelName();
            $referenceFields = $relation->getReferenceFields();
            $referenceModel = $relation->getReferenceModel();
            $referenceColumnMap = $referenceModel->getMetaData()->getColumnMap();

            $builder->fromModel(
                $referenceModelName
            );

            $conditions = [];
            $bindParams = [];
            for ($i=0; $i<count($fields); $i++) {
                $field = $fields[$i];
                $referenceField = $referenceFields[$i];

                $referenceFieldAttribute = $referenceColumnMap[$referenceField] ?? $referenceField;

                $conditions[] = $referenceFieldAttribute . ' = ' . ':'.$field;
                $bindParams[$field] = $this->readAttribute($field);
            }

            $builder->andWhere(
                implode(' AND ', $conditions),
                $bindParams
            );

            $query = $builder->getQuery();
        }
        elseif ($relation->isManyToMany()) {
            $fields = $relation->getFields();

            $intermediateModelName = $relation->getIntermediateModelName();
            $intermediateFields = $relation->getIntermediateFields();
            $intermediateReferenceFields = $relation->getIntermediateReferenceFields();
            $intermediateModel = $relation->getIntermediateModel();
            $intermediateColumnMap = $intermediateModel->getMetaData()->getColumnMap();

            $referenceModelName = $relation->getReferenceModelName();
            $referenceFields = $relation->getReferenceFields();
            $referenceModel = $relation->getReferenceModel();
            $referenceColumnMap = $referenceModel->getMetaData()->getColumnMap();



            $builder->fromModel(
                $referenceModelName
            );

            $joinsConditions = [];
            for ($i=0; $i<count($intermediateReferenceFields); $i++) {
                $intermediateFieldAttribute = $intermediateColumnMap[$intermediateReferenceFields[$i]] ?? $intermediateReferenceFields[$i];
                $referenceFieldAttribute = $referenceColumnMap[$referenceFields[$i]] ?? $referenceFields[$i];

                $joinsConditions[] = $intermediateFieldAttribute . ' = ' . $referenceFieldAttribute;
            }
            $builder->innerJoin(
                $intermediateModelName,
                implode(' AND ', $joinsConditions)
            );


            $conditions = [];
            $bindParams = [];
            for ($i=0; $i<count($fields); $i++) {
                $intermediateFieldAttribute = $intermediateColumnMap[$intermediateFields[$i]] ?? $intermediateFields[$i];

                $conditions[] = $intermediateFieldAttribute . ' = ' . ':'.$fields[$i];
                $bindParams[$fields[$i]] = $this->readAttribute($fields[$i]);
            }
            $builder->andWhere(
                implode(' AND ', $conditions),
                $bindParams
            );

            $query = $builder->getQuery();
        }
        elseif ($relation->isBelongsTo()) {
            $fields = $relation->getFields();
            $columnMap = $metaData->getColumnMap();

            $referenceModelName = $relation->getReferenceModelName();
            $referenceFields = $relation->getReferenceFields();
            $referenceModel = $relation->getReferenceModel();
            $referenceColumnMap = $referenceModel->getMetaData()->getColumnMap();

            $builder->fromModel(
                $referenceModelName
            );

            $joinsConditions = [];
            for ($i=0; $i<count($fields); $i++) {
                $field = $fields[$i];
                $referenceField = $referenceFields[$i];

                $fieldAttribute = $columnMap[$field] ?? $field;
                $referenceFieldAttribute = $referenceColumnMap[$referenceField] ?? $referenceField;

                $joinsConditions[] = $fieldAttribute . ' = ' . $referenceFieldAttribute;
            }
            $builder->innerJoin(
                get_called_class(),
                implode(' AND ', $joinsConditions)
            );


            $primaryKey = $metaData->getPrimaryKey();

            $primaryKeyField = $columnMap[$primaryKey] ?? $primaryKey;

            $conditions = $primaryKeyField . ' = ' . ':'.$primaryKey;
            $bindParams = [];
            $bindParams[$primaryKey] = $this->readAttribute($primaryKey);

            $builder->andWhere(
                $conditions,
                $bindParams
            );

            $query = $builder->getQuery();
            $query->setIsUniqueRow(true);
        }
        else
            return null; // throw error

        $query->setHydrateMode(Result::HYDRATE_MODEL);

        $result = $query->execute();

        if ($query->isUniqueRow()) {
            return $result->getFirst();
        } else {
            $builder->columns([
                'rowcount' => 'COUNT(*)',
            ]);
            $builder->limit(null, null);

            $result->setFoundRows($builder->getQuery()->execute()->getFirst()->rowcount);

            return $result;
        }
    }

    public function setRelated($alias, $value) {
        $this->dirtyRelated[$alias] = $value;
    }


    private function _exists(): bool {
        $metaData = $this->getMetaData();

        $columnMap = $metaData->getColumnMap();

        $bindParams = [];
        $bindTypes = [];

        $primaryKey = $metaData->getPrimaryKey();

        $primaryKeyField = $columnMap[$primaryKey] ?? $primaryKey;

        $conditions = $primaryKeyField . ' = ' . ':'.$primaryKey;
        $bindParams[$primaryKey] = $this->readAttribute($primaryKey);

        if (isset($metaData->getDataTypes()[$primaryKey]))
            $bindTypes[$primaryKey] = $metaData->getDataTypes()[$primaryKey];

        $data = $this->getReadConnection()->query(
            "SELECT * FROM ". $metaData->getSource() ." WHERE ". $conditions ." LIMIT 1",
            $bindParams,
            $bindTypes
        );

        if (isset($data[0]))
            return true;
        else
            return false;
    }


    public function beforeSave(): bool {
        return true;
    }

    private function _preSaveRelatedRecords(): bool {
        foreach ($this->dirtyRelated as $name => $record) {
            $relation = $this->getRelationByAlias($name);

            if ($relation->isBelongsTo()) {
                if (!$record instanceof Model)
                    return null; // throw error

                $fields = $relation->getFields();

                $referenceFields = $relation->getReferenceFields();

                for ($i=0; $i<count($fields); $i++) {
                    $this->setAttribute(
                        $fields[$i],
                        $record->readAttribute($referenceFields[$i])
                    );
                    $this->dirtyAttributes[$fields[$i]] = $record->readAttribute($referenceFields[$i]);
                }
            }
        }

        return true;
    }

    public function save(): bool {
        if ($this->_exists())
            $success = $this->update();
        else
            $success = $this->create();

        return $success;
    }

    private function _postSaveRelatedRecords(): bool {
        foreach ($this->dirtyRelated as $name => $record) {
            $relation = $this->getRelationByAlias($name);

            if ($relation->isBelongsTo())
                continue;


            if ($relation->isOne()) {
                $fields = $relation->getFields();

                $referenceFields = $relation->getReferenceFields();


                if (!$record instanceof Model)
                    return null; //throw error

                for ($i=0; $i<count($fields); $i++) {
                    $record->writeAttribute(
                        $referenceFields[$i],
                        $this->readAttribute($fields[$i])
                    );
                }

                if (!$record->save())
                    return false;
            }
            elseif ($relation->isMany()) {
                $fields = $relation->getFields();

                $referenceFields = $relation->getReferenceFields();


                if ($record instanceof Model)
                    $record = [$record];

                foreach ($record as $referenceModel) {
                    if (!$referenceModel instanceof Model)
                        return null; // throw error

                    for ($i=0; $i<count($fields); $i++) {
                        $referenceModel->writeAttribute(
                            $referenceFields[$i],
                            $this->readAttribute($fields[$i])
                        );
                    }

                    if (!$referenceModel->save())
                        return false;
                }
            }
            elseif ($relation->isManyToMany()) {
                $fields = $relation->getFields();

                $intermediateFields = $relation->getIntermediateFields();
                $intermediateReferenceFields = $relation->getIntermediateReferenceFields();

                $referenceFields = $relation->getReferenceFields();

                if ($record instanceof Model)
                    $record = [$record];

                foreach ($record as $referenceModel) {
                    if (!$referenceModel instanceof Model)
                        return null; // throw error

                    $intermediateModel = $relation->getIntermediateModel();

                    for ($i=0; $i<count($fields); $i++) {
                        $intermediateModel->writeAttribute(
                            $intermediateFields[$i],
                            $this->readAttribute($fields[$i])
                        );

                        $intermediateModel->writeAttribute(
                            $intermediateReferenceFields[$i],
                            $referenceModel->readAttribute($referenceFields[$i])
                        );
                    }

                    if (!$intermediateModel->save())
                        return false;
                }
            }
            else
                return null; // throw error
        }

        return true;
    }

    public function afterSave() {
    }


    public function beforeCreate(): bool {
        return true;
    }

    public function create(): bool {
        if ($this->_exists())
            return false; // throw error

        if (!$this->beforeSave())
            return false;
        if (!$this->beforeCreate())
            return false;

        $newProperties = [];
        foreach ($this->getMetaData()->getAttributes() as $attribute) {
            $newProperties[$attribute] = $this->readAttribute($attribute);
        }
        $this->dirtyAttributes = array_merge(array_diff_assoc($newProperties, $this->defaultProperties), $this->dirtyAttributes);

        if (!empty($this->dirtyRelated)) {
            if (!$this->_preSaveRelatedRecords())
                return false;
        }

        $metaData = $this->getMetaData();

        $columnMap = $metaData->getColumnMap();
        $attributesSkipped = $metaData->getAttributesSkippedOnCreate();

        $table = $metaData->getSource();
        $data = [];
        $bindTypes = [];

        $primaryKey = $metaData->getPrimaryKey();
        $primaryKeyField = $columnMap[$primaryKey] ?? $primaryKey;
        $fields[$primaryKeyField] = null;
        if (isset($metaData->getDataTypes()[$primaryKey]))
            $bindTypes[$primaryKeyField] = $metaData->getDataTypes()[$primaryKey];

        foreach ($metaData->getAttributes() as $attribute) {
            if (in_array($attribute, $attributesSkipped))
                continue;

            $attributeField = $columnMap[$attribute] ?? $attribute;

            if (!array_key_exists($attribute, $this->dirtyAttributes))
                continue;

            $data[$attributeField] = $this->readAttribute($attribute);

            if (isset($metaData->getDataTypes()[$attribute]))
                $bindTypes[$attributeField] = $metaData->getDataTypes()[$attribute];
        }

        $success = $this->getWriteConnection()->insert(
            $table,
            $data,
            $bindTypes
        );

        if ($success)
            $this->writeAttribute($metaData->getPrimaryKey(), $this->getWriteConnection()->lastInsertId());

        if (!empty($this->dirtyRelated) && $success)
            $success = $this->_postSaveRelatedRecords();

        if ($success) {
            if (!empty($this->dirtyAttributes))
                $this->dirtyAttributes = [];
            if (!empty($this->dirtyRelated))
                $this->dirtyRelated = [];

            $this->afterCreate();
        }

        return $success;
    }

    public function afterCreate() {
    }


    public function beforeUpdate(): bool {
        return true;
    }

    public function update(): bool {
        if (!$this->_exists())
            return false; // throw error

        if (!$this->beforeSave())
            return false;
        if (!$this->beforeUpdate())
            return false;

        $newProperties = [];
        foreach ($this->getMetaData()->getAttributes() as $attribute) {
            $newProperties[$attribute] = $this->readAttribute($attribute);
        }
        $this->dirtyAttributes = array_merge(array_diff_assoc($newProperties, $this->defaultProperties), $this->dirtyAttributes);

        if (!empty($this->dirtyRelated)) {
            if (!$this->_preSaveRelatedRecords())
                return false;
        }

        $metaData = $this->getMetaData();

        $columnMap = $metaData->getColumnMap();
        $attributesSkipped = $metaData->getAttributesSkippedOnUpdate();

        $table = $metaData->getSource();
        $data = [];
        $conditions = [];
        $bindTypes = [];

        if ($this->isUsingDynamicUpdate()) {
        }
        else {
            $primaryKey = $metaData->getPrimaryKey();
            $primaryKeyField = $columnMap[$primaryKey] ?? $primaryKey;
            $conditions[$primaryKeyField] = $this->readAttribute($primaryKey);
            if (isset($metaData->getDataTypes()[$primaryKey]))
                $bindTypes[$primaryKeyField] = $metaData->getDataTypes()[$primaryKey];

            foreach ($metaData->getAttributes() as $attribute) {
                if (in_array($attribute, $attributesSkipped))
                    continue;

                $attributeField = $columnMap[$attribute] ?? $attribute;

                if (!array_key_exists($attribute, $this->dirtyAttributes))
                    continue;

                $data[$attributeField] = $this->readAttribute($attribute);

                if (isset($metaData->getDataTypes()[$attribute]))
                    $bindTypes[$attributeField] = $metaData->getDataTypes()[$attribute];
            }
        }

        if (!empty($data)) {
            $success = $this->getWriteConnection()->update(
                $table,
                $data,
                $conditions,
                $bindTypes
            );
        } else {
            $success = true;
        }


        if (!empty($this->dirtyRelated) && $success)
            $success = $this->_postSaveRelatedRecords();

        if ($success) {
            if (!empty($this->dirtyAttributes))
                $this->dirtyAttributes = [];
            if (!empty($this->dirtyRelated))
                $this->dirtyRelated = [];

            $this->afterUpdate();
        }

        return $success;
    }

    public function afterUpdate() {
    }


    public function beforeDelete(): bool {
        return true;
    }

    public function delete(): bool {
        if (!$this->beforeDelete())
            return false;

        $metaData = $this->getMetaData();

        $columnMap = $metaData->getColumnMap();

        $table = $metaData->getSource();
        $conditions = [];
        $bindParams = [];
        $bindTypes = [];

        $primaryKey = $metaData->getPrimaryKey();
        $primaryKeyField = $columnMap[$primaryKey] ?? $primaryKey;
        $conditions[] = $primaryKeyField . ' = ' . ':'.$primaryKey;
        $bindParams[$primaryKey] = $this->readAttribute($primaryKey);
        if (isset($metaData->getDataTypes()[$primaryKey]))
            $bindTypes[$primaryKeyField] = $metaData->getDataTypes()[$primaryKey];

        $success = $this->getWriteConnection()->delete(
            $table,
            $conditions,
            $bindParams,
            $bindTypes
        );

        if ($success) {
            $this->afterDelete();
        }

        return $success;
    }

    public function afterDelete() {
    }



    public static function cloneResult(Model $model, array $data, $reverseColumnMap = null) {
        $instance = clone $model;

        $dataTypes = $instance->getMetaData()->getDataTypes();

        foreach ($data as $key => $value) {
            $attributeName = $reverseColumnMap[$key] ?? $key;

            if ($value !== null && isset($dataTypes[$attributeName])) {
                switch ($dataTypes[$attributeName]) {
                    case Column::TYPE_TINYINT:
                    case Column::TYPE_SMALLINT:
                    case Column::TYPE_MEDIUMINT:
                    case Column::TYPE_INT:
                    case Column::TYPE_BIGINT:
                        $value = intval($value);
                        break;

                    case Column::TYPE_DECIMAL:
                    case Column::TYPE_FLOAT:
                    case Column::TYPE_DOUBLE:
                    case Column::TYPE_REAL:
                        $value = doubleval($value);
                        break;

                    case Column::TYPE_BIT:
                    case Column::TYPE_BOOLEAN:
                    case Column::TYPE_SERIAL:
                        $value = boolval($value);
                        break;

                    case Column::TYPE_DATE:
                    case Column::TYPE_DATETIME:
                    case Column::TYPE_TIMESTAMP:
                    case Column::TYPE_TIME:
                    case Column::TYPE_YEAR:
                        $value = strval($value);
                        break;

                    case Column::TYPE_CHAR:
                    case Column::TYPE_VARCHAR:
                        $value = strval($value);
                        break;

                    case Column::TYPE_TINYTEXT:
                    case Column::TYPE_TEXT:
                    case Column::TYPE_MEDIUMTEXT:
                    case Column::TYPE_LONGTEXT:
                        $value = strval($value);
                        break;

                    case Column::TYPE_BINARY:
                    case Column::TYPE_VARBINARY:
                        $value = strval($value);
                        break;

                    case Column::TYPE_TINYBLOB:
                    case Column::TYPE_MEDIUMBLOB:
                    case Column::TYPE_BLOB:
                    case Column::TYPE_LONGBLOB:
                        $value = strval($value);
                        break;

                    case Column::TYPE_ENUM:
                    case Column::TYPE_SET:
                        $value = strval($value);
                        break;
                }
            }

            $instance->writeAttribute($attributeName, $value);
        }

        return $instance;
    }

    public static function cloneResultArray(array $data, $reverseColumnMap = null) {
        if (!is_array($reverseColumnMap))
            return $data;

        $result = [];

        foreach ($data as $key => $value) {

            if (is_array($reverseColumnMap)) {
                $attributeName = $reverseColumnMap[$key] ?? $key;

                $result[$attributeName] = $value;
            }
            else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public static function cloneResultObject(array $data, $reverseColumnMap = null) {
        $result = (object) [];

        foreach ($data as $key => $value) {

            if (is_array($reverseColumnMap)) {
                $attributeName = $reverseColumnMap[$key] ?? $key;

                $result->{$attributeName} = $value;
            }
            else {
                $result->{$key} = $value;
            }
        }

        return $result;
    }


    public function toJsonApi(): ResourceObject {
        if (!$this instanceof JsonApiSerializable)
            return null; // throw error

        $attributes = [];
        foreach ($this->JsonApi_attributes() as $name => $value) {
            if (isset(JsonApi::getFields()[$this->JsonApi_type()])) {
                if (!in_array($name, JsonApi::getFields()[$this->JsonApi_type()]))
                    continue;
            }

            $attributes[$name] = $value;
        }


        $relationships = [];
        foreach ($this->JsonApi_relationships() as $name => $relationship) {
            if (isset(JsonApi::getFields()[$this->JsonApi_type()])) {
                if (!in_array($name, JsonApi::getFields()[$this->JsonApi_type()]))
                    continue;
            }


            $links = [];
            if ($relationship[Relationship::LINKS_SELF])
                $links[] = new SelfLink('/'.URL::format($this->JsonApi_type()).'/'.$this->JsonApi_id().'/relationships/'.$name);
            if ($relationship[Relationship::LINKS_RELATED])
                $links[] = new RelatedLink('/'.URL::format($this->JsonApi_type()).'/'.$this->JsonApi_id().'/'.$name);

            $relationships[$name] = new Relationship(
                $name,
                new LinksObject(...$links),
                $relationship[Relationship::DATA] ?? null,
                $relationship[Relationship::META] ?? null
            );
        }

        return new ResourceObject(
            $this->JsonApi_type(),
            $this->JsonApi_id(),
            new LinksObject(
                new SelfLink('/'.URL::format($this->JsonApi_type()).'/'.$this->JsonApi_id())
            ),
            $attributes,
            $relationships,
            null
        );
    }

    public static function fromJsonApi(JSONObject $data) {
        $model = new static;

        if (!$model instanceof Model || !$model instanceof JsonApiSerializable)
            return null;


        if ($data->optString('type') != $model->JsonApi_type())
            return null; // throw error

        $model->setAttribute('id', $data->optString('id'));

        if ($data->has('attributes')) {
            $attributes = $data->optJSONObject('attributes');

            $metaData = $model->getMetaData();

            foreach ($metaData->getAttributes() as $attribute) {
                $model->defaultProperties[$attribute] = $model->readAttribute($attribute);
            }

            foreach ($attributes->keys() as $key) {
                $model->setAttribute($key, $attributes->opt($key));

                if (array_key_exists($key, $metaData->getColumnMap()))
                    $model->dirtyAttributes[$key] = $attributes->opt($key);
            }

            $newProperties = [];
            foreach ($metaData->getAttributes() as $attribute) {
                $newProperties[$attribute] = $model->readAttribute($attribute);
            }


            $model->dirtyAttributes = array_merge(
                array_udiff_uassoc($newProperties, $model->defaultProperties, function($value1, $value2) {
                    if ($value1 === $value2)
                        return 0;
                    return ($value1 > $value2) ? 1 : -1;

                }, function($key1, $key2) {
                    if ($key1 === $key2)
                        return 0;
                    return ($key1 > $key2) ? 1 : -1;
                }),
                $model->dirtyAttributes);
        }

        if ($data->has('relationships')) {
            $relationships = $data->optJSONObject('relationships');

            foreach ($relationships->keys() as $key) {

                if (is_array($relationships->optJSONObject($key)->opt('data'))) {
                    if ($relationships->optJSONObject($key)->has('data')) {
                        $relationship = $relationships->optJSONObject($key)->optJsonArray('data');
                        $relatedModelName = $model->getRelationByAlias($key)->getReferenceModelName();

                        $related = [];
                        for ($i=0; $i<count($relationship); $i++) {
                            $related[] = $relatedModelName::fromJsonApi($relationship->optJSONObject($i));
                        }

                        $model->setRelated($key, $related);
                    }
                }
                else {
                    if ($relationships->optJSONObject($key)->has('data')) {
                        if ($model instanceof \Franchise) {
                            switch ($key) {
                                case 'source':
                                    $model->sourceType = $relationships->optJSONObject($key)->optJSONObject('data')->optString('type');
                                    break;
                                case 'destination':
                                    $model->destinationType = $relationships->optJSONObject($key)->optJSONObject('data')->optString('type');
                                    break;
                            }
                            $model->initializeRelations();
                        }
                        
                        $relatedModelName = $model->getRelationByAlias($key)->getReferenceModelName();

                        $model->setRelated($key, $relatedModelName::fromJsonApi($relationships->optJSONObject($key)->optJSONObject('data')));
                    }
                }
            }
        }


        return $model;
    }
}