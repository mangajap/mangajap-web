<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class MangaEntry extends Model implements JsonApiSerializable {

    public $id;
    public $userId;
    public $mangaId;
    public $createdAt;
    public $updatedAt;
    public $isAdd;
    public $isFavorites;
    public $isPrivate;
    public $status;
    public $volumesRead;
    public $chaptersRead;
    public $startedAt;
    public $finishedAt;
    public $rating;
    public $rereadCount;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('mangaentry');

        $this->setColumnMap([
            'id' => 'mangaentry_id',
            'userId' => 'mangaentry_userid',
            'mangaId' => 'mangaentry_mangaid',
            'isAdd' => 'mangaentry_isadd',
            'isFavorites' => 'mangaentry_isfavorites',
            'isPrivate' => 'mangaentry_isprivate',
            'status' => 'mangaentry_status',
            'volumesRead' => 'mangaentry_volumesread',
            'chaptersRead' => 'mangaentry_chaptersread',
            'rating' => 'mangaentry_rating',
            'startedAt' => 'mangaentry_startedat',
            'finishedAt' => 'mangaentry_finishedat',
            'rereadCount' => 'mangaentry_rereadcount',
            'createdAt' => 'mangaentry_createdat',
            'updatedAt' => 'mangaentry_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'userId',
            'mangaId',
            'isAdd',
            'isFavorites',
            'isPrivate',
            'status',
            'volumesRead',
            'chaptersRead',
            'rating',
            'startedAt',
            'finishedAt',
            'rereadCount',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'userId' => Column::TYPE_INT,
            'mangaId' => Column::TYPE_INT,
            'isAdd' => Column::TYPE_BOOLEAN,
            'isFavorites' => Column::TYPE_BOOLEAN,
            'isPrivate' => Column::TYPE_BOOLEAN,
            'status' => Column::TYPE_ENUM,
            'volumesRead' => Column::TYPE_INT,
            'chaptersRead' => Column::TYPE_INT,
            'rating' => Column::TYPE_INT,
            'startedAt' => Column::TYPE_DATETIME,
            'finishedAt' => Column::TYPE_DATETIME,
            'rereadCount' => Column::TYPE_INT,
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
    }

    public function beforeCreate(): bool {
        if (\App\OAuth::getBearerToken() == null)
            return false;

        if (MangaEntry::get([
                "mangaentry_userid = :userId AND mangaentry_mangaid = :mangaId",
                'bind' => [
                    'userId' => $this->getUserId(),
                    'mangaId' => $this->getMangaId(),
                ],
            ]) != null)
            return false;

        return true;
    }

    public function beforeUpdate(): bool {
        $user = User::fromAccessToken();
        $mangaEntry = MangaEntry::get($this->getId());

        if (!$user instanceof User || !$mangaEntry instanceof MangaEntry)
            return false;

        if ($user->getId() != $mangaEntry->getUserId())
            return false;

        return true;
    }


    public static function routerGroup(Application $app): RouterGroup {
        $mangaEntries = new RouterGroup();

        $mangaEntries->get(
            '/manga-entries',
            function() {
                return MangaEntry::getList(JsonApi::getParameters());
            }
        );

        $mangaEntries->post(
            '/manga-entries',
            function() use ($app) {
                $mangaEntry = MangaEntry::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($mangaEntry->create())
                    return MangaEntry::get($mangaEntry->getId());
                else
                    return null; // throw error
            }
        );

        $mangaEntries->get(
            '/manga-entries/{id:[0-9]+}',
            function($id) {
                return MangaEntry::get($id);
            }
        );

        $mangaEntries->patch(
            '/manga-entries/{id:[0-9]+}',
            function($id) use ($app) {
                $mangaEntry = MangaEntry::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($mangaEntry->update())
                    return MangaEntry::get($mangaEntry->getId());
                else
                    return null; // throw error
            }
        );

        $mangaEntries->get(
            '/manga-entries/{id:[0-9]+}/user',
            function($id) {
                return MangaEntry::get($id)->getRelated("user");
            }
        );

        $mangaEntries->get(
            '/manga-entries/{id:[0-9]+}/manga',
            function($id) {
                return MangaEntry::get($id)->getRelated("manga");
            }
        );

        return $mangaEntries;
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

    public function getVolumesRead() {
        return $this->volumesRead;
    }

    public function setVolumesRead($volumesRead) {
        $this->volumesRead = $volumesRead;

        $user = User::fromAccessToken();
        if ($user instanceof User && $user->isAdmin()) {
            $manga = MangaEntry::get($this->getId())->getRelated("manga");
            if ($manga instanceof Manga) {
                if ($this->volumesRead > $manga->volumeCount) {
					for ($volumeNumber = $manga->volumeCount+1; $volumeNumber <= $this->volumesRead; $volumeNumber++) {
						$volume = new Volume();
						$volume->setNumber($volumeNumber);
						$volume->setMangaId($manga->getId());
						$volume->create();
					}
					
                    $manga->setVolumeCount($this->volumesRead);
                    $manga->update();
                }
            }
        }
    }

    public function getChaptersRead() {
        return $this->chaptersRead;
    }

    public function setChaptersRead($chaptersRead) {
        $this->chaptersRead = $chaptersRead;

        $user = User::fromAccessToken();
        if ($user instanceof User && $user->isAdmin()) {
            $manga = MangaEntry::get($this->getId())->getRelated("manga");
            if ($manga instanceof Manga) {
                if ($this->chaptersRead > $manga->chapterCount) {
                    $manga->setChapterCount($this->chaptersRead);
                    $manga->update();
                }
            }
        }
    }

    public function getStartedAt() {
        return $this->startedAt;
    }

    public function setStartedAt($startedAt) {
        if (strpos($startedAt, "T") !== false) {
            $startedAt = date('Y-m-d H:i:s', strtotime($startedAt));
        }
        $this->startedAt = $startedAt;
    }

    public function getFinishedAt() {
        return $this->finishedAt;
    }

    public function setFinishedAt($finishedAt) {
        if (strpos($finishedAt, "T") !== false) {
            $finishedAt = date('Y-m-d H:i:s', strtotime($finishedAt));
        }
        $this->finishedAt = $finishedAt;
    }

    public function getRating() {
        return $this->rating;
    }

    public function setRating($rating) {
        $this->rating = $rating;
    }

    public function getRereadCount() {
        return $this->rereadCount;
    }

    public function setRereadCount($rereadCount) {
        $this->rereadCount = $rereadCount;
    }



    public function JsonApi_type() {
        return "mangaEntries";
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
            'volumesRead' => $this->volumesRead,
            'chaptersRead' => $this->chaptersRead,
            'startedAt' => $this->startedAt,
            'finishedAt' => $this->finishedAt,
            'rating' => $this->rating,
            'rereadCount' => $this->rereadCount,
        ];
    }

    public function JsonApi_relationships() {
        return [
            'user' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'manga' => [
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