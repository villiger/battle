<?php

namespace Battle;

use RedBeanPHP\OODBBean;

class Game
{
    private $id;
    private $field;
    private $players;
    private $currentPlayer;

    /**
     * Creates a new game, saves it to the database and the cache.
     *
     * @return Game
     */
    public static function create()
    {
        $seed = mt_rand();

        $bean = \R::dispense("game");
        $bean->seed = $seed;
        $bean->field_width = 8;
        $bean->field_height = 8;
        $bean->is_done = false;
        $bean->updated = \R::isoDateTime();
        $bean->created = \R::isoDateTime();
        \R::store($bean);

        $id = (int) $bean->getID();
        $game = new Game($id);

        apc_store("game_$id", $game);

        return $game;
    }

    /**
     * Loads game from the cache or the database.
     * If not in cache, saves it to the cache.
     *
     * @param int $id
     * @return Game
     * @throws \Exception
     */
    public static function load($id)
    {
        $game = apc_fetch("game_$id");

        if ($game === false) {
            $game = new Game($id);

            apc_store("game_$id", $game);
        }

        return $game;
    }

    /**
     * @param int $id
     * @return \RedBeanPHP\OODBBean
     * @throws \Exception
     */
    public static function getBean($id)
    {
        $bean = \R::load('game', $id);
        if (! $bean) {
            throw new \Exception("Game with id '$id' not found.'");
        }

        return $bean;
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function __construct($id)
    {
        $this->id = $id;

        $bean = Game::getBean($id);

        // Set random seed to generate the field accordingly
        mt_srand((int) $bean->seed);

        $this->currentPlayer = 1;

        $this->field = new Field($this, (int) $bean->field_width, (int) $bean->field_height);

        $this->players = array_map(function ($userBean) {
            /** @var $userBean OODBBean */
            return new User($userBean->getID());
        }, array_values($bean->sharedUserList));
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function addPlayer(User $user)
    {
        if (count($this->players) < 2) {
            foreach ($this->players as $player) {
                /** @var $player User */
                if ($player->getId() === $user->getId()) {
                    throw new \Exception("User with id '{$user->getId()}' is already a player of this game.");
                }
            }

            $this->players[] = $user;

            apc_store("game_{$this->getId()}", $this);

            $gameBean = Game::getBean($this->getId());
            $userBean = User::getBean($user->getId());

            $gameBean->sharedUserList[] = $userBean;
            \R::store($gameBean);
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Returns the opponent of the given user.
     * Returns null if no opponent or user not in this game.
     *
     * @param User $user
     * @return User
     */
    public function getOpponent(User $user)
    {
        $userFound = false;
        $opponent = null;

        foreach ($this->players as $player) {
            /** @var $player User */
            if ($player->getId() == $user->getId()) {
                $userFound = true;
            } else {
                $opponent = $player;
            }
        }

        return $userFound ? $opponent : null;
    }

    /**
     * Returns JSON representation of the game's current state.
     *
     * @return string
     */
    public function getStateJson()
    {
        $state = array(
            'field' => array(
                'width' => $this->getField()->getWidth(),
                'height' => $this->getField()->getHeight(),
                'tiles' => $this->getField()->getTiles()
            ),
            'players' => array_map(function ($player) {
                /** @var $player User */
                return array(
                    'id' => $player->getId(),
                    'name' => $player->getName(),
                    'picture' => $player->getPicture()
                );
            }, $this->players),
            'current_player' => $this->currentPlayer
        );

        return json_encode($state);
    }
}
