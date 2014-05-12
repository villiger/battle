<?php

$app->get('/', function() use ($app) {
    $app->render('index.php', array('title' => 'Welcome to Battle Chess!'));
});

$app->get('/hello/:name', function($name) use ($app) {
    $greeting = "Welcome, $name!";
    $app->render('index.php', array('title' => $greeting));
});

$app->get('/login/callback', function() use ($app) {
    Hybrid_Endpoint::process();
});

$app->get('/login/:provider', function($provider) use ($app) {
    $auth = new \Battle\Auth($provider);
    $user = $auth->authenticate();

    echo "Welcome, {$user->name}!";
});
