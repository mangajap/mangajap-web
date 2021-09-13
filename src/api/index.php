<?php

use App\Application;
use App\Database\MySql;
use App\Header;
use App\HTTP;
use App\JsonApi;
use App\MVC\Model;
use App\MVC\Query\Result;
use App\MVC\Router;
use App\Utils\JSON;
use App\OAuth;

header("Content-Type: application/vnd.api+json");
header("Access-Control-Allow-Origin: *");

spl_autoload_register(
  function ($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
      require $file;
      return true;
    }
    return false;
  }
);

$app = new Application();

$router = new Router();

foreach (glob("Model/*.php") as $filename) {
  require_once $filename;
}

$router->get(
  '/test',
  function () use ($app) {
  }
);

$router->patch(
  '/test',
  function () use ($app) {
  }
);

$router->post(
  '/test',
  function () use ($app) {
  }
);

$router->post(
  '/forgot-password',
  function () use ($app) {
    $user = User::get([
      'conditions' => 'user_pseudo = :pseudo AND user_email = :email',
      'bind' => [
        'pseudo' => $_POST['pseudo'],
        'email' => $_POST['email'],
      ],
    ]);

    if ($user instanceof User) {
      $result['access_token'] = $user->getUid();
      $result['sub'] = $user->getId();

      return $result;
    }

    return null;

    //        $user = User::get([
    //            'conditions' => 'user_email = :email',
    //            'bind' => [
    //                'email' => $_POST['email'],
    //            ],
    //        ]);
    //
    //        if ($user instanceof User) {
    //            $to  = $_POST['email']; // notez la virgule
    //            $subject = 'Réinitialiser votre mot de passe MangaJap';
    //
    //            $message = '
    //<html>
    //	<head>
    //		<title>Réinitialiser votre mot de passe MangaJap</title>
    //	</head>
    //	<body>
    //		<h1>Mot de passe oublié</h1>
    //		<p>MangaJap a reçu une demande pour réinitialiser le mot de passe de votre compte StanTanasi.</p>
    //		<p>Pour réinitialiser votre mot de passe, cliquez sur le bouton ci-dessous </p>
    //		<a href="'."http://mangajap.000webhostapp.com/reset-password.php?token=".$user->getResetPasswordToken().'">Réinitialiser mon mot de passe</a>
    //	</body>
    //</html>
    //     ';
    //
    //            $headers[] = 'MIME-Version: 1.0';
    //            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
    //            $headers[] = 'From: MangaJap <support@mangajap.com>';
    //
    //            mail($to, $subject, $message, implode("\r\n", $headers));
    //
    //            return null;
    //        }
    //
    //        throw new JsonApi\Document\Errors(
    //            new JsonApi\JsonApiException(
    //                null,
    //                null,
    //                null,
    //                HTTP::CODE_BAD_REQUEST,
    //                "Invalid email address",
    //                "Email address not found",
    //                null,
    //                null,
    //                null
    //            )
    //        );
  }
);

$router->patch(
  '/reset-password',
  function () use ($app) {
    Header::setAuthorization('Bearer ' . $_GET['token']);

    $user = User::fromAccessToken();

    if ($user instanceof User) {
      $user->setPassword($_POST['password']);

      if ($user->update())
        return User::get($user->getId());
      else
        throw new JsonApi\Document\Errors(
          new JsonApi\JsonApiException(
            null,
            null,
            null,
            HTTP::CODE_BAD_REQUEST,
            "Could not update user",
            "User could not be save",
            null,
            null,
            null
          )
        );
    }

    throw new JsonApi\Document\Errors(
      new JsonApi\JsonApiException(
        null,
        null,
        null,
        HTTP::CODE_BAD_REQUEST,
        "Invalid token",
        "Token invalid",
        null,
        null,
        null
      )
    );
  }
);

$router->mount(OAuth\PasswordGrant::routerGroup($app));

$router->mount(Anime::routerGroup($app));
$router->mount(AnimeEntry::routerGroup($app));
$router->mount(Episode::routerGroup($app));
$router->mount(Follow::routerGroup($app));
$router->mount(Franchise::routerGroup($app));
$router->mount(Genre::routerGroup($app));
$router->mount(Manga::routerGroup($app));
$router->mount(MangaEntry::routerGroup($app));
$router->mount(People::routerGroup($app));
$router->mount(Request::routerGroup($app));
$router->mount(Review::routerGroup($app));
$router->mount(Staff::routerGroup($app));
$router->mount(Theme::routerGroup($app));
$router->mount(User::routerGroup($app));
$router->mount(Volume::routerGroup($app));

require_once 'routes/cronjobs.php';

require_once('db.config.php');
$app->setDatabase(
  'db_mangajap',
  new MySql([
    'host' => $dbConfig['host'],
    'dbname' => $dbConfig['dbname'],
    'username' => $dbConfig['username'],
    'password' => $dbConfig['password'],
  ])
);

$app->setRouter($router);

$app->setDefault($app);

$result = $app->run();

// ob_start('ob_gzhandler');

if ($result !== null) {
  if ($result instanceof Model || $result instanceof Result)
    echo JsonApi::encode($result);
  else
    echo JSON::encode($result);
}
