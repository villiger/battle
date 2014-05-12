<?php

// TODO: change this back to POST
$app->get('/game', function() use ($app) {
    # Creates a new game

    # Check if logged in

    $game = \Battle\Game::create();

    # Create account for user if game already existing?

    # Check if friend exists?

    $app->redirect('/game/' . $game->getId());
});

$app->get('/game/:id', function($id) use ($app) {
    $game = \Battle\Game::load($id);

    # Check if logged in and player of this game in particular

    $app->render('game.php', array('game' => $game));
});
