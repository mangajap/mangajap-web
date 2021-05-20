<?php

use App\Application;
use App\Database\Column;
use App\HTTP;
use App\JsonApi;
use App\JsonApi\Document\Errors;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiException;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;
use App\Security\Password;
use App\Security\Random;
use App\Utils\Slug;

class User extends Model implements JsonApiSerializable {

    private static $selfUser = 'null';

    public $id;
    public $createdAt;
    public $updatedAt;
    public $accessToken = 'null';
    public $pseudo;
    public $slug;
    public $isAdmin;
    public $isPremium;
    public $about;
    public $followersCount;
    public $followingCount;
    public $followedMangaCount;
    public $volumesRead;
    public $chaptersRead;
    public $followedAnimeCount;
    public $episodesWatch;
    public $timeSpentOnAnime;
    public $firstName;
    public $lastName;
    public $birthday;
    public $gender;
    public $country;
    public $email;
    public $password;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('user');

        $this->setColumnMap([
            'id' => 'user_id',
            'accessToken' => 'user_accesstoken',
            'pseudo' => 'user_pseudo',
            'slug' => 'user_slug',
            'email' => 'user_email',
            'password' => 'user_password',
            'isAdmin' => 'user_isadmin',
            'isPremium' => 'user_ispremium',
            'firstName' => 'user_firstname',
            'lastName' => 'user_lastname',
            'about' => 'user_about',
            'gender' => 'user_gender',
            'birthday' => 'user_birthday',
			'country' => 'user_country',
            'followersCount' => 'user_followerscount',
            'followingCount' => 'user_followingcount',
            'followedMangaCount' => 'user_followedmangacount',
            'volumesRead' => 'user_mangavolumesread',
            'chaptersRead' => 'user_mangachaptersread',
            'followedAnimeCount' => 'user_followedanimecount',
            'episodesWatch' => 'user_animeepisodeswatch',
            'timeSpentOnAnime' => 'user_animetimespent',
            'createdAt' => 'user_createdat',
            'updatedAt' => 'user_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'accessToken',
            'pseudo',
            'slug',
            'email',
            'password',
            'isAdmin',
            'isPremium',
            'firstName',
            'lastName',
            'about',
            'gender',
            'birthday',
			'country',
            'followersCount',
            'followingCount',
            'followedMangaCount',
            'volumesRead',
            'chaptersRead',
            'followedAnimeCount',
            'episodesWatch',
            'timeSpentOnAnime',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'accessToken' => Column::TYPE_CHAR,
            'pseudo' => Column:: TYPE_VARCHAR,
            'slug' => Column::TYPE_TINYTEXT,
            'email' => Column::TYPE_TINYTEXT,
            'password' => Column::TYPE_TINYTEXT,
            'isAdmin' => Column::TYPE_BOOLEAN,
            'isPremium' => Column::TYPE_BOOLEAN,
            'firstName' => Column::TYPE_TEXT,
            'lastName' => Column::TYPE_TEXT,
            'about' => Column::TYPE_TEXT,
            'gender' => Column::TYPE_ENUM,
            'birthday' => Column::TYPE_DATE,
            'country' => Column::TYPE_TINYTEXT,
            'followersCount' => Column::TYPE_INT,
            'followingCount' => Column::TYPE_INT,
            'followedMangaCount' => Column::TYPE_INT,
            'volumesRead' => Column::TYPE_INT,
            'chaptersRead' => Column::TYPE_INT,
            'followedAnimeCount' => Column::TYPE_INT,
            'episodesWatch' => Column::TYPE_INT,
            'timeSpentOnAnime' => Column::TYPE_INT,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);


        $this->hasMany(
            'id',
            Follow::class,
            'followedId',
            [
                'alias' => 'followers'
            ]
        );

        $this->hasMany(
            'id',
            Follow::class,
            'followerId',
            [
                'alias' => 'following'
            ]
        );

        $this->hasMany(
            'id',
            MangaEntry::class,
            'userId',
            [
                'alias' => 'manga-library',
                'params' => [
                    'conditions' => 'mangaentry_isadd = TRUE',
                    'order' => [
                        'updatedAt DESC',
                    ],
                    'limit' => 20,
                ],
            ]
        );

        $this->hasMany(
            'id',
            AnimeEntry::class,
            'userId',
            [
                'alias' => 'anime-library',
                'params' => [
                    'conditions' => 'animeentry_isadd = TRUE',
                    'order' => [
                        'updatedAt DESC',
                    ],
                    'limit' => 20,
                ]
            ]
        );

        $this->hasMany(
            'id',
            MangaEntry::class,
            'userId',
            [
                'alias' => 'manga-favorites',
                'params' => [
                    'conditions' => [
                        'mangaentry_isadd = TRUE',
                        'mangaentry_isfavorites = TRUE'
                    ],
                    'order' => [
                        'updatedAt DESC',
                    ],
                    'limit' => 20,
                ]
            ]
        );

        $this->hasMany(
            'id',
            AnimeEntry::class,
            'userId',
            [
                'alias' => 'anime-favorites',
                'params' => [
                    'conditions' => [
                        'animeentry_isadd = TRUE',
                        'animeentry_isfavorites = TRUE'
                    ],
                    'order' => [
                        'updatedAt DESC',
                    ],
                    'limit' => 20,
                ],
            ]
        );

        $this->hasMany(
            'id',
            Review::class,
            'userId',
            [
                'alias' => 'reviews',
            ]
        );

        $this->hasMany(
            'id',
            Request::class,
            'userId',
            [
                'alias' => 'requests',
            ]
        );
    }

