<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Theme extends Model implements JsonApiSerializable {

    public $id;
    public $createdAt;
    public $updatedAt;
    public $title;
    public $description;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('theme');

        $this->setColumnMap([
            'id' => 'theme_id',
            'title' => 'theme_title_fr',
            'description' => 'theme_description',
            'createdAt' => 'theme_createdat',
            'updatedAt' => 'theme_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'title',
            'description',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'title' => Column::TYPE_TINYTEXT,
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
            ThemeRelationships::class,
            'themeId',
            'mangaId',
            Manga::class,
            'id',
            [
                'alias' => 'manga',
                'params' => [
                    'conditions' => 'themerelationships_mangaid IS NOT NULL'
                ]
            ]
        );

        $this->hasManyToMany(
            'id',
            ThemeRelationships::class,
            'themeId',
            'animeId',
            Anime::class,
            'id',
            [
                'alias' => 'anime',
                'params' => [
                    'conditions' => 'themerelationships_animeid IS NOT NULL'
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
        $themes = new RouterGroup();

        $themes->get(
            '/themes',
            function() {
                return Theme::getList(JsonApi::getParameters());
            }
        );

        $themes->post(
            '/themes',
            function() use ($app) {
                $theme = Theme::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($theme->create())
                    return Theme::get($theme->getId());
                else
                    return null; // throw error
            }
        );

        $themes->get(
            '/themes/{id:[0-9]+}',
            function($id) {
                return Theme::get($id);
            }
        );

        $themes->patch(
            '/themes/{id:[0-9]+}',
            function ($id) use ($app) {
                $theme = Theme::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($theme->update())
                    return Theme::get($theme->getId());
                else
                    return null; // throw error
            }
        );

        $themes->get(
            '/themes/{id:[0-9]+}/manga',
            function($id) {
                return Theme::get($id)->getRelated("manga");
            }
        );

        $themes->get(
            '/themes/{id:[0-9]+}/anime',
            function($id) {
                return Theme::get($id)->getRelated("anime");
            }
        );

        return $themes;
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

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setTitles($titles) {
        foreach (get_object_vars($titles) as $key => $value) {
            $this->{'title_'.$key} = $value;
        }
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }


    public function JsonApi_type() {
        return "themes";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'title' => $this->title,
            'titles' => [ // TODO: DEPRECATED use title
                'fr' => $this->title,
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