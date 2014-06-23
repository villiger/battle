<?php

namespace Battle;

use RedBeanPHP\OODBBean;
use Battle\Units\Unit;

class Game
{
    private $id;
    private $field;
    private $players;
    private $currentPlayer;
    private $messages;

    /**
     * Creates a new game, saves it to the database and the cache.
     *
     * @return Game
     */
    public static function create(User $player, User $opponent)
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

        $game->addPlayer($player);
        $game->addPlayer($opponent);

        $actions = array(
            Action::create(Action::TYPE_PLACE, $player, $game, array('row' => 0, 'column' => 2, 'type' => Unit::TYPE_ARCHER)),
            Action::create(Action::TYPE_PLACE, $player, $game, array('row' => 0, 'column' => 3, 'type' => Unit::TYPE_FIGHTER)),
            Action::create(Action::TYPE_PLACE, $player, $game, array('row' => 0, 'column' => 4, 'type' => Unit::TYPE_FIGHTER)),
            Action::create(Action::TYPE_PLACE, $player, $game, array('row' => 0, 'column' => 5, 'type' => Unit::TYPE_ARCHER)),
            Action::create(Action::TYPE_PLACE, $opponent, $game, array('row' => 7, 'column' => 2, 'type' => Unit::TYPE_MINOTAUR)),
            Action::create(Action::TYPE_PLACE, $opponent, $game, array('row' => 7, 'column' => 3, 'type' => Unit::TYPE_JUGGERNAUT)),
            Action::create(Action::TYPE_PLACE, $opponent, $game, array('row' => 7, 'column' => 4, 'type' => Unit::TYPE_JUGGERNAUT)),
            Action::create(Action::TYPE_PLACE, $opponent, $game, array('row' => 7, 'column' => 5, 'type' => Unit::TYPE_MINOTAUR)),
        );

        array_walk($actions, function (Action $action) {
            if ($action->execute()) {
                $action->store();
            }
        });

        $game->saveToCache();

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
            $game->saveToCache();
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

        $this->messages = array();

        $this->field = new Field($this, (int) $bean->field_width, (int) $bean->field_height);

        $this->players = array_map(function (OODBBean $userBean) {
            $user = new User($userBean->getID());

            if ($this->currentPlayer == null) {
                $this->setCurrentPlayer($user);
            }

            return $user;
        }, $bean->sharedUserList);

        $actions = $this->getActions();
        array_walk($actions, function (Action $action) {
            $action->execute();
        });
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

            $this->players[$user->getId()] = $user;

            if ($this->currentPlayer == null) {
                $this->setCurrentPlayer($user);
            }

            $this->saveToCache();

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
        return (int) $this->id;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return User
     */
    public function getCurrentPlayer() {
        return $this->currentPlayer;
    }

    /**
     * @param User $player
     */
    public function setCurrentPlayer(User $player) {
        $this->currentPlayer = $player;
    }

    /**
     * Check if a particular user is a player of this game.
     *
     * @param User $user
     * @return bool
     */
    public function isPlayer(User $user)
    {
        return $this->players && array_key_exists($user->getId(), $this->players);
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
        if ($this->isPlayer($user)) {
            foreach ($this->players as $player) {
                /** @var $player User */
                if ($player->getId() != $user->getId()) {
                    return $player;
                }
            }
        }

        return null;
    }

    /**
     * Returns JSON representation of the game's current state.
     *
     * @return string
     */
    public function toJson()
    {
        $state = array(
            'id' => $this->getId(),
            'field' => array(
                'width' => $this->getField()->getWidth(),
                'height' => $this->getField()->getHeight(),
                'tiles' => $this->getField()->getTiles(),
                'units' => array_map(function (Unit $unit) {
                    return array(
                        'id' => $unit->getId(),
                        'user_id' => $unit->getUser()->getId(),
                        'row' => $unit->getRow(),
                        'column' => $unit->getColumn(),
                        'type' => $unit->getType(),
                        'max_life' => $unit->getMaxLife(),
                        'life' => $unit->getLife(),
                        'move_range' => $unit->getMoveRange(),
                        'attack_range' => $unit->getAttackRange(),
                        'has_moved' => $unit->hasMoved(),
                        'has_attacked' => $unit->hasAttacked()
                    );
                }, $this->getField()->getUnits())
            ),
            'players' => array_map(function (User $player) {
                return array(
                    'id' => $player->getId(),
                    'name' => $player->getName(),
                    'picture' => $player->getPicture()
                );
            }, $this->players),
            'current_player_id' => $this->currentPlayer->getId(),
            'messages' => $this->messages,
            'last_action_id' => $this->getLastActionId()
        );

        return json_encode($state);
    }

    /**
     * Saves the current game state into the cache.
     */
    public function saveToCache()
    {
        apc_store("game_{$this->getId()}", $this);
    }

    /**
     * @param User $player
     * @param string $message
     */
    public function addMessage(User $player, $message)
    {
        $this->messages[] = array(
            'user_id' => $player->getId(),
            'message' => $message
        );
    }

    /**
     * @return Action or NULL if non exists
     */
    public function getLastAction()
    {
        // TODO: this could be made faster, without database query.
        $gameBean = Game::getBean($this->getId());
        $lastActions = array_keys($gameBean->xownActionList);
        if ($lastActions) {
            return Action::load(max($lastActions));
        } else {
            return NULL;
        }
    }

    /**
     * @return int
     */
    public function getLastActionId()
    {
        $lastAction = $this->getLastAction();
        if ($lastAction) {
            return $lastAction->getId();
        } else {
            return 0;
        }
    }

    /**
     * Returns an array of all actions in this game.
     *
     * @return array
     */
    public function getActions()
    {
        $gameBean = Game::getBean($this->getId());
        $actionBeans = $gameBean->with('ORDER BY action.id ASC')->xownActionList;

        $actions = array();
        foreach ($actionBeans as $actionBean){
            $user = User::load($actionBean->user_id);
            $payload = json_decode($actionBean->payload, true);
            $actions[] = Action::create($actionBean->type, $user, $this, $payload);
        }

        return $actions;
    }

    /**
     * Returns an array of actions since the given $lastActionId.
     *
     * @param int $lastActionId
     * @return array
     * @throws \Exception
     */
    public function getNewActions($lastActionId)
    {
        $gameBean = Game::getBean($this->getId());
        $actionBeans = $gameBean->withCondition('action.id > ? ORDER BY action.id ASC', [ $lastActionId ])->xownActionList;

        $actions = array();
        foreach ($actionBeans as $actionBean){
            $user = User::load($actionBean->user_id);
            $payload = json_decode($actionBean->payload, true);
            $actions[] = Action::create($actionBean->type, $user, $this, $payload);
        }

        return $actions;
    }
}
