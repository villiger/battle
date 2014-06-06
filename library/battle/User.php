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
        return $this->id;
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

        return array_map(function ($gameBean) {
            /** @var $gameBean OODBBean */
            return Game::load($gameBean->getID());
        }, $userBean->sharedGameList);
    }

    public function getFriends()
    {
        $userBean = User::getBean($this->getId());

        return array_map(function ($friendBean) {
            /** @var $friendBean OODBBean */
            return User::load($friendBean->getID());
        }, $userBean->sharedUserList);
    }
}
