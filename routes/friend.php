<?php

$app->get('/friends', function() use ($app) {
    $user = getCurrentUser();
    $app->render('friend/list.php', array('friends' => $user->getFriends()));
});