    public function afterGet() {
        $this->getWriteConnection()->execute(
            "
            UPDATE
                user
            SET
                user_followerscount =(
                    SELECT
                        COUNT(*)
                    FROM
                        follow
                    WHERE
                        follow_followedid = user_id
                ),
                user_followingcount =(
                    SELECT
                        COUNT(*)
                    FROM
                        follow
                    WHERE
                        follow_followerid = user_id
                ),
                user_followedmangacount =(
                    SELECT
                        COUNT(*)
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_userid = user_id AND mangaentry_isadd = 1
                ),
                user_mangavolumesread =(
                    SELECT
                        COALESCE(
                            SUM(
                                mangaentry_volumesread *(mangaentry_rereadcount +1)
                            ),
                            0
                        )
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_userid = user_id
                ),
                user_mangachaptersread =(
                    SELECT
                        COALESCE(
                            SUM(
                                mangaentry_chaptersread *(mangaentry_rereadcount +1)
                            ),
                            0
                        )
                    FROM
                        mangaentry
                    WHERE
                        mangaentry_userid = user_id
                ),
                user_followedanimecount =(
                    SELECT
                        COUNT(*)
                    FROM
                        animeentry
                    WHERE
                        animeentry_userid = user_id AND animeentry_isadd = 1
                ),
                user_animeepisodeswatch =(
                    SELECT
                        COALESCE(
                            SUM(
                                animeentry_episodeswatch *(animeentry_rewatchcount +1)
                            ),
                            0
                        )
                    FROM
                        animeentry
                    WHERE
                        animeentry_userid = user_id
                ),
                user_animetimespent =(
                    SELECT
                        COALESCE(
                            SUM(
                                animeentry_episodeswatch * TIME_TO_SEC(anime_episodelength)
                            ),
                            0
                        )
                    FROM
                        animeentry
                    RIGHT OUTER JOIN anime ON anime_id = animeentry_animeid
                    WHERE
                        animeentry_userid = user_id
                )
            WHERE
                user_id = :userId",
            [
                'userId' => $this->getId()
            ]
        );
    }

    public function beforeCreate(): bool {
        $this->setAccessToken(Random::hex(64));

        if (!isset($this->slug))
            $this->slug = Slug::generate($this->pseudo);

        return true;
    }

    public function beforeUpdate(): bool {
        $user = User::fromAccessToken();

        if (!$user instanceof User)
            return false;

        if ($user->getId() == $this->getId())
            return true;

        if ($user->isAdmin())
            return true;

        return false;
    }

    public function beforeSave(): bool {
        if ($this->isPseudoTaken())
            return false;

        if ($this->isEmailTaken())
            return false;

        if (isset($this->password) && $this->password !== 'null')
            $this->setPassword(Password::hash($this->password));

        return true;
    }


    public static function fromAccessToken($accessToken = null) {
        if (!isset($accessToken))
            $accessToken = \App\OAuth::getBearerToken();

        $user = self::$selfUser;

        if ($user === 'null') {
            $user = User::get([
                'conditions' => 'user_accesstoken = :accessToken',
                'bind' => [
                    'accessToken' => $accessToken,
                ],
                'limit' => 1,
            ]);
        }
        elseif ($user instanceof User) {
            if ($user->getAccessToken() !== $accessToken)
                $user = User::get([
                    'conditions' => 'user_accesstoken = :accessToken',
                    'bind' => [
                        'accessToken' => $accessToken,
                    ],
                    'limit' => 1,
                ]);
        }

        self::$selfUser = $user;
        return self::$selfUser;
    }

    public function isPseudoTaken(): bool {
        $user = self::get([
            'conditions' => 'user_pseudo = :pseudo AND user_id != :id',
            'bind' => [
                'pseudo' => $this->getPseudo(),
                'id' => $this->getId(),
            ],
            'limit' => 1,
        ]);

        if ($user instanceof User)
            return true;
        else
            return false;
    }

    public function isEmailTaken(): bool {
        $user = self::get([
            'conditions' => 'user_email = :email AND user_id != :id',
            'bind' => [
                'email' => $this->email,
                'id' => $this->getId(),
            ],
            'limit' => 1,
        ]);

        if ($user instanceof User)
            return true;
        else
            return false;
    }

    public function isAccessTokenTaken(): bool {
        $user = self::get([
            'conditions' => 'user_accesstoken = :accessToken AND user_id != :id',
            'bind' => [
                'accessToken' => $this->getAccessToken(),
                'id' => $this->getId(),
            ],
            'limit' => 1,
        ]);

        if ($user instanceof User)
            return true;
        else
            return false;
    }


    public static function routerGroup(Application $app): RouterGroup {
        $users = new RouterGroup();

        $users->get(
            '/users',
            function() {
                return User::getList(JsonApi::getParameters());
            }
        );

        $users->post(
            '/users',
            function() use ($app) {
                $user = User::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($user->create())
                    return User::get($user->getId());
                else
                    return null;
            }
        );

        $users->get(
            '/users/{id:[0-9]+}',
            function($id) {
                return User::get($id);
            }
        );

        $users->patch(
            '/users/{id:[0-9]+}',
            function($id) use ($app) {
                $user = User::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($user->update())
                    return User::get($user->getId());
                else
                    return null;
            }
        );

        $users->get(
            '/users/{id:[0-9]+}/followers',
            function($id) {
                return User::get($id)->getRelated("followers", JsonApi::getParameters());
            }
        );

        $users->get(
            '/users/{id:[0-9]+}/following',
            function($id) {
                return User::get($id)->getRelated("following", JsonApi::getParameters());
            }
        );

        $users->get(
            '/users/{id:[0-9]+}/manga-library',
            function($id) {
                return User::get($id)->getRelated("manga-library", JsonApi::getParameters());
            }
        );

        $users->get(
            '/users/{id:[0-9]+}/anime-library',
            function($id) {
                return User::get($id)->getRelated("anime-library", JsonApi::getParameters());
            }
        );

        $users->get(
            '/users/{id:[0-9]+}/manga-favorites',
            function($id) {
                return User::get($id)->getRelated("manga-favorites", JsonApi::getParameters());
            }
        );

        $users->get(
            '/users/{id:[0-9]+}/anime-favorites',
            function($id) {
                return User::get($id)->getRelated("anime-favorites", JsonApi::getParameters());
            }
        );

        $users->get(
            '/users/{id:[0-9]+}/reviews',
            function($id) {
                return User::get($id)->getRelated("reviews", JsonApi::getParameters());
            }
        );

        $users->get(
            '/users/{id:[0-9]+}/requests',
            function($id) {
                return User::get($id)->getRelated("requests", JsonApi::getParameters());
            }
        );

        return $users;
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

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken) {
        do {
            $this->accessToken = $accessToken;
        }
        while ($this->isAccessTokenTaken());
    }

    public function getPseudo() {
        return $this->pseudo;
    }

