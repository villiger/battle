<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim();

$app->config(array(
    'debug' => true,
    'templates.path' => 'templates',
    'view' => '\Slim\LayoutView',
    'layout' => 'layout/base.php'
));

require 'routes/index.php';
require 'routes/game.php';

$app->run();
