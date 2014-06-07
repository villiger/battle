<?php

namespace Battle;

use RedBeanPHP\OODBBean;

class User {
    private $id;
    private $email;
    private $name;
    private $picture;

    public static function getBean($id)
    {
        $bean = \R::load('user', $id);
        if (! $bean) {
            throw new \Exception("User with id '$id' not found.'");
        }

        return $bean;
    }

    /**
     * Loads user from the cache or the database.
     * If not in cache, saves him to the cache.
     *
     * @param int $id
     * @return User
     * @throws \Exception
     */
    public static function load($id)
    {
        $user = apc_fetch("user_$id");

        if ($user === false) {
            $user = new User($id);

            apc_store("user_$id", $user);
        }

        return $user;
    }

    public function __construct($id)
    {
        $this->id = $id;

        $bean = User::getBean($id);

        $this->email = $bean->email;
        $this->name = $bean->name;
        $this->picture = $bean->picture;
    }

    public function getId()
    {
        return (int) $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function getGames()
    {
        $userBean = User::getBean($this->getId());

        $games = array_map(function (OODBBean $gameBean) {
            return Game::load($gameBean->getID());
        }, $userBean->sharedGameList);

        usort($games, function(Game $a, Game $b) {
            if ($a->getLastActionId() == $b->getLastActionId()) {
                return 0;
            } elseif ($a->getLastActionId() < $b->getLastActionId()) {
                return 1;
            } else {
                return -1;
            }
        });

        return $games;
    }

    public function getFriends()
    {
        $userBean = User::getBean($this->getId())->with('ORDER BY name ASC');

        return array_map(function (OODBBean $friendBean) {
            return User::load($friendBean->getID());
        }, $userBean->sharedUserList);
    }
}
