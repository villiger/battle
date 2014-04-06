<?php

require 'vendor/autoload.php';
use RedBean_Facade as R;

if (! file_exists('config.php')) {
    die("Please copy 'config.php.dist' to 'config.php' and set all values properly.");
}

require 'config.php';

$app = new \Slim\Slim();

$app->config(array(
    'debug' => DEBUG,
    'templates.path' => 'templates',
    'view' => '\Slim\LayoutView',
    'layout' => 'layout/base.php'
));

R::setup(DB_PATH, DB_USER, DB_PASS);

require_once 'library/battle/Auth.php';

require_once 'routes/index.php';
require_once 'routes/game.php';

$app->run();

R::close();