    public function setPseudo($pseudo) {
        $this->pseudo = $pseudo;

        if ($this->isPseudoTaken()) {
            throw new Errors(
                new JsonApiException(
                    null,
                    null,
                    HTTP::CODE_UNPROCESSABLE_ENTITY,
                    null,
                    "Invalid attribute",
                    "The pseudo is already taken",
                    "/data/attributes/pseudo",
                    null,
                    null
                )
            );
        }
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function isAdmin() {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin) {
//        $this->isAdmin = $isAdmin;
    }

    public function isPremium() {
        return $this->isPremium;
    }

    public function setIsPremium($isPremium) {
//        $this->isPremium = $isPremium;
    }

    public function getAbout() {
        return $this->about;
    }

    public function setAbout($about) {
        $this->about = $about;
    }

    public function getFollowersCount() {
        return $this->followersCount;
    }

    public function setFollowersCount($followersCount) {
        $this->followersCount = $followersCount;
    }

    public function getFollowingCount() {
        return $this->followingCount;
    }

    public function setFollowingCount($followingCount) {
        $this->followingCount = $followingCount;
    }

    public function getFollowedMangaCount() {
        return $this->followedMangaCount;
    }

    public function setFollowedMangaCount($followedMangaCount) {
        $this->followedMangaCount = $followedMangaCount;
    }

    public function getVolumesRead() {
        return $this->volumesRead;
    }

    public function setVolumesRead($volumesRead) {
        $this->volumesRead = $volumesRead;
    }

    public function getChaptersRead() {
        return $this->chaptersRead;
    }

    public function setChaptersRead($chaptersRead) {
        $this->chaptersRead = $chaptersRead;
    }

    public function getFollowedAnimeCount() {
        return $this->followedAnimeCount;
    }

    public function setFollowedAnimeCount($followedAnimeCount) {
        $this->followedAnimeCount = $followedAnimeCount;
    }

    public function getEpisodesWatch() {
        return $this->episodesWatch;
    }

    public function setEpisodesWatch($episodesWatch) {
        $this->episodesWatch = $episodesWatch;
    }

    public function getTimeSpentOnAnime() {
        return $this->timeSpentOnAnime;
    }

    public function setTimeSpentOnAnime($timeSpentOnAnime) {
        $this->timeSpentOnAnime = $timeSpentOnAnime;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function getBirthday() {
        return $this->birthday;
    }

    public function setBirthday($birthday) {
        $this->birthday = $birthday;
    }

    public function getGender() {
        return $this->gender;
    }

    public function setGender($gender) {
        $this->gender = $gender;
    }
	
	public function getCountry() {
		return $this->country;
	}
	
	public function setCountry($country) {
		$this->country = $country;
	}

    public function getAvatar() {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/user/avatar/'.$this->getId().'.jpg')) {
            $avatar = [];

            $avatar['tiny'] = 'https://mangajap.000webhostapp.com/media/users/avatars/'.$this->getId().'/tiny.jpeg';
            $avatar['small'] = 'https://mangajap.000webhostapp.com/media/users/avatars/'.$this->getId().'/small.jpeg';
            $avatar['medium'] = 'https://mangajap.000webhostapp.com/media/users/avatars/'.$this->getId().'/medium.jpeg';
            $avatar['large'] = 'https://mangajap.000webhostapp.com/media/users/avatars/'.$this->getId().'/large.jpeg';
            $avatar['original'] = 'https://mangajap.000webhostapp.com/media/users/avatars/'.$this->getId().'/original.jpeg';

            return $avatar;
        }

        return null;
    }

    public function setAvatar($avatar) {
        $avatarPath = $_SERVER['DOCUMENT_ROOT'] . '/images/user/avatar/'.$this->getId().'.jpg';
        if ($avatar === null) {
            if (file_exists($avatarPath))
                unlink($avatarPath);
        }
        else {
            if (substr($avatar, 0, 4 ) === "data") {
                $avatar = explode(',', $avatar)[1];
            }
            $image = imagecreatefromstring(base64_decode(str_replace(' ','+', $avatar)));
            $size = min(imagesx($image), imagesy($image));
            $image = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);

            imagejpeg($image, $avatarPath);
        }
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;

        if ($this->isEmailTaken()) {
            throw new Errors(
                new JsonApiException(
                    null,
                    null,
                    HTTP::CODE_UNPROCESSABLE_ENTITY,
                    null,
                    "Invalid attribute",
                    "The email is already taken",
                    "/data/attributes/email",
                    null,
                    null
                )
            );
        }
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }





    public function JsonApi_type() {
        return "users";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        $attributes = [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'pseudo' => $this->pseudo,
            'slug' => $this->slug,
            'isAdmin' => $this->isAdmin,
            'isPremium' => $this->isPremium,
            'about' => $this->about,
            'followersCount' => $this->followersCount,
            'followingCount' => $this->followingCount,
            'followedMangaCount' => $this->followedMangaCount,
            'volumesRead' => $this->volumesRead,
            'chaptersRead' => $this->chaptersRead,
            'followedAnimeCount' => $this->followedAnimeCount,
            'episodesWatch' => $this->episodesWatch,
            'timeSpentOnAnime' => $this->timeSpentOnAnime,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'birthday' => $this->birthday,
            'gender' => $this->gender,
			'country' => $this->country,
            'avatar' => $this->getAvatar(),
        ];

        $user = User::fromAccessToken();
        if ($user instanceof User && $user->getId() == $this->getId()) {
            $attributes['email'] = $this->email;
            $attributes['password'] = null;
        }
        
        return $attributes;
    }

    public function JsonApi_relationships() {
        return [
            'followers' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'following' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'manga-library' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'anime-library' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'manga-favorites' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'anime-favorites' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'reviews' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'requests' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];
    }

    public function JsonApi_filter() {
        return [
            'self' => [
                'conditions' => 'user_accesstoken = :accessToken',
                'bind' => [
                    'accessToken' => \App\OAuth::getBearerToken(),
                ],
                'limit' => 1,
            ],
            'query' => [
                'conditions' => [
                    'user_pseudo LIKE CONCAT("%",:query,"%") OR 
                    user_slug LIKE CONCAT("%",:query,"%")'
                ],
                'order' => [
                    'CASE 
                        WHEN user_pseudo LIKE CONCAT(:query,"%") THEN 0 
                        WHEN user_slug LIKE CONCAT(:query,"%") THEN 1
                        ELSE 2
                    END',
                ],
            ],
        ];
    }
}