<?php

namespace App\OAuth;

use App\Application;
use App\MVC\Router\RouterGroup;
use App\OAuth\Exception\InvalidGrant;
use App\OAuth\Exception\UnsupportedGrantType;
use App\Security\Password;
use User;

class PasswordGrant {

    private $username;
    private $password;
    private $scope;
    private $clientId;
    private $clientSecret;

    public function __construct($username, $password, $scope = 'public', $client_id = '', $client_secret = '') {
        $this->username = $username;
        $this->password = $password;
        $this->scope = $scope;
        $this->clientId = $client_id;
        $this->clientSecret = $client_secret;
    }

    public static function routerGroup(Application $app): RouterGroup {
        $routerGroup = new RouterGroup();

        $routerGroup->post(
            '/oauth/token',
            function() {
                switch($_POST['grant_type']) {
                    case 'password':
                        $passwordGrant = new PasswordGrant($_POST['username'], $_POST['password']);
                        return $passwordGrant->getResponse();

                    default:
                        throw new UnsupportedGrantType;
                }
            }
        );

        return $routerGroup;
    }

    private function getResponse() {
        $users = User::getList([
            'conditions' => 'user_pseudo = :username OR user_email = :username',
            'bind' => [
                'username' => $this->username,
            ],
        ]);

        foreach ($users as $user) {
            if (!$user instanceof User) continue;

            if (Password::equals($this->password, $user->getPassword())) {
                $result['access_token'] = $user->getAccessToken();
                $result['sub'] = $user->getId();
                $result['scope'] = $this->scope;
                $result['token_type'] = 'bearer';

                return $result;
            }
        }

        throw new InvalidGrant;
    }

}