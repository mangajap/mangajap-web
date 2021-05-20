<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Review extends Model implements JsonApiSerializable {

    public $id;
    public $userId;
    public $mangaId;
    public $animeId;
    public $createdAt;
    public $updatedAt;
    public $content;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('review');

        $this->setColumnMap([
            'id' => 'review_id',
            'userId' => 'review_userid',
            'mangaId' => 'review_mangaid',
            'animeId' => 'review_animeid',
            'content' => 'review_content',
            'createdAt' => 'review_createdat',
            'updatedAt' => 'review_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'userId',
            'mangaId',
            'animeId',
            'content',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'userId' => Column::TYPE_INT,
            'mangaId' => Column::TYPE_INT,
            'animeId' => Column::TYPE_INT,
            'content' => Column::TYPE_TEXT,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);

        $this->skipAttributesOnUpdate([
            'userId',
            'mangaId',
            'animeId',
        ]);


        $this->belongsTo(
            'userId',
            User::class,
            'id',
            [
                'alias' => 'user'
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

    public function beforeCreate(): bool {
        if (\App\OAuth::getBearerToken() == null)
            return false;

        return true;
    }

    public function beforeUpdate(): bool {
        $user = User::fromAccessToken();
        $review = Review::get($this->getId());

        if (!$user instanceof User || !$review instanceof Review)
            return false;

        if ($user->getId() != $review->getUserId())
            return false;

        return true;
    }


    public static function routerGroup(Application $app): RouterGroup {
        $reviews = new RouterGroup();

        $reviews->get(
            '/reviews',
            function() {
                return Review::getList(JsonApi::getParameters());
            }
        );

        $reviews->post(
            '/reviews',
            function() use ($app) {
                $review = Review::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($review->create())
                    return Review::get($review->getId());
                else
                    return null; // throw error
            }
        );

        $reviews->get(
            '/reviews/{id:[0-9]+}',
            function ($id) {
                return Review::get($id);
            }
        );

        $reviews->patch(
            '/reviews/{id:[0-9]+}',
            function ($id) use ($app) {
                $review = Review::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($review->update())
                    return Review::get($review->getId());
                else
                    return null; // throw error
            }
        );

        $reviews->get(
            '/reviews/{id:[0-9]+}/user',
            function ($id) {
                return Review::get($id)->getRelated("user");
            }
        );

        $reviews->get(
            '/reviews/{id:[0-9]+}/manga',
            function ($id) {
                return Review::get($id)->getRelated("manga");
            }
        );

        $reviews->get(
            '/reviews/{id:[0-9]+}/anime',
            function ($id) {
                return Review::get($id)->getRelated("anime");
            }
        );

        return $reviews;
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
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

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }


    public function JsonApi_type() {
        return "reviews";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'content' => $this->content,
        ];
    }

    public function JsonApi_relationships() {
        $relationships = [
            'user' => [
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