<?php

use App\Application;
use App\Database\Column;
use App\JsonApi;
use App\JsonApi\Document\PrimaryData\Resource\Relationship;
use App\JsonApi\JsonApiSerializable;
use App\MVC\Model;
use App\MVC\Router\RouterGroup;

class Request extends Model implements JsonApiSerializable {

    public $id;
    public $userId;
    public $createdAt;
    public $updatedAt;
    public $requestType;
    public $data;
    public $isDone;
    public $userHasRead;


    public function initialize() {
        $this->setConnectionService('db_mangajap');

        $this->setSource('request');

        $this->setColumnMap([
            'id' => 'request_id',
            'userId' => 'request_userid',
            'requestType' => 'request_type',
            'data' => 'request_data',
            'isDone' => 'request_isdone',
            'userHasRead' => 'request_userhasread',
            'createdAt' => 'request_createdat',
            'updatedAt' => 'request_updatedat',
        ]);

        $this->setPrimaryKey('id');

        $this->setAttributes([
            'userId',
            'requestType',
            'data',
            'isDone',
            'userHasRead',
            'createdAt',
            'updatedAt',
        ]);

        $this->setDataTypes([
            'id' => Column::TYPE_INT,
            'userId' => Column::TYPE_INT,
            'requestType' => Column::TYPE_TINYTEXT,
            'data' => Column::TYPE_TEXT,
            'isDone' => Column::TYPE_BOOLEAN,
            'userHasRead' => Column::TYPE_BOOLEAN,
            'createdAt' => Column::TYPE_DATETIME,
            'updatedAt' => Column::TYPE_DATETIME,
        ]);

        $this->skipAttributes([
            'createdAt',
            'updatedAt',
        ]);

        $this->skipAttributesOnUpdate([
            'userId',
        ]);


        $this->belongsTo(
            'userId',
            User::class,
            'id',
            [
                'alias' => 'user'
            ]
        );
    }

    public function beforeCreate(): bool {
        if (\App\OAuth::getBearerToken() == null)
            return false;

        return true;
    }

    public function beforeUpdate(): bool {
        $user = User::fromAccessToken();
        $request = Request::get($this->getId());

        if (!$user instanceof User || !$request instanceof Request)
            return false;

        if ($user->getId() != $request->getUserId())
            return false;

        return true;
    }


    public static function routerGroup(Application $app): RouterGroup {
        $requests = new RouterGroup();

        $requests->get(
            '/requests',
            function() {
                return Request::getList(JsonApi::getParameters());
            }
        );

        $requests->post(
            '/requests',
            function() use ($app) {
                $request = Request::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($request->create())
                    return Request::get($request->getId());
                else
                    return null;
            }
        );

        $requests->get(
            '/requests/{id:[0-9]+}',
            function($id) {
                return Request::get($id);
            }
        );

        $requests->patch(
            '/requests/{id:[0-9]+}',
            function($id) use ($app) {
                $request = Request::fromJsonApi($app->getRequest()->getJSONObject()->optJSONObject('data'));
                if ($request->update())
                    return Request::get($request->getId());
                else
                    return null;
            }
        );

        $requests->get(
            '/requests/{id:[0-9]+}/user',
            function($id) {
                return Request::get($id)->getRelated("user");
            }
        );

        return $requests;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * @param mixed $requestType
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getIsDone()
    {
        return $this->isDone;
    }

    /**
     * @param mixed $isDone
     */
    public function setIsDone($isDone)
    {
        $this->isDone = $isDone;
    }

    /**
     * @return mixed
     */
    public function getUserHasRead()
    {
        return $this->userHasRead;
    }

    /**
     * @param mixed $userHasRead
     */
    public function setUserHasRead($userHasRead)
    {
        $this->userHasRead = $userHasRead;
    }



    public function JsonApi_type()
    {
        return "request";
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
            'requestType' => $this->requestType,
            'data' => $this->data,
            'isDone' => $this->isDone,
            'userHasRead' => $this->userHasRead,
        ];
    }

    public function JsonApi_relationships()
    {
        return [
            'user' => [
                Relationship::LINKS_SELF => false,
                Relationship::LINKS_RELATED => true,
            ],
        ];
    }

    public function JsonApi_filter()
    {
        return [
            'isDone',
            'userHasRead',
        ];
    }
}