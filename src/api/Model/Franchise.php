<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Franchise extends Model implements JsonApiSerializable {

    public $id;
    public $sourceType;
    public $sourceId;
    public $destinationType;
    public $destinationId;
    public $createdAt;
    public $updatedAt;
    public $role;

    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource("franchise");

        $this->setColumnMap([
            'id' => 'franchise_id',
            'sourceType' => 'franchise_sourcetype',
            'sourceId' => 'franchise_sourceid',
            'destinationType' => 'franchise_destinationtype',
            'destinationId' => 'franchise_destinationid',
            'role' => 'franchise_role',
            'createdAt' => 'franchise_createdat',
            'updatedAt' => 'franchise_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'sourceType',
            'sourceId',
            'destinationType',
            'destinationId',
            'role',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'sourceType' => Column::TYPE_TINYTEXT,
            'sourceId' => Column::TYPE_INT,
            'destinationType' => Column::TYPE_TINYTEXT,
            'destinationId' => Column::TYPE_INT,
            'role' => Column::TYPE_ENUM,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);

				// TODO: à voir si on peut modifier ou non (supprimer et en recréer un OU le modifier)
        $this->skipAttributesOnUpdate([
            'sourceType',
            'sourceId',
            // 'destinationType',
            // 'destinationId',
        ]);
    }

    public function initializeRelations() {
        switch ($this->getSourceType()) {
            case (new Manga())->getMetaData()->getSource():
                $this->belongsTo(
                    'sourceId',
                    Manga::class,
                    'id',
                    [
                        'alias' => 'source',
                    ]
                );
                break;
            case (new Anime())->getMetaData()->getSource():
                $this->belongsTo(
                    'sourceId',
                    Anime::class,
                    'id',
                    [
                        'alias' => 'source',
                    ]
                );
                break;
        }

        switch ($this->getDestinationType()) {
            case (new Manga())->getMetaData()->getSource():
                $this->belongsTo(
                    'destinationId',
                    Manga::class,
                    'id',
                    [
                        'alias' => 'destination',
                    ]
                );
                break;
            case (new Anime())->getMetaData()->getSource():
                $this->belongsTo(
                    'destinationId',
                    Anime::class,
                    'id',
                    [
                        'alias' => 'destination',
                    ]
                );
                break;
        }
    }

    public function afterGet() {
        $this->initializeRelations();
    }

  public static function routerGroup(Application $app): RouterGroup {
        $franchises = new RouterGroup();

        $franchises->setPrefix('/franchises');

        $franchises->get(
            '/',
            function() {
                return Franchise::getList(JsonApi::getParameters());
            }
        );

        $franchises->post(
            '/',
            function() use ($app) {
                $franchise = Franchise::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($franchise->create())
                    return Franchise::get($franchise->getId());
                else
                    return null; // throw error
            }
        );

        $franchises->get(
            '/{id:[0-9]+}',
            function($id) {
                return Franchise::get($id);
            }
        );

        $franchises->patch(
            '/{id:[0-9]+}',
            function ($id) use ($app) {
                $franchise = Franchise::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($franchise->update())
                    return Franchise::get($franchise->getId());
                else
                    return null; // throw error
            }
        );

        $franchises->get(
            '/{id:[0-9]+}/source',
            function($id) {
                return Franchise::get($id)->getRelated("source");
            }
        );

        $franchises->get(
            '/{id:[0-9]+}/destination',
            function($id) {
                return Franchise::get($id)->getRelated("destination");
            }
        );

        return $franchises;
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getSourceType() {
        return $this->sourceType;
    }

    public function setSourceType($sourceType) {
        $this->sourceType = $sourceType;
    }

    public function getSourceId() {
        return $this->sourceId;
    }

    public function setSourceId($sourceId) {
        $this->sourceId = $sourceId;
    }

    public function getDestinationType() {
        return $this->destinationType;
    }

    public function setDestinationType($destinationType) {
        $this->destinationType = $destinationType;
    }

    public function getDestinationId() {
        return $this->destinationId;
    }

    public function setDestinationId($destinationId) {
        $this->destinationId = $destinationId;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }





    public function JsonApi_type() {
        return "franchises";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'role' => $this->role,
        ];
    }

    public function JsonApi_relationships() {
        return [
            'source' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'destination' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];
    }

    public function JsonApi_filter() {
        return [];
    }
}
