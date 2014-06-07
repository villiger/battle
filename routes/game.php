<?php

use Battle\Action;
use Battle\Game;

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

    $actions = array(
        Action::create(Action::TYPE_PLACE, $user, $game, array('row' => 0, 'column' => 3)),
        Action::create(Action::TYPE_PLACE, $user, $game, array('row' => 0, 'column' => 4)),
        Action::create(Action::TYPE_PLACE, $opponent, $game, array('row' => 7, 'column' => 3)),
        Action::create(Action::TYPE_PLACE, $opponent, $game, array('row' => 7, 'column' => 4)),
    );

    array_walk($actions, function (Action $action) {
        if ($action->execute()) {
            $action->store();
        }
    });

    $game->saveToCache();

    $app->redirect('/game/' . $game->getId());
});

$app->get('/game/:id', function($id) use ($app) {
    $game = \Battle\Game::load($id);
    $user = getCurrentUser();

    if ($game->isPlayer($user)) {
        $app->render('game/game.php', array('game' => $game, 'user' => $user));
    } else {
        throw new Exception("You are not a player of this game.");
    }
});

$app->post('/game/:id/action/:actionType', function($id, $actionType) use ($app) {
    $user = getCurrentUser();
    $game = Game::load($id);
    $payload = $app->request->post("payload");

    $action = Action::create($actionType, $user, $game, $payload);

    if ($action->execute()){
        $action->store();
        $game->saveToCache();
    } else {
        // Action is not executeable eg. invalid move location
        $app->response->setStatus(500);
    }
});

$app->get('/game/:id/action/:lastActionId', function($id, $lastActionId) use ($app) {
    $game = Game::load($id);

    $actions = array_map(function (Action $action) {
        return $action->toArray();
    }, $game->getNewActions($lastActionId));

    header("Content-Type: application/json");
    echo json_encode(array(
        'last_action_id' => $game->getLastActionId(),
        'actions' => $actions
    ));
    exit;
});
