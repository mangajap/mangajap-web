<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Genre extends Model implements JsonApiSerializable {

    public $id;
    public $createdAt;
    public $updatedAt;
    public $title_fr;
    public $description;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('genre');

        $this->setColumnMap([
            'id' => 'genre_id',
            'title_fr' => 'genre_title_fr',
            'description' => 'genre_description',
            'createdAt' => 'genre_createdat',
            'updatedAt' => 'genre_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'title_fr',
            'description',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'title_fr' => Column::TYPE_TINYTEXT,
            'description' => Column::TYPE_TEXT,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);

        
        $this->hasManyToMany(
            'id',
            GenreRelationships::class,
            'genreId',
            'mangaId',
            Manga::class,
            'id',
            [
                'alias' => 'manga',
                'params' => [
                    'conditions' => 'genrerelationships_mangaid IS NOT NULL'
                ]
            ]
        );

        $this->hasManyToMany(
            'id',
            GenreRelationships::class,
            'genreId',
            'animeId',
            Anime::class,
            'id',
            [
                'alias' => 'anime',
                'params' => [
                    'conditions' => 'genrerelationships_animeid IS NOT NULL'
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
        $genres = new RouterGroup();

        $genres->get(
            '/genres',
            function() {
                return Genre::getList(JsonApi::getParameters());
            }
        );

        $genres->post(
            '/genres',
            function() use ($app) {
                $genre = Genre::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($genre->create())
                    return Genre::get($genre->getId());
                else
                    return null; // throw error
            }
        );

        $genres->get(
            '/genres/{id:[0-9]+}',
            function($id) {
                return Genre::get($id);
            }
        );

        $genres->patch(
            '/genres/{id:[0-9]+}',
            function ($id) use ($app) {
                $genre = Genre::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($genre->update())
                    return Genre::get($genre->getId());
                else
                    return null; // throw error
            }
        );

        $genres->get(
            '/genres/{id:[0-9]+}/manga',
            function($id) {
                return Genre::get($id)->getRelated("manga", JsonApi::getParameters());
            }
        );

        $genres->get(
            '/genres/{id:[0-9]+}/anime',
            function($id) {
                return Genre::get($id)->getRelated("anime", JsonApi::getParameters());
            }
        );

        return $genres;
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

    public function getTitleFr() {
        return $this->title_fr;
    }

    public function setTitleFr($title_fr) {
        $this->title_fr = $title_fr;
    }

    public function setTitles($titles) {
        $this->title_fr = $titles->fr;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }


    public function JsonApi_type() {
        return "genres";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'title' => $this->title_fr,
            'titles' => [
                'fr' => $this->title_fr,
            ],
            'description' => $this->description,
        ];
    }

    public function JsonApi_relationships() {
        return [
            'manga' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'anime' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];
    }

    public function JsonApi_filter() {
        return [];
    }
}