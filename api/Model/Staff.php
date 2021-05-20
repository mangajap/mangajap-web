<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Staff extends Model implements JsonApiSerializable {

    public $id;
    public $peopleId;
    public $mangaId;
    public $animeId;
    public $createdAt;
    public $updatedAt;
    public $role;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('staff');

        $this->setColumnMap([
            'id' => 'staff_id',
            'peopleId' => 'staff_peopleid',
            'mangaId' => 'staff_mangaid',
            'animeId' => 'staff_animeid',
            'role' => 'staff_role',
            'createdAt' => 'staff_createdat',
            'updatedAt' => 'staff_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'peopleId',
            'mangaId',
            'animeId',
            'role',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'peopleId' => Column::TYPE_INT,
            'mangaId' => Column::TYPE_INT,
            'animeId' => Column::TYPE_INT,
            'role' => Column::TYPE_ENUM,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);

        $this->skipAttributesOnUpdate([
            'peopleId',
            'mangaId',
            'animeId',
        ]);


        $this->belongsTo(
            'peopleId',
            People::class,
            'id',
            [
                'alias' => 'people'
            ]
        );

        $this->belongsTo(
            'mangaId',
            Manga::class,
            'id',
            [
                'alias' => 'manga'
            ]
        );

        $this->belongsTo(
            'animeId',
            Anime::class,
            'id',
            [
                'alias' => 'anime'
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
        $staff = new RouterGroup();

        $staff->get(
            '/staff',
            function() {
                return Staff::getList(JsonApi::getParameters());
            }
        );

        $staff->post(
            '/staff',
            function() use ($app) {
                $staff = Staff::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($staff->create())
                    return Staff::get($staff->getId());
                else
                    return null; // throw error
            }
        );

        $staff->get(
            '/staff/{id:[0-9]+}',
            function($id) {
                return Staff::get($id);
            }
        );

        $staff->patch(
            '/staff/{id:[0-9]+}',
            function ($id) use ($app) {
                $staff = Staff::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($staff->update())
                    return Staff::get($staff->getId());
                else
                    return null; // throw error
            }
        );

        $staff->get(
            '/staff/{id:[0-9]+}/people',
            function($id) {
                return Staff::get($id)->getRelated("people");
            }
        );

        $staff->get(
            '/staff/{id:[0-9]+}/manga',
            function($id) {
                return Staff::get($id)->getRelated("manga");
            }
        );

        $staff->get(
            '/staff/{id:[0-9]+}/anime',
            function($id) {
                return Staff::get($id)->getRelated("anime");
            }
        );

        return $staff;
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getPeopleId() {
        return $this->peopleId;
    }

    public function setPeopleId($peopleId) {
        $this->peopleId = $peopleId;
    }

    public function getMangaId() {
        return $this->mangaId;
    }

    public function setMangaId($mangaId) {
        $this->mangaId = $mangaId;
    }

    public function getAnimeId() {
        return $this->animeId;
    }

    public function setAnimeId($animeId) {
        $this->animeId = $animeId;
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
        return "staff";
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
        $relationships = [
            'people' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];

        if ($this->getMangaId() != null)
            $relationships['manga'] = [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ];
        if ($this->getAnimeId() != null)
            $relationships['anime'] = [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ];

        return $relationships;
    }

    public function JsonApi_filter() {
        return [];
    }
}