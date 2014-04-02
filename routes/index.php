<?php

use RedBean_Facade as R;

$app->get('/', function() use ($app) {
    $app->render('index.php', array('title' => 'Welcome to Battle Chess!'));
});

$app->get('/hello/:name', function($name) use ($app) {
    $greeting = "Welcome, $name!";
    $app->render('index.php', array('title' => $greeting));
});
