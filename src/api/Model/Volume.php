<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Volume extends Model implements JsonApiSerializable {

    public $id;
    public $mangaId;
    public $title_fr;
    public $title_en;
    public $title_en_jp;
    public $title_ja_jp;
    public $number;
    public $startChapter;
    public $endChapter;
    public $published;
    public $coverImage;
    public $createdAt;
    public $updatedAt;

    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('volume');

        $this->setColumnMap([
            'id' => 'volume_id',
            'mangaId' => 'volume_mangaid',
            'title_fr' => 'volume_title_fr',
            'title_en' => 'volume_title_en',
            'title_en_jp' => 'volume_title_en_jp',
            'title_ja_jp' => 'volume_title_ja_jp',
            'number' => 'volume_number',
            'startChapter' => 'volume_startchapter',
            'endChapter' => 'volume_endchapter',
            'published' => 'volume_published',
            'createdAt' => 'volume_createdat',
            'updatedAt' => 'volume_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'id',
            'mangaId',
            'title_fr',
            'title_en',
            'title_en_jp',
            'title_ja_jp',
            'number',
            'startChapter',
            'endChapter',
            'published',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'mangaId' => Column::TYPE_INT,
            'title_fr' => Column::TYPE_TINYTEXT,
            'title_en' => Column::TYPE_TINYTEXT,
            'title_en_jp' => Column::TYPE_TINYTEXT,
            'title_ja_jp' => Column::TYPE_TINYTEXT,
            'number' => Column::TYPE_INT,
            'startChapter' => Column::TYPE_INT,
            'endChapter' => Column::TYPE_INT,
            'published' => Column::TYPE_DATE,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);


        $this->belongsTo(
            'mangaId',
            Manga::class,
            'id',
            [
                'alias' => 'manga',
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
        $volumes = new RouterGroup();

        $volumes->get(
            '/volumes',
            function() {
                return Volume::getList(JsonApi::getParameters());
            }
        );

        $volumes->post(
            '/volumes',
            function() use ($app) {
                $volume = Volume::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($volume->create())
                    return Volume::get($volume->getId());
                else
                    return null; // throw error
            }
        );

        $volumes->get(
            '/volumes/{id:[0-9]+}',
            function($id) {
                return Volume::get($id);
            }
        );

        $volumes->patch(
            '/volumes/{id:[0-9]+}',
            function ($id) use ($app) {
                $volume = Volume::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($volume->update())
                    return Volume::get($volume->getId());
                else
                    return null; // throw error
            }
        );

        $volumes->get(
            '/volumes/{id:[0-9]+}/manga',
            function($id) {
                return Volume::get($id)->getRelated("manga");
            }
        );

        return $volumes;
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getMangaId() {
        return $this->mangaId;
    }

    public function setMangaId($mangaId) {
        $this->mangaId = $mangaId;
    }

    public function getTitleFr() {
        return $this->title_fr;
    }

    public function setTitleFr($title_fr) {
        $this->title_fr = $title_fr;
    }

    public function getTitleEn() {
        return $this->title_en;
    }

    public function setTitleEn($title_en) {
        $this->title_en = $title_en;
    }

    public function getTitleEnJp() {
        return $this->title_en_jp;
    }

    public function setTitleEnJp($title_en_jp) {
        $this->title_en_jp = $title_en_jp;
    }

    public function getTitleJaJp() {
        return $this->title_ja_jp;
    }

    public function setTitleJaJp($title_ja_jp) {
        $this->title_ja_jp = $title_ja_jp;
    }

    public function setTitles($titles) {
        foreach (get_object_vars($titles) as $key => $value) {
            $this->{'title_'.$key} = $value;
        }
    }

    public function getNumber() {
        return $this->number;
    }

    public function setNumber($number) {
        $this->number = $number;
    }

    public function getStartChapter() {
        return $this->startChapter;
    }

    public function setStartChapter($startChapter) {
        $this->startChapter = $startChapter;
    }

    public function getEndChapter() {
        return $this->endChapter;
    }

    public function setEndChapter($endChapter) {
        $this->endChapter = $endChapter;
    }

    public function getPublished() {
        return $this->published;
    }

    public function setPublished($published) {
        $this->published = $published;
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


    public function JsonApi_type() {
        return "volumes";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'titles' => [
                'fr' => $this->title_fr,
                'en' => $this->title_en,
                'en_jp' => $this->title_en_jp,
                'ja_jp' => $this->title_ja_jp,
            ],
            'number' => $this->number,
            'startChapter' => $this->startChapter,
            'endChapter' => $this->endChapter,
            'published' => $this->published,
            'coverImage' => $this->coverImage,
        ];
    }

    public function JsonApi_relationships() {
        return [
            'manga' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];
    }

    public function JsonApi_filter() {
        return [];
    }
}