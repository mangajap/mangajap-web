<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;
use App\Utils\Slug;

class Anime extends Model implements JsonApiSerializable
{

    public $id;
    public $createdAt;
    public $updatedAt;
    public $title;
    public $title_fr;
    public $title_en;
    public $title_en_jp;
    public $title_ja_jp;
    public $slug;
    public $coverImage;
    public $startDate;
    public $endDate;
    public $origin;
    public $status;
    public $seasonCount;
    public $episodeCount;
    public $episodeLength;
    public $totalLength;
    public $animeType;
    public $synopsis;
    public $averageRating;
    public $ratingRank;
    public $popularity;
    public $userCount;
    public $favoritesCount;
    public $reviewCount;
    public $youtubeVideoId;


    public function initialize()
    {
        $this->setConnectionService('db_mangajap');

        $this->setSource('anime');

        $this->setColumnMap([
            'id' => 'anime_id',
            'title' => 'anime_title',
            'title_fr' => 'anime_title_fr',
            'title_en' => 'anime_title_en',
            'title_en_jp' => 'anime_title_en_jp',
            'title_ja_jp' => 'anime_title_ja_jp',
            'slug' => 'anime_slug',
            'youtubeVideoId' => 'anime_youtubevideoid',
            'startDate' => 'anime_releasedate',
            'endDate' => 'anime_enddate',
            'origin' => 'anime_origin',
            'status' => 'anime_status',
            'seasonCount' => 'anime_seasoncount',
            'episodeCount' => 'anime_episodecount',
            'episodeLength' => 'anime_episodelength',
            'animeType' => 'anime_type',
            'synopsis' => 'anime_synopsis',
            'averageRating' => 'anime_rating',
            'ratingRank' => 'anime_ratingrank',
            'popularity' => 'anime_popularity',
            'userCount' => 'anime_usercount',
            'favoritesCount' => 'anime_favoritescount',
            'reviewCount' => 'anime_reviewcount',
            'createdAt' => 'anime_createdat',
            'updatedAt' => 'anime_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'title',
            'title_fr',
            'title_en',
            'title_en_jp',
            'title_ja_jp',
            'slug',
            'youtubeVideoId',
            'startDate',
            'endDate',
            'origin',
            'status',
            'seasonCount',
            'episodeCount',
            'episodeLength',
            'animeType',
            'synopsis',
            'averageRating',
            'ratingRank',
            'popularity',
            'userCount',
            'favoritesCount',
            'reviewCount',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'title' => Column::TYPE_TINYTEXT,
            'title_fr' => Column::TYPE_TINYTEXT,
            'title_en' => Column::TYPE_TINYTEXT,
            'title_en_jp' => Column::TYPE_TINYTEXT,
            'title_ja_jp' => Column::TYPE_TINYTEXT,
            'slug' => Column::TYPE_TINYTEXT,
            'youtubeVideoId' => Column::TYPE_TINYTEXT,
            'startDate' => Column::TYPE_DATE,
            'endDate' => Column::TYPE_DATE,
            'origin' => Column::TYPE_TINYTEXT,
            'status' => Column::TYPE_ENUM,
            'seasonCount' => Column::TYPE_INT,
            'episodeCount' => Column::TYPE_INT,
            'episodeLength' => Column::TYPE_INT,
            'animeType' => Column::TYPE_ENUM,
            'synopsis' => Column::TYPE_TEXT,
            'averageRating' => Column::TYPE_DOUBLE,
            'ratingRank' => Column::TYPE_INT,
            'popularity' => Column::TYPE_INT,
            'userCount' => Column::TYPE_INT,
            'favoritesCount' => Column::TYPE_INT,
            'reviewCount' => Column::TYPE_INT,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);


        $this->hasMany(
            'id',
            Episode::class,
            'animeId',
            [
                'alias' => 'episodes',
                'params' => [
                    'order' => [
                        'number ASC',
                    ],
                ],
            ]
        );

        $this->hasMany(
            'id',
            Season::class,
            'animeId',
            [
                'alias' => 'seasons',
                'params' => [
                    'order' => [
                        'number ASC',
                    ],
                ],
            ]
        );

        $this->hasManyToMany(
            'id',
            GenreRelationships::class,
            'animeId',
            'genreId',
            Genre::class,
            'id',
            [
                'alias' => 'genres',
            ]
        );

        $this->hasManyToMany(
            'id',
            ThemeRelationships::class,
            'animeId',
            'themeId',
            Theme::class,
            'id',
            [
                'alias' => 'themes',
            ]
        );

        $this->hasMany(
            'id',
            Staff::class,
            'animeId',
            [
                'alias' => 'staff',
            ]
        );

        $this->hasMany(
            'id',
            Review::class,
            'animeId',
            [
                'alias' => 'reviews',
                'params' => [
                    'order' => [
                        'updatedAt DESC',
                    ],
                ],
            ]
        );

        $this->hasMany(
            'id',
            Franchise::class,
            'sourceId',
            [
                'alias' => 'franchise',
                'params' => [
                    'conditions' => "franchise_sourcetype = :sourceType",
                    'bind' => [
                        'sourceType' => $this->getMetaData()->getSource(),
                    ],
                ],
            ]
        );

        $user = User::fromAccessToken();
        $this->hasOne(
            'id',
            AnimeEntry::class,
            'animeId',
            [
                'alias' => 'anime-entry',
                'params' => [
                    'conditions' => 'animeentry_userid = :userId',
                    'bind' => [
                        'userId' => (($user instanceof User) ? $user->getId() : 0),
                    ],
                ],
            ]
        );
    }

    public function beforeCreate(): bool
    {
        if (!isset($this->slug))
            $this->slug = Slug::generate($this->title);

        return true;
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

    public function afterGet()
    {
        $this->getWriteConnection()->execute(
            "
            UPDATE
                anime
            SET
                anime_seasoncount =(
                    SELECT
                        COALESCE(MAX(episode_seasonnumber), 0)
                    FROM
                        episode
                    WHERE
                        episode_animeid = anime_id
                ),
                anime_episodecount =(
                    SELECT
                        COUNT(*)
                    FROM
                        episode
                    WHERE
                        episode_animeid = anime_id
                ),
                anime_rating =(
                    SELECT
                        AVG(animeentry_rating)
                    FROM
                        animeentry
                    WHERE
                        animeentry_animeid = anime_id AND animeentry_rating IS NOT NULL
                    GROUP BY
                        animeentry_animeid
                ),
                anime_usercount =(
                    SELECT
                        COUNT(*)
                    FROM
                        animeentry
                    WHERE
                        animeentry_animeid = anime_id AND animeentry_isadd = 1
                ),
                anime_favoritescount =(
                    SELECT
                        COUNT(*)
                    FROM
                        animeentry
                    WHERE
                        animeentry_animeid = anime_id AND animeentry_isfavorites = 1
                ),
                anime_popularity =(
                    SELECT
                        COALESCE(
                            (anime_usercount + anime_favoritescount) +
                            anime_usercount * COALESCE(anime_rating, 0) +
                            2 * COUNT(animeentry_id) * COALESCE(anime_rating, 0) *(anime_usercount + anime_favoritescount),
                            0
                        )
                    FROM
                        animeentry
                    WHERE
                        animeentry_animeid = anime_id AND animeentry_updatedat BETWEEN(NOW() - INTERVAL 7 DAY) AND NOW()
                )
            WHERE
                anime_id = :animeId",
            [
                'animeId' => $this->id
            ]
        );
    }


    public static function routerGroup(Application $app): RouterGroup
    {
        $anime = new RouterGroup();

        $anime->get(
            '/anime',
            function () {
                return Anime::getList(JsonApi::getParameters());
            }
        );

        $anime->post(
            '/anime',
            function () use ($app) {
                $anime = Anime::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($anime->create())
                    return Anime::get($anime->getId());
                else
                    return null; // throw error
            }
        );

        $anime->get(
            '/anime/{id:[0-9]+}',
            function ($id) {
                return Anime::get($id);
            }
        );

        $anime->patch(
            '/anime/{id:[0-9]+}',
            function ($id) use ($app) {
                $anime = Anime::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($anime->update())
                    return Anime::get($anime->getId());
                else
                    return null; // throw error
            }
        );

        $anime->get(
            '/anime/{id:[0-9]+}/episodes',
            function ($id) {
                return Anime::get($id)->getRelated("episodes", JsonApi::getParameters());
            }
        );

        $anime->get(
            '/anime/{id:[0-9]+}/seasons',
            function ($id) {
                return Anime::get($id)->getRelated("seasons", JsonApi::getParameters());
            }
        );

        $anime->get(
            '/anime/{id:[0-9]+}/genres',
            function ($id) {
                return Anime::get($id)->getRelated("genres", JsonApi::getParameters());
            }
        );

        $anime->get(
            '/anime/{id:[0-9]+}/themes',
            function ($id) {
                return Anime::get($id)->getRelated("themes", JsonApi::getParameters());
            }
        );

        $anime->get(
            '/anime/{id:[0-9]+}/staff',
            function ($id) {
                return Anime::get($id)->getRelated("staff", JsonApi::getParameters());
            }
        );

        $anime->get(
            '/anime/{id:[0-9]+}/reviews',
            function ($id) {
                return Anime::get($id)->getRelated("reviews", JsonApi::getParameters());
            }
        );

        $anime->get(
            '/anime/{id:[0-9]+}/franchise',
            function ($id) {
                return Anime::get($id)->getRelated("franchise", JsonApi::getParameters());
            }
        );

        $anime->get(
            '/anime/{id:[0-9]+}/anime-entry',
            function ($id) {
                return Anime::get($id)->getRelated("anime-entry");
            }
        );

        $anime->get(
            '/trending/anime',
            function () {
                $user = User::fromAccessToken();

                if ($user instanceof User)
                    return Anime::getList([
                        'joins' => [
                            [
                                'type' => "LEFT",
                                'model' => AnimeEntry::class,
                                'conditions' => "anime_id = animeentry_animeid AND animeentry_userid = :userId",
                            ],
                        ],
                        'conditions' => "animeentry_animeid IS NULL OR animeentry_isadd = 0",
                        'bind' => [
                            'userId' => (($user instanceof User) ? $user->getId() : 0)
                        ],
                        'order' => "popularity DESC",
                        'limit' => JsonApi::getLimit(),
                        'offset' => JsonApi::getOffset(),
                    ]);
                else
                    return Anime::getList([
                        'order' => "popularity DESC",
                        'limit' => JsonApi::getLimit(),
                        'offset' => JsonApi::getOffset(),
                    ]);
            }
        );

        return $anime;
    }


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitleEn()
    {
        return $this->title_en;
    }

    public function setTitleEn($title_en)
    {
        $this->title_en = $title_en;
    }

    public function getTitleEnJp()
    {
        return $this->title_en_jp;
    }

    public function setTitleEnJp($title_en_jp)
    {
        $this->title_en_jp = $title_en_jp;
    }

    public function getTitleJaJp()
    {
        return $this->title_ja_jp;
    }

    public function setTitleJaJp($title_ja_jp)
    {
        $this->title_ja_jp = $title_ja_jp;
    }

    public function setTitles($titles)
    {
        foreach (get_object_vars($titles) as $key => $value) {
            $this->{'title_' . $key} = $value;
        }
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getCoverImage()
    {
        if (isset($this->coverImage))
            return $this->coverImage;
        else if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/anime/cover/' . $this->slug . '.jpg'))
            return 'http://mangajap.000webhostapp.com/images/anime/cover/' . $this->slug . '.jpg';
        else if (true)
            return 'https://firebasestorage.googleapis.com/v0/b/mangajap.appspot.com/o/anime%2F' . $this->id . '%2Fimages%2Fcover.jpg?alt=media';
        else {
            $coverImage = [];

            $coverImage['tiny'] = "https://mangajap.000webhostapp.com/media/anime/cover/{$this->slug}/tiny.jpeg";
            $coverImage['small'] = "https://mangajap.000webhostapp.com/media/anime/cover/{$this->slug}/small.jpeg";
            $coverImage['medium'] = "https://mangajap.000webhostapp.com/media/anime/cover/{$this->slug}/medium.jpeg";
            $coverImage['large'] = "https://mangajap.000webhostapp.com/media/anime/cover/{$this->slug}/large.jpeg";
            $coverImage['original'] = "https://mangajap.000webhostapp.com/media/anime/cover/{$this->slug}/original.jpeg";;

            return $coverImage;
        }
    }

    public function setCoverImage($coverImage)
    {
        $this->slug = Anime::get($this->id)->slug ?? Slug::generate($this->title);
        $coverImagePath = $_SERVER['DOCUMENT_ROOT'] . '/images/anime/cover/' . $this->slug . '.jpg';

        if ($coverImage === null) {
            if (file_exists($coverImagePath))
                unlink($coverImagePath);
        } else {
            if (substr($coverImage, 0, 4) === "data") {
                $coverImage = explode(',', $coverImage)[1];
            }
            $image = imagecreatefromstring(base64_decode(str_replace(' ', '+', $coverImage)));
            imagejpeg($image, $coverImagePath);
        }
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getSeasonCount()
    {
        return $this->seasonCount;
    }

    public function setSeasonCount($seasonCount)
    {
        $this->seasonCount = $seasonCount;
    }

    public function getEpisodeCount()
    {
        return $this->episodeCount;
    }

    public function setEpisodeCount($episodeCount)
    {
        $this->episodeCount = $episodeCount;
    }

    public function getEpisodeLength()
    {
        return $this->episodeLength;
    }

    public function setEpisodeLength($episodeLength)
    {
        $this->episodeLength = $episodeLength;
    }

    public function setTotalLength($totalLength)
    {
        $this->totalLength = $totalLength;
    }

    public function getTotalLength()
    {
        return $this->getEpisodeCount() * $this->getEpisodeLength();
    }

    public function getAnimeType()
    {
        return $this->animeType;
    }

    public function setAnimeType($animeType)
    {
        $this->animeType = $animeType;
    }

    public function getSynopsis()
    {
        return $this->synopsis;
    }

    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;
    }

    public function getAverageRating()
    {
        return $this->averageRating;
    }

    public function setAverageRating($averageRating)
    {
        $this->averageRating = $averageRating;
    }

    public function getRatingRank()
    {
        return $this->ratingRank;
    }

    public function setRatingRank($ratingRank)
    {
        $this->ratingRank = $ratingRank;
    }

    public function getPopularity()
    {
        return $this->popularity;
    }

    public function setPopularity($popularity)
    {
        $this->popularity = $popularity;
    }

    public function getUserCount()
    {
        return $this->userCount;
    }

    public function setUserCount($userCount)
    {
        $this->userCount = $userCount;
    }

    public function getFavoritesCount()
    {
        return $this->favoritesCount;
    }

    public function setFavoritesCount($favoritesCount)
    {
        $this->favoritesCount = $favoritesCount;
    }

    public function getReviewCount()
    {
        return $this->reviewCount;
    }

    public function setReviewCount($reviewCount)
    {
        $this->reviewCount = $reviewCount;
    }

    public function getYoutubeVideoId()
    {
        return $this->youtubeVideoId;
    }

    public function setYoutubeVideoId($youtubeVideoId)
    {
        $this->youtubeVideoId = $youtubeVideoId;
    }



    public function JsonApi_type()
    {
        return "anime";
    }

    public function JsonApi_id()
    {
        return $this->getId();
    }

    public function JsonApi_attributes()
    {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'canonicalTitle' => $this->title, // TODO: DEPRECATED use title
            'title' => $this->title,
            'titles' => [
                'fr' => $this->title_fr,
                'en' => $this->title_en,
                'en_jp' => $this->title_en_jp,
                'ja_jp' => $this->title_ja_jp,
            ],
            'slug' => $this->slug,
            'synopsis' => $this->synopsis,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'origin' => $this->origin,
            'status' => $this->status,
            'animeType' => $this->animeType,
            'seasonCount' => $this->seasonCount,
            'episodeCount' => $this->episodeCount,
            'episodeLength' => $this->episodeLength,
            'totalLength' => $this->getTotalLength(),
            'averageRating' => $this->averageRating,
            'ratingRank' => $this->ratingRank,
            'popularity' => $this->popularity,
            'userCount' => $this->userCount,
            'favoritesCount' => $this->favoritesCount,
            'reviewCount' => $this->reviewCount,
            'coverImage' => $this->getCoverImage(),
            'youtubeVideoId' => $this->youtubeVideoId,
        ];
    }

    public function JsonApi_relationships()
    {
        $relationships = [
            'episodes' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'seasons' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'genres' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'themes' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'staff' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'reviews' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'franchise' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];

        if (\App\OAuth::getBearerToken() != null)
            $relationships['anime-entry'] = [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ];

        return $relationships;
    }

    public function JsonApi_filter()
    {
        return [
            'query' => [
                'conditions' => [
                    'anime_title LIKE CONCAT("%",:query,"%") OR
                    anime_title_fr LIKE CONCAT("%",:query,"%") OR
                    anime_title_en LIKE CONCAT("%",:query,"%") OR
                    anime_title_en_jp LIKE CONCAT("%",:query,"%") OR
                    anime_title_ja_jp LIKE CONCAT("%",:query,"%")'
                ],
                'order' => [
                    'CASE
                        WHEN anime_title LIKE CONCAT(:query,"%") THEN 0
                        WHEN anime_title_fr LIKE CONCAT(:query,"%") THEN 1
                        WHEN anime_title_en LIKE CONCAT(:query,"%") THEN 2
                        WHEN anime_title_en_jp LIKE CONCAT(:query,"%") THEN 3
                        WHEN anime_title_ja_jp LIKE CONCAT(:query,"%") THEN 4
                        ELSE 5
                    END',
                ],
            ],
        ];
    }
}
