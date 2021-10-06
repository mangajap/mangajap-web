<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Season extends Model implements JsonApiSerializable
{

  public $id;
  public $animeId;
  public $createdAt;
  public $updatedAt;
  public $title_fr;
  public $title_en;
  public $title_en_jp;
  public $title_ja_jp;
  public $number;
  public $episodeCount;

  public function initialize()
  {
    $this->setConnectionService('db_mangajap');

    $this->setSource('season');

    $this->setColumnMap([
      'id' => 'season_id',
      'animeId' => 'season_animeid',
      'title_fr' => 'season_title_fr',
      'title_en' => 'season_title_en',
      'title_en_jp' => 'season_title_en_jp',
      'title_ja_jp' => 'season_title_ja_jp',
      'number' => 'season_number',
      'episodeCount' => 'season_episodecount',
      'createdAt' => 'season_createdat',
      'updatedAt' => 'season_updatedat',
    ]);

    $this->setPrimaryKey('id');

    $this->setAttributes([
      'id',
      'animeId',
      'title_fr',
      'title_en',
      'title_en_jp',
      'title_ja_jp',
      'number',
      'episodeCount',
      'createdAt',
      'updatedAt',
    ]);

    $this->setDataTypes([
      'id' => Column::TYPE_INT,
      'animeId' => Column::TYPE_INT,
      'title_fr' => Column::TYPE_TINYTEXT,
      'title_en' => Column::TYPE_TINYTEXT,
      'title_en_jp' => Column::TYPE_TINYTEXT,
      'title_ja_jp' => Column::TYPE_TINYTEXT,
      'number' => Column::TYPE_INT,
      'episodeCount' => Column::TYPE_INT,
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

    $this->hasMany(
      'id',
      Episode::class,
      'seasonId',
      [
        'alias' => 'episodes',
      ]
    );
  }

  public function beforeSave(): bool
  {
    $user = User::fromAccessToken();

    if (!$user instanceof User)
      return false;

    if (!$user->isAdmin())
      return false;

    return true;
  }


  public static function routerGroup(Application $app): RouterGroup
  {
    $seasons = new RouterGroup();

    $seasons->setPrefix('/seasons');

    $seasons->get(
      '/',
      function () {
        return Season::getList(JsonApi::getParameters());
      }
    );

    $seasons->post(
      '/',
      function () use ($app) {
        $episode = Season::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
        if ($episode->create())
          return Season::get($episode->id);
        else
          return null; // throw error
      }
    );

    $seasons->get(
      '/{id:[0-9]+}',
      function ($id) {
        return Season::get($id);
      }
    );

    $seasons->patch(
      '/{id:[0-9]+}',
      function ($id) use ($app) {
        $episode = Season::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
        if ($episode->update())
          return Season::get($episode->id);
        else
          return null; // throw error
      }
    );

    $seasons->get(
      '/{id:[0-9]+}/anime',
      function ($id) {
        return Season::get($id)->getRelated("anime");
      }
    );

    $seasons->get(
      '/{id:[0-9]+}/episodes',
      function ($id) {
        return Season::get($id)->getRelated("episodes");
      }
    );

    return $seasons;
  }



  public function setTitles($titles)
  {
    foreach (get_object_vars($titles) as $key => $value) {
      $this->{'title_' . $key} = $value;
    }
  }



  public function JsonApi_type()
  {
    return "seasons";
  }

  public function JsonApi_id()
  {
    return $this->id;
  }

  public function JsonApi_attributes()
  {
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
      'episodeCount' => $this->episodeCount,
    ];
  }

  public function JsonApi_relationships()
  {
    return [
      'anime' => [
        Relationship::LINKS_SELF => false,
        Relationship::LINKS_RELATED => true,
      ],
      'episodes' => [
        Relationship::LINKS_SELF => false,
        Relationship::LINKS_RELATED => true,
      ],
    ];
  }

  public function JsonApi_filter()
  {
    return [];
  }
}
