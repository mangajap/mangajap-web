<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;
use App\Utils\Slug;

class Manga extends Model implements JsonApiSerializable {

    public $id;
    public $createdAt;
    public $updatedAt;
    public $canonicalTitle;
    public $title_fr;
    public $title_en;
    public $title_en_jp;
    public $title_ja_jp;
    public $slug;
    public $coverImage;
    public $bannerImage;
    public $startDate;
    public $endDate;
    public $origin;
    public $status;
    public $volumeCount;
    public $chapterCount;
    public $mangaType;
    public $synopsis;
    public $averageRating;
    public $ratingRank;
    public $popularity;
    public $userCount;
    public $favoritesCount;
    public $reviewCount;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('manga');

        $this->setColumnMap([
            'id' => 'manga_id',
            'canonicalTitle' => 'manga_title',
            'title_fr' => 'manga_title_fr',
            'title_en' => 'manga_title_en',
            'title_en_jp' => 'manga_title_en_jp',
            'title_ja_jp' => 'manga_title_ja_jp',
            'slug' => 'manga_slug',
            'startDate' => 'manga_releasedate',
            'endDate' => 'manga_enddate',
            'origin' => 'manga_origin',
            'status' => 'manga_status',
            'volumeCount' => 'manga_volumecount',
            'chapterCount' => 'manga_chaptercount',
            'mangaType' => 'manga_type',
            'synopsis' => 'manga_synopsis',
            'averageRating' => 'manga_rating',
            'ratingRank' => 'manga_ratingrank',
            'popularity' => 'manga_popularity',
            'userCount' => 'manga_usercount',
            'favoritesCount' => 'manga_favoritescount',
            'reviewCount' => 'manga_reviewcount',
            'createdAt' => 'manga_createdat',
            'updatedAt' => 'manga_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'canonicalTitle',
            'title_fr',
            'title_en',
            'title_en_jp',
            'title_ja_jp',
            'slug',
            'startDate',
            'endDate',
            'origin',
            'status',
            'volumeCount',
            'chapterCount',
            'mangaType',
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
            'canonicalTitle' => Column::TYPE_TINYTEXT,
            'title_fr' => Column::TYPE_TINYTEXT,
            'title_en' => Column::TYPE_TINYTEXT,
            'title_en_jp' => Column::TYPE_TINYTEXT,
            'title_ja_jp' => Column::TYPE_TINYTEXT,
            'slug' => Column::TYPE_TINYTEXT,
            'startDate' => Column::TYPE_DATE,
            'endDate' => Column::TYPE_DATE,
            'origin' => Column::TYPE_TINYTEXT,
            'status' => Column::TYPE_ENUM,
            'volumeCount' => Column::TYPE_INT,
            'chapterCount' => Column::TYPE_INT,
            'mangaType' => Column::TYPE_ENUM,
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
            Volume::class,
            'mangaId',
            [
                'alias' => 'volumes',
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
            'mangaId',
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
            'mangaId',
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
            'mangaId',
            [
                'alias' => 'staff',
            ]
        );

        $this->hasMany(
            'id',
            Review::class,
            'mangaId',
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
            MangaEntry::class,
            'mangaId',
            [
                'alias' => 'manga-entry',
                'params' => [
                    'conditions' => 'mangaentry_userid = :userId',
                    'bind' => [
                        'userId' => (($user instanceof User) ? $user->getId() : 0),
                    ],
                ],
            ]
        );
    }

    public function beforeCreate(): bool {
        if (!isset($this->slug))
            $this->slug = Slug::generate($this->canonicalTitle);

        return true;
    }

    public function beforeSave(): bool {
        $user = User::fromAccessToken();

        if (!$user instanceof User)
            return false;

        if (!$user->isAdmin())
            return false;

        return true;
    }

    public function afterGet() {
        $this->getWriteConnection()->execute(
            "
            UPDATE
                manga
            SET
                manga_rating = (
                    SELECT
                        AVG(mangaentry_rating)
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_mangaid = manga_id AND mangaentry_rating IS NOT NULL
                    GROUP BY
                        mangaentry_mangaid
                ),
                manga_usercount = (
                    SELECT
                        COUNT(*)
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_mangaid = manga_id AND mangaentry_isadd = 1
                ),
                manga_favoritescount = (
                    SELECT
                        COUNT(*)
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_mangaid = manga_id AND mangaentry_isfavorites = 1
                ),
                manga_reviewcount = (
                    SELECT
                        COUNT(*)
                    FROM
                        review
                    WHERE
                        review_mangaid = manga_id
                ),
                manga_popularity = (
                    SELECT
                        COALESCE(
                            (manga_usercount + manga_favoritescount) +
                            manga_usercount * COALESCE(manga_rating, 0) +
                            2 * COUNT(mangaentry_id) * COALESCE(manga_rating, 0) *(manga_usercount + manga_favoritescount),
                            0
                        )
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_mangaid = manga_id AND mangaentry_updatedat BETWEEN(NOW() - INTERVAL 7 DAY) AND NOW()
                )
            WHERE
                manga_id = :mangaId",
            [
                'mangaId' => $this->getId()
            ]
        );
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    public function setCanonicalTitle($canonicalTitle) {
        $this->canonicalTitle = $canonicalTitle;
    }

    public function setTitleEn($title_en) {
        $this->title_en = $title_en;
    }

    public function setTitleEnJp($title_en_jp) {
        $this->title_en_jp = $title_en_jp;
    }

    public function setTitleJaJp($title_ja_jp) {
        $this->title_ja_jp = $title_ja_jp;
    }

    public function setTitles($titles) {
        foreach (get_object_vars($titles) as $key => $value) {
            $this->{'title_'.$key} = $value;
        }
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    public function setOrigin($origin) {
        $this->origin = $origin;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setVolumeCount($volumeCount) {
        $this->volumeCount = $volumeCount;
    }

    public function setChapterCount($chapterCount) {
        $this->chapterCount = $chapterCount;
    }

    public function setMangaType($mangaType) {
        $this->mangaType = $mangaType;
    }

    public function setSynopsis($synopsis) {
        $this->synopsis = $synopsis;
    }

    public function setAverageRating($averageRating) {
        $this->averageRating = $averageRating;
    }

    public function setRatingRank($ratingRank) {
        $this->ratingRank = $ratingRank;
    }

    public function setPopularity($popularity) {
        $this->popularity = $popularity;
    }

    public function setUserCount($userCount) {
        $this->userCount = $userCount;
    }

    public function setFavoritesCount($favoritesCount) {
        $this->favoritesCount = $favoritesCount;
    }

    public function setReviewCount($reviewCount) {
        $this->reviewCount = $reviewCount;
    }

    public function getCoverImage() {
        if (isset($this->coverImage))
            return $this->coverImage;
        else if (true)
            return 'http://mangajap.000webhostapp.com/images/manga/cover/'. $this->slug .'.jpg';
        else {
            $coverImage = [];

            $coverImage['tiny'] = "https://mangajap.000webhostapp.com/media/manga/cover/{$this->slug}/tiny.jpeg";
            $coverImage['small'] = "https://mangajap.000webhostapp.com/media/manga/cover/{$this->slug}/small.jpeg";
            $coverImage['medium'] = "https://mangajap.000webhostapp.com/media/manga/cover/{$this->slug}/medium.jpeg";
            $coverImage['large'] = "https://mangajap.000webhostapp.com/media/manga/cover/{$this->slug}/large.jpeg";
            $coverImage['original'] = "https://mangajap.000webhostapp.com/media/manga/cover/{$this->slug}/original.jpeg";;

            return $coverImage;
        }
    }

    public function setCoverImage($coverImage) {
        if (!isset(Manga::get($this->id)->slug))
            $this->slug = Slug::generate($this->canonicalTitle);
        $coverImagePath = $_SERVER['DOCUMENT_ROOT'] . '/images/manga/cover/'.$this->slug.'.jpg';

        if ($coverImage === null) {
            if (file_exists($coverImagePath))
                unlink($coverImagePath);
        }
        else {
            if (substr($coverImage, 0, 4 ) === "data") {
                $coverImage = explode(',', $coverImage)[1];
            }
            $image = imagecreatefromstring(base64_decode(str_replace(' ','+', $coverImage)));
            imagejpeg($image, $coverImagePath);
        }
    }

    public function getBannerImage() {
        if (isset($this->bannerImage))
            return $this->bannerImage;
        else if (true)
            return 'http://mangajap.000webhostapp.com/images/manga/banner/'. $this->slug.'.jpg';
        else {
            $bannerImage = [];

            $bannerImage['tiny'] = "https://mangajap.000webhostapp.com/media/manga/banner/{$this->slug}/tiny.jpeg";
            $bannerImage['small'] = "https://mangajap.000webhostapp.com/media/manga/banner/{$this->slug}/small.jpeg";
            $bannerImage['medium'] = "https://mangajap.000webhostapp.com/media/manga/banner/{$this->slug}/medium.jpeg";
            $bannerImage['large'] = "https://mangajap.000webhostapp.com/media/manga/banner/{$this->slug}/large.jpeg";
            $bannerImage['original'] = "https://mangajap.000webhostapp.com/media/manga/banner/{$this->slug}/original.jpeg";;

            return $bannerImage;
        }
    }

    public function setBannerImage($bannerImage) {
        if (!isset(Manga::get($this->id)->slug))
            $this->slug = Slug::generate($this->canonicalTitle);
        $coverImagePath = $_SERVER['DOCUMENT_ROOT'] . '/images/manga/banner/'.$this->slug.'.jpg';

        if ($bannerImage === null) {
            if (file_exists($coverImagePath))
                unlink($coverImagePath);
        }
        else {
            if (substr($bannerImage, 0, 4 ) === "data") {
                $bannerImage = explode(',', $bannerImage)[1];
            }
            $image = imagecreatefromstring(base64_decode(str_replace(' ','+', $bannerImage)));
            imagejpeg($image, $coverImagePath);
        }
    }


    public static function routerGroup(Application $app): RouterGroup {
        $manga = new RouterGroup();

        $manga->get(
            '/manga',
            function() {
                return Manga::getList(JsonApi::getParameters());
            }
        );

        $manga->post(
            '/manga',
            function() use ($app) {
                $manga = Manga::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($manga->create())
                    return Manga::get($manga->getId());
                else
                    return null; // throw error
            }
        );

        $manga->get(
            '/manga/{id:[0-9]+}',
            function($id) {
                return Manga::get($id);
            }
        );

        $manga->patch(
            '/manga/{id:[0-9]+}',
            function ($id) use ($app) {
                $manga = Manga::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($manga->update())
                    return Manga::get($manga->getId());
                else
                    return null; // throw error
            }
        );

        $manga->get(
            '/manga/{id:[0-9]+}/volumes',
            function($id) {
                return Manga::get($id)->getRelated("volumes", JsonApi::getParameters());
            }
        );

        $manga->get(
            '/manga/{id:[0-9]+}/genres',
            function($id) {
                return Manga::get($id)->getRelated("genres", JsonApi::getParameters());
            }
        );

        $manga->get(
            '/manga/{id:[0-9]+}/themes',
            function($id) {
                return Manga::get($id)->getRelated("themes", JsonApi::getParameters());
            }
        );

        $manga->get(
            '/manga/{id:[0-9]+}/staff',
            function($id) {
                return Manga::get($id)->getRelated("staff", JsonApi::getParameters());
            }
        );

        $manga->get(
            '/manga/{id:[0-9]+}/reviews',
            function($id) {
                return Manga::get($id)->getRelated("reviews", JsonApi::getParameters());
            }
        );

        $manga->get(
            '/manga/{id:[0-9]+}/franchise',
            function($id) {
                return Manga::get($id)->getRelated("franchise", JsonApi::getParameters());
            }
        );

        $manga->get(
            '/manga/{id:[0-9]+}/manga-entry',
            function($id) {
                return Manga::get($id)->getRelated("manga-entry");
            }
        );

        $manga->get(
            '/trending/manga',
            function() {
                $user = User::fromAccessToken();

                if ($user instanceof User)
                    return Manga::getList([
                        'joins' => [
                            [
                                'type' => "LEFT",
                                'model' => MangaEntry::class,
                                'conditions' => "manga_id = mangaentry_mangaid AND mangaentry_userid = :userId",
                            ],
                        ],
                        'conditions' => "mangaentry_mangaid IS NULL OR mangaentry_isadd = 0",
                        'bind' => [
                            'userId' => (($user instanceof User) ? $user->getId() : 0)
                        ],
                        'order' => "popularity DESC",
                        'limit' => JsonApi::getLimit(),
                        'offset' => JsonApi::getOffset(),
                    ]);
                else
                    return Manga::getList([
                        'order' => "popularity DESC",
                        'limit' => JsonApi::getLimit(),
                        'offset' => JsonApi::getOffset(),
                    ]);
            }
        );

        return $manga;
    }



    public function JsonApi_type() {
        return "manga";
    }

    public function JsonApi_id() {
        return $this->id;
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'canonicalTitle' => $this->canonicalTitle,
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
            'mangaType' => $this->mangaType,
            'volumeCount' => $this->volumeCount,
            'chapterCount' => $this->chapterCount,
            'averageRating' => $this->averageRating,
            'ratingRank' => $this->ratingRank,
            'popularity' => $this->popularity,
            'userCount' => $this->userCount,
            'favoritesCount' => $this->favoritesCount,
            'reviewCount' => $this->reviewCount,
            'coverImage' => $this->getCoverImage(),
            'bannerImage' => $this->getBannerImage(),
        ];
    }

    public function JsonApi_relationships() {
        $relationships = [
            'volumes' => [
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
            $relationships['manga-entry'] = [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ];

        return $relationships;
    }

    public function JsonApi_filter() {
        return [
            'query' => [
                'conditions' => [
                    'manga_title LIKE CONCAT("%",:query,"%") OR
                    manga_title_fr LIKE CONCAT("%",:query,"%") OR
                    manga_title_en LIKE CONCAT("%",:query,"%") OR
                    manga_title_en_jp LIKE CONCAT("%",:query,"%") OR
                    manga_title_ja_jp LIKE CONCAT("%",:query,"%")'
                ],
                'order' => [
                    'CASE
                        WHEN manga_title LIKE CONCAT(:query,"%") THEN 0
                        WHEN manga_title_fr LIKE CONCAT(:query,"%") THEN 1
                        WHEN manga_title_en LIKE CONCAT(:query,"%") THEN 2
                        WHEN manga_title_en_jp LIKE CONCAT(:query,"%") THEN 3
                        WHEN manga_title_ja_jp LIKE CONCAT(:query,"%") THEN 4
                        ELSE 5
                    END',
                ],
            ],
        ];
    }
}
