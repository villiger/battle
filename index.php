<?php

session_start();

require 'vendor/autoload.php';
require 'rb.phar';

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

\R::setup(DB_PATH, DB_USER, DB_PASS);

function redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    exit();
}

if (! isset($_SESSION['user'])) {
    $path = $app->request()->getPath();

    if (! preg_match('@^/(login|)@', $path)) {
        redirect('/');
    }
}

require_once 'library/battle/Auth.php';
require_once 'library/battle/Game.php';
require_once 'library/battle/Field.php';
//require_once 'library/battle/Action.php';

require_once 'routes/auth.php';
require_once 'routes/index.php';
require_once 'routes/game.php';

$app->run();

\R::close();
