<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Follow extends Model implements JsonApiSerializable {

    public $id;
    public $followerId;
    public $followedId;
    public $createdAt;
    public $updatedAt;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('follow');

        $this->setColumnMap([
            'id' => 'follow_id',
            'followerId' => 'follow_followerid',
            'followedId' => 'follow_followedid',
            'createdAt' => 'follow_createdat',
            'updatedAt' => 'follow_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'followerId',
            'followedId',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'followerId' => Column::TYPE_INT,
            'followedId' => Column::TYPE_INT,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);

        $this->skipAttributesOnUpdate([
            'followerId',
            'followedId',
        ]);


        $this->belongsTo(
            'followerId',
            User::class,
            'id',
            [
                'alias' => 'follower'
            ]
        );

        $this->belongsTo(
            'followedId',
            User::class,
            'id',
            [
                'alias' => 'followed'
            ]
        );
    }

    public function beforeCreate(): bool {
        if (\App\OAuth::getBearerToken() == null)
            return false;

        if (Follow::get([
                "follow_followerid = :followerId AND follow_followedid = :followedId",
                'bind' => [
                    'followerId' => $this->getFollowerId(),
                    'followedId' => $this->getFollowedId(),
                ],
            ]) != null)
            return false;

        return true;
    }

    public function beforeUpdate(): bool {
        $user = User::fromAccessToken();
        $follow = Follow::get($this->getId());

        if (!$user instanceof User || !$follow instanceof Follow)
            return false;

        if ($user->getId() != $follow->getFollowerId() || $user->getId() != $follow->getFollowedId())
            return false;

        return true;
    }

    public function beforeDelete(): bool {
        $user = User::fromAccessToken();
        $follow = Follow::get($this->getId());

        if (!$user instanceof User || !$follow instanceof Follow)
            return false;

        if ($user->getId() != $follow->getFollowerId() && $user->getId() != $follow->getFollowedId())
            return false;

        return true;
    }


    public static function routerGroup(Application $app): RouterGroup {
        $follows = new RouterGroup();

        $follows->get(
            '/follows',
            function() {
                return Follow::getList(JsonApi::getParameters());
            }
        );

        $follows->post(
            '/follows',
            function() use ($app) {
                $follow = Follow::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($follow->create())
                    return Follow::get($follow->getId());
                else
                    return null; // throw error
            }
        );

        $follows->get(
            '/follows/{id:[0-9]+}',
            function($id) {
                return Follow::get($id);
            }
        );

        $follows->delete(
            '/follows/{id:[0-9]+}',
            function($id) {
                Follow::get($id)->delete();
            }
        );

        // Celui qui suit qqn
        $follows->get(
            '/follows/{id:[0-9]+}/follower',
            function($id) {
                return Follow::get($id)->getRelated("follower");
            }
        );

        // Celui qui se fait suivre par qqn
        $follows->get(
            '/follows/{id:[0-9]+}/followed',
            function($id) {
                return Follow::get($id)->getRelated("followed");
            }
        );

        return $follows;
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getFollowerId() {
        return $this->followerId;
    }

    public function setFollowerId($followerId) {
        $this->followerId = $followerId;
    }

    public function getFollowedId() {
        return $this->followedId;
    }

    public function setFollowedId($followedId) {
        $this->followedId = $followedId;
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
        return "follows";
    }

    public function JsonApi_id() {
        return $this->getId();
    }

    public function JsonApi_attributes() {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function JsonApi_relationships() {
        return [
            'follower' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
            'followed' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];
    }

    public function JsonApi_filter() {
        return [
            'followerId',
            'followedId',
        ];
    }
}