<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Episode extends Model implements JsonApiSerializable {

    public $id;
    public $animeId;
    public $seasonId;
    public $createdAt;
    public $updatedAt;
    public $title_fr;
    public $title_en;
    public $title_en_jp;
    public $title_ja_jp;
    public $seasonNumber;
    public $relativeNumber;
    public $number;
    public $airDate;
    public $episodeType;

    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('episode');

        $this->setColumnMap([
            'id' => 'episode_id',
            'animeId' => 'episode_animeid',
            'seasonId' => 'episode_seasonid',
            'title_fr' => 'episode_title_fr',
            'title_en' => 'episode_title_en',
            'title_en_jp' => 'episode_title_en_jp',
            'title_ja_jp' => 'episode_title_ja_jp',
            'seasonNumber' => 'episode_seasonnumber',
            'relativeNumber' => 'episode_relativenumber',
            'number' => 'episode_number',
            'airDate' => 'episode_airdate',
            'episodeType' => 'episode_type',
            'createdAt' => 'episode_createdat',
            'updatedAt' => 'episode_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'id',
            'animeId',
            'seasonId',
            'title_fr',
            'title_en',
            'title_en_jp',
            'title_ja_jp',
            'seasonNumber',
            'relativeNumber',
            'number',
            'airDate',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'animeId' => Column::TYPE_INT,
            'seasonId' => Column::TYPE_INT,
            'title_fr' => Column::TYPE_TINYTEXT,
            'title_en' => Column::TYPE_TINYTEXT,
            'title_en_jp' => Column::TYPE_TINYTEXT,
            'title_ja_jp' => Column::TYPE_TINYTEXT,
            'seasonNumber' => Column::TYPE_INT,
            'relativeNumber' => Column::TYPE_INT,
            'number' => Column::TYPE_INT,
            'airDate' => Column::TYPE_DATE,
            'episodeType' => Column::TYPE_ENUM,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);


        $this->belongsTo(
            'animeId',
            Anime::class,
            'id',
            [
                'alias' => 'anime',
            ]
        );

        $this->belongsTo(
            'seasonId',
            Season::class,
            'id',
            [
                'alias' => 'season',
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
        $episodes = new RouterGroup();

        $episodes->get(
            '/episodes',
            function() {
                return Episode::getList(JsonApi::getParameters());
            }
        );

        $episodes->post(
            '/episodes',
            function() use ($app) {
                $episode = Episode::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($episode->create())
                    return Episode::get($episode->getId());
                else
                    return null; // throw error
            }
        );

        $episodes->get(
            '/episodes/{id:[0-9]+}',
            function($id) {
                return Episode::get($id);
            }
        );

        $episodes->patch(
            '/episodes/{id:[0-9]+}',
            function ($id) use ($app) {
                $episode = Episode::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($episode->update())
                    return Episode::get($episode->getId());
                else
                    return null; // throw error
            }
        );

        $episodes->get(
            '/episodes/{id:[0-9]+}/anime',
            function($id) {
                return Episode::get($id)->getRelated("anime");
            }
        );

        $episodes->get(
            '/episodes/{id:[0-9]+}/season',
            function($id) {
                return Episode::get($id)->getRelated("season");
            }
        );

        return $episodes;
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
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

    public function getTitlefr() {
        return $this->title_fr;
    }

    public function setTitlefr($title_fr) {
        $this->title_fr = $title_fr;
    }

    public function setTitles($titles) {
        foreach (get_object_vars($titles) as $key => $value) {
            $this->{'title_'.$key} = $value;
        }
    }

    public function getSeasonNumber() {
        return $this->seasonNumber;
    }

    public function setSeasonNumber($seasonNumber) {
        $this->seasonNumber = $seasonNumber;
    }

    public function getRelativeNumber() {
        return $this->relativeNumber;
    }

    public function setRelativeNumber($relativeNumber) {
        $this->relativeNumber = $relativeNumber;
    }

    public function getNumber() {
        return $this->number;
    }

    public function setNumber($number) {
        $this->number = $number;
    }

    public function getAirDate() {
        return $this->airDate;
    }

    public function setAirDate($airDate) {
        $this->airDate = $airDate;
    }

    public function setEpisodeType($episodeType) {
        $this->episodeType = $episodeType;
    }


    public function JsonApi_type() {
        return "episodes";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'canonicalTitle' => $this->title_fr, // TODO: DEPRECATED use titles
            'titles' => [
                'fr' => $this->title_fr,
                'en' => $this->title_en,
                'en_jp' => $this->title_en_jp,
                'ja_jp' => $this->title_ja_jp,
            ],
            'seasonNumber' => $this->seasonNumber,
            'relativeNumber' => $this->relativeNumber,
            'number' => $this->number,
            'airDate' => $this->airDate,
            'episodeType' => $this->episodeType,
        ];
    }

    public function JsonApi_relationships() {
        return [
            'anime' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'season' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];
    }

    public function JsonApi_filter() {
        return [];
    }
}