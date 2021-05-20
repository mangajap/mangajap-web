<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class People extends Model implements JsonApiSerializable {

    public $id;
    public $createdAt;
    public $updatedAt;
    public $firstName;
    public $lastName;
    public $pseudo;
    public $image;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('people');

        $this->setColumnMap([
            'id' => 'people_id',
            'firstName' => 'people_firstname',
            'lastName' => 'people_lastname',
            'pseudo' => 'people_pseudo',
            'createdAt' => 'people_createdat',
            'updatedAt' => 'people_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'firstName',
            'lastName',
            'pseudo',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'firstName' => Column::TYPE_TINYTEXT,
            'lastName' => Column::TYPE_TINYTEXT,
            'pseudo' => Column::TYPE_TINYTEXT,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);
        
        
        $this->hasMany(
            'id',
            Staff::class,
            'peopleId',
            [
                'alias' => 'staff'
            ]
        );

        $this->hasMany(
            'id',
            Staff::class,
            'peopleId',
            [
                'alias' => 'manga-staff',
                'params' => [
                    'conditions' => 'staff_mangaid IS NOT NULL'
                ]
            ]
        );

        $this->hasMany(
            'id',
            Staff::class,
            'peopleId',
            [
                'alias' => 'anime-staff',
                'params' => [
                    'conditions' => 'staff_animeid IS NOT NULL'
                ]
            ]
        );
    }

    public function beforeSave(): bool {
        $user = User::fromAccessToken();

        if (!$user instanceof User)
            return false;

        if (!$user->isAdmin())
            return false;

        return true;
    }


    public static function routerGroup(Application $app): RouterGroup {
        $people = new RouterGroup();

        $people->get(
            '/people',
            function() {
                return People::getList(JsonApi::getParameters());
            }
        );

        $people->post(
            '/people',
            function() use ($app) {
                $people = People::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($people->create())
                    return People::get($people->getId());
                else
                    return null; // throw error
            }
        );

        $people->get(
            '/people/{id:[0-9]+}',
            function ($id) {
                return People::get($id);
            }
        );

        $people->patch(
            '/people/{id:[0-9]+}',
            function ($id) use ($app) {
                $people = People::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($people->update())
                    return People::get($people->getId());
                else
                    return null; // throw error
            }
        );

        $people->get(
            '/people/{id:[0-9]+}/staff',
            function ($id) {
                return People::get($id)->getRelated("staff", JsonApi::getParameters());
            }
        );

        $people->get(
            '/people/{id:[0-9]+}/manga-staff',
            function ($id) {
                return People::get($id)->getRelated("manga-staff", JsonApi::getParameters());
            }
        );

        $people->get(
            '/people/{id:[0-9]+}/anime-staff',
            function ($id) {
                return People::get($id)->getRelated("anime-staff", JsonApi::getParameters());
            }
        );

        return $people;
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
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

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function getPseudo() {
        return $this->pseudo;
    }

    public function setPseudo($pseudo) {
        $this->pseudo = $pseudo;
    }

    public function getImage() {
        if (file_exists($_SERVER['DOCUMENT_ROOT'].'/images/people/'.$this->getId().'.jpg'))
            return 'http://mangajap.000webhostapp.com/images/people/'.$this->getId().'.jpg';

        return null;
    }

    public function setImage($image) {
        $this->image = $image;
    }



    public function JsonApi_type() {
        return "people";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'pseudo' => $this->pseudo,
            'image' => $this->image,
        ];
    }

    public function JsonApi_relationships() {
        return [
            'staff' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'manga-staff' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'anime-staff' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];
    }

    public function JsonApi_filter() {
        return [
            'query' => [
                'conditions' => [
                    'people_firstname LIKE CONCAT("%",:query,"%") OR 
                    people_lastname LIKE CONCAT("%",:query,"%") OR 
                    people_pseudo LIKE CONCAT("%",:query,"%")'
                ],
                'order' => [
                    'CASE 
                        WHEN people_firstname LIKE CONCAT(:query,"%") THEN 0 
                        WHEN people_lastname LIKE CONCAT(:query,"%") THEN 1
                        WHEN people_pseudo LIKE CONCAT(:query,"%") THEN 2 
                        ELSE 3
                    END',
                ],
            ],
        ];
    }
}