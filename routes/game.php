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

$app->get('/game/:id/action/dummy', function($gameId) use ($app) {
    // This is just a dummy route to create some actions :)

	\Battle\Action::save(\Battle\Action::TYPE_MESSAGE, 1, $gameId, array("message" => "Hello World!"));

});

$app->get('/game/:id/action/:last', function($gameId, $lastActionId) use ($app) {
    $actions = \Battle\Action::getNewActions($gameId, $lastActionId);

    # Check if logged in and player of this game in particular

    $encActions = array();
    foreach( $actions as $action ){
    	$encActions[] = $action->serialize();
    	//var_dump($action->serialize());
    }

    $response = $app->response();
    $response['Content-Type'] = "application/json";
    $response->body(json_encode($encActions));

    // Needs an exit?! http://stackoverflow.com/questions/14150595/slim-php-returning-json
    exit();
});
