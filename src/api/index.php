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
$router->mount(Season::routerGroup($app));
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
