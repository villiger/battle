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

$app->get('/game/:id', function($gameId) use ($app) {
    # Check if logged in

    $game = new \Battle\Game($gameId);
    
    $app->render('game.php', array('game' => $game));
});

$app->post('/game/:id', function($gameId) use ($app) {
    # Check if logged in


    # Get POST values and save them to DB
    $game = new \Battle\Game($gameId);
    
    $game->loadPost($_POST);

    # Redirect to game?
    $app->redirect('/game/' . $gameId);
});

$app->get('/game/', function() use ($app) {
    # Creates a new game

    # Check if logged in

    $game = new \Battle\Game();

    # Create account for user if game already existing?

    # Check if friend exists?
    
    $app->render('game.php', array('game' => $game));
});