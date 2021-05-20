<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class AnimeEntry extends Model implements JsonApiSerializable {

    public $id;
    public $userId;
    public $animeId;
    public $createdAt;
    public $updatedAt;
    public $isAdd;
    public $isFavorites;
    public $isPrivate;
    public $status;
    public $episodesWatch;
    public $startedAt;
    public $finishedAt;
    public $rating;
    public $rewatchCount;
    public $comments;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('animeentry');

        $this->setColumnMap([
            'id' => 'animeentry_id',
            'userId' => 'animeentry_userid',
            'animeId' => 'animeentry_animeid',
            'isAdd' => 'animeentry_isadd',
            'isFavorites' => 'animeentry_isfavorites',
            'isPrivate' => 'animeentry_isprivate',
            'status' => 'animeentry_status',
            'episodesWatch' => 'animeentry_episodeswatch',
            'rating' => 'animeentry_rating',
            'startedAt' => 'animeentry_startedat',
            'finishedAt' => 'animeentry_finishedat',
            'rewatchCount' => 'animeentry_rewatchcount',
            'createdAt' => 'animeentry_createdat',
            'updatedAt' => 'animeentry_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'userId',
            'animeId',
            'isAdd',
            'isFavorites',
            'isPrivate',
            'status',
            'episodesWatch',
            'rating',
            'startedAt',
            'finishedAt',
            'rewatchCount',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'userId' => Column::TYPE_INT,
            'animeId' => Column::TYPE_INT,
            'isAdd' => Column::TYPE_BOOLEAN,
            'isFavorites' => Column::TYPE_BOOLEAN,
            'isPrivate' => Column::TYPE_BOOLEAN,
            'status' => Column::TYPE_ENUM,
            'episodesWatch' => Column::TYPE_INT,
            'rating' => Column::TYPE_INT,
            'startedAt' => Column::TYPE_DATETIME,
            'finishedAt' => Column::TYPE_DATETIME,
            'rewatchCount' => Column::TYPE_INT,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);

        $this->skipAttributesOnUpdate([
            'userId',
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

        if (AnimeEntry::get([
                "animeentry_userid = :userId AND animeentry_animeid = :animeId",
                'bind' => [
                    'userId' => $this->getUserId(),
                    'animeId' => $this->getAnimeId(),
                ],
            ]) != null)
            return false;

        return true;
    }

    public function beforeUpdate(): bool {
        $user = User::fromAccessToken();
        $animeEntry = AnimeEntry::get($this->getId());

        if (!$user instanceof User || !$animeEntry instanceof AnimeEntry)
            return false;

        if ($user->getId() != $animeEntry->getUserId())
            return false;

        return true;
    }


    public static function routerGroup(Application $app): RouterGroup {
        $animeEntries = new RouterGroup();

        $animeEntries->get(
            '/anime-entries',
            function() {
                return AnimeEntry::getList(JsonApi::getParameters());
            }
        );

        $animeEntries->post(
            '/anime-entries',
            function() use ($app) {
                $animeEntry = AnimeEntry::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($animeEntry->create())
                    return AnimeEntry::get($animeEntry->getId());
                else
                    return null;
            }
        );

        $animeEntries->get(
            '/anime-entries/{id:[0-9]+}',
            function($id) {
                return AnimeEntry::get($id);
            }
        );

        $animeEntries->patch(
            '/anime-entries/{id:[0-9]+}',
            function($id) use ($app) {
                $animeEntry = AnimeEntry::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($animeEntry->update())
                    return AnimeEntry::get($animeEntry->getId());
                else
                    return null;
            }
        );

        $animeEntries->get(
            '/anime-entries/{id:[0-9]+}/user',
            function($id) {
                return AnimeEntry::get($id)->getRelated("user");
            }
        );

        $animeEntries->get(
            '/anime-entries/{id:[0-9]+}/anime',
            function($id) {
                return AnimeEntry::get($id)->getRelated("anime");
            }
        );

        return $animeEntries;
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

    public function isAdd() {
        return $this->isAdd;
    }

    public function setIsAdd($isAdd) {
        $this->isAdd = $isAdd;
    }

    public function isFavorites() {
        return $this->isFavorites;
    }

    public function setIsFavorites($isFavorites) {
        $this->isFavorites = $isFavorites;
    }

    public function isPrivate() {
        return $this->isPrivate;
    }

    public function setIsPrivate($isPrivate) {
        $this->isPrivate = $isPrivate;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getEpisodesWatch() {
        return $this->episodesWatch;
    }

    public function setEpisodesWatch($episodesWatch) {
        $this->episodesWatch = $episodesWatch;
    }

    public function getStartedAt() {
        return $this->startedAt;
    }

    public function setStartedAt($startedAt) {
        $this->startedAt = $startedAt;
    }

    public function getFinishedAt() {
        return $this->finishedAt;
    }

    public function setFinishedAt($finishedAt) {
        $this->finishedAt = $finishedAt;
    }

    public function getRating() {
        return $this->rating;
    }

    public function setRating($rating) {
        $this->rating = $rating;
    }

    public function getRewatchCount() {
        return $this->rewatchCount;
    }

    public function setRewatchCount($rewatchCount) {
        $this->rewatchCount = $rewatchCount;
    }

    public function getComments() {
        return $this->comments;
    }

    public function setComments($comments) {
        $this->comments = $comments;
    }



    public function JsonApi_type() {
        return "animeEntries";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'isAdd' => $this->isAdd,
            'isFavorites' => $this->isFavorites,
            'status' => $this->status,
            'episodesWatch' => $this->episodesWatch,
            'startedAt' => $this->startedAt,
            'finishedAt' => $this->finishedAt,
            'rating' => $this->rating,
            'rewatchCount' => $this->rewatchCount,
        ];
    }

    public function JsonApi_relationships() {
        return [
            'user' => [
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
        return [
            'status'
        ];
    }
}