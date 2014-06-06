<?php

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Battle\User;

FacebookSession::setDefaultApplication(AUTH_FACEBOOK_ID, AUTH_FACEBOOK_SECRET);

function getRedirectUrl($provider)
{
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/login/$provider/callback";
}

$app->get('/login/facebook', function() use ($app) {
    $redirectUrl = getRedirectUrl('facebook');

    $helper = new FacebookRedirectLoginHelper($redirectUrl);
    $loginUrl = $helper->getLoginUrl(array(
        'scope' => 'email, user_friends'
    ));

    $app->redirect($loginUrl);
});

$app->get('/login/facebook/callback', function() use ($app) {
    $redirectUrl = getRedirectUrl('facebook');

    $helper = new FacebookRedirectLoginHelper($redirectUrl);

    try {
        /** @var $graphUser GraphUser */
        /** @var $graphPicture GraphObject */
        /** @var $graphFriend GraphUser */

        $session = $helper->getSessionFromRedirect();

        $graphUser = (new FacebookRequest(
            $session, 'GET', '/me'
        ))->execute()->getGraphObject(GraphUser::className());

        $graphFriends = (new FacebookRequest(
            $session, 'GET', '/me/friends'
        ))->execute()->getGraphObjectList(GraphUser::className());

        $graphPicture = (new FacebookRequest(
            $session, 'GET', '/me/picture',
            array (
                'redirect' => false,
                'width' => '200',
                'height' => '200',
                'type' => 'normal',
            )
        ))->execute()->getGraphObject();

        $user = \R::findOne('user', 'facebook_id = ?', [ $graphUser->getId() ]);

        if (! $user) {
            $user = \R::dispense('user');
            $user->facebook_id = $graphUser->getId();
            $user->created = \R::isoDateTime();
        }

        $user->name = $graphUser->getName();
        $user->email = $graphUser->getProperty('email');
        $user->picture = $graphPicture->asArray()->url;
        $user->updated = \R::isoDateTime();

        foreach ($graphFriends as $graphFriend) {
            $friend = \R::findOne('user', 'facebook_id = ?', [ $graphFriend->getId() ]);
            if ($friend) {
                $user->sharedUserList[] = $friend;
                $friend->sharedUserList[] = $user;

                R::store($friend);
            }
        }

        \R::store($user);

        $userId = (int) $user->getID();

        $_SESSION['user'] = new User($userId);
        $_SESSION['user_id'] = $userId;
        $_SESSION['facebook_token'] = $session->getToken();

        $app->redirect('/games');
    } catch (FacebookRequestException $ex) {
        var_dump($ex);
    } catch (Exception $ex) {
        var_dump($ex);
    }
});

$app->get('/logout', function() use ($app) {
    session_unset();

    $app->redirect('/');
});
