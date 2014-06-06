<?php

$app->get('/games', function() use ($app) {
    $user = getCurrentUser();
    $app->render('game/list.php', array('currentUser' => $user, 'games' => $user->getGames()));
});

$app->post('/game', function() use ($app) {
    $user = getCurrentUser();

    $opponentId = $app->request->post('opponent');
    $opponent = \Battle\User::load($opponentId);

    $game = \Battle\Game::create();
    $game->addPlayer($user);
    $game->addPlayer($opponent);

    $app->redirect('/game/' . $game->getId());
});

$app->get('/game/:id', function($id) use ($app) {
    $game = \Battle\Game::load($id);

    # Check if logged in and player of this game in particular

    $app->render('game/game.php', array('game' => $game));
});

$app->post('/game/:id/action', function($gameId) use ($app) {
    try {
        $payload = json_decode($app->request->params("payload"), true);

        // Is there a safer way to get the player? This way it is easily trickable.
        $action = \Battle\Action::create($app->request->params("type"), $app->request->params("playerId"), $gameId, $payload);

        if( $action->execute() ){
            $action->store();
        } else {
            // Action is not executeable eg. invalid move location
            $app->response->setStatus(500);
        }
    } catch(Exception $e) {
        // @TODO: Remove! Only for dev purposes
        echo( $e->getTraceAsString() );
        // most probably wrong format or missing data
        $app->response->setStatus(400);
    }
});

$app->get('/game/:id/action/dummy', function($gameId) use ($app) {
    // This is just a dummy route to create some actions :)

	//$action = \Battle\Action::create(\Battle\Action::TYPE_MESSAGE, 1, $gameId, array("message" => "New World!"));
    $action = \Battle\Action::create(\Battle\Action::TYPE_ATTACK, 2, $gameId, array("unitId" => 777, "targetRow" => 4, "targetColumn" => 1));
    $action->store();

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
    // exit();
});
