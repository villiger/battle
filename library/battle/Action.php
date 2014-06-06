<?php

namespace Battle;

use \Battle\Units;

class Action
{
	const TYPE_MESSAGE = 'message';
	const TYPE_PLACE = 'place';
    const TYPE_MOVE = 'move';
    const TYPE_ATTACK = 'attack';

    protected $type;
    protected $user;
    protected $game;
	protected $payload;

    /**
     * @param int $id
     * @return AttackAction|MessageAction|MoveAction|PlaceAction
     */
    public static function load($id)
    {
		$bean = Action::getBean($id);

        $user = User::load($bean->user_id);
        $game = Game::load($bean->game_id);
		$payload = json_decode($bean->payload, true);

		return Action::create($bean->type, $user, $game, $payload);
	}

    /**
     * @param string $type
     * @param User $user
     * @param Game $game
     * @param array $payload
     * @return AttackAction|MessageAction|MoveAction|PlaceAction
     * @throws \Exception
     */
    public static function create($type, User $user, Game $game, array $payload)
    {
		switch ($type) {
			case self::TYPE_MESSAGE:
				return new MessageAction($user, $game, $payload);
            case self::TYPE_PLACE:
                return new PlaceAction($user, $game, $payload);
            case self::TYPE_MOVE:
                return new MoveAction($user, $game, $payload);
			case self::TYPE_ATTACK:
				return new AttackAction($user, $game, $payload);
			default:
				throw new \Exception("Invalid type '$type' given!");
		}
	}

    /**
     * Creates an action from an encoded JSON string.
     *
     * @param string $json
     * @return AttackAction|MessageAction|MoveAction|PlaceAction
     */
    public static function fromJson($json)
    {
        $decoded = json_decode($json, true);

        return Action::create(
            $decoded['type'],
            $decoded['user_id'],
            $decoded['game_id'],
            $decoded['payload']
        );
    }

    /**
     * Returns a JSON representation of this action.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode(array(
            "type" => $this->type,
            "user_id" => $this->user->getId(),
            "game_id" => $this->game->getId(),
            "payload" => $this->payload
        ));
    }

    /**
     * @param int $id
     * @return \RedBeanPHP\OODBBean
     * @throws \Exception
     */
    public static function getBean($id)
    {
        $bean = \R::load('action', $id);

        if (! $bean){
            throw new \Exception("Action with id '$id' not found!");
        }

        return $bean;
    }

    /**
     * @param string $type
     * @param User $user
     * @param Game $game
     * @param string $payload
     */
    protected function __construct($type, User $user, Game $game, $payload)
    {
        $this->type = $type;
		$this->user = $user;
		$this->game = $game;
		$this->payload = $payload;
	}

    /**
     * Creates a new action in the database. Just call this once!
     */
    public function store()
    {
		$actionBean = \R::dispense('action');
        $actionBean->type = $this->type;
        $actionBean->payload = json_encode($this->payload);
        $actionBean->createdAt = \R::isoDateTime();

		$gameBean = Game::getBean($this->game->getId());
		$gameBean->noLoad()->xownActionList[$actionBean->getID()] = $actionBean;
		\R::store($gameBean);

		$userBean = User::getBean($this->user->getId());
		$userBean->noLoad()->xownActionList[$actionBean->getID()] = $actionBean;
		\R::store($userBean);

        \R::store($actionBean);
	}

    /**
     * This action has to be overriden by child class.
     *
     * @return bool
     */
    public function execute()
    {
        return false;
    }
}

class MessageAction extends Action
{
	private $message;

	public function __construct(User $user, Game $game, array $payload)
    {
		parent::__construct(self::TYPE_MESSAGE, $user, $game, $payload);

		$this->message = $payload["message"];
	}

	public function execute()
    {
		$this->game->addMessage($this->user, $this->message);

		return true;
	}
}

class PlaceAction extends Action
{
    private $row;
    private $column;

    public function __construct(User $user, Game $game, array $payload)
    {
        parent::__construct(self::TYPE_PLACE, $user, $game, $payload);

        $this->row = $payload["row"];
        $this->column = $payload["column"];
    }

    public function execute()
    {
        return $this->game->getField()->createUnit($this->user, $this->row, $this->column);
    }
}

class MoveAction extends Action
{
    private $unit;
    private $row;
    private $column;

    public function __construct(User $user, Game $game, array $payload)
    {
        parent::__construct(self::TYPE_MOVE, $user, $game, $payload);

        $unitId = (int) $payload["unit_id"];

        $this->unit = $this->game->getField()->getUnit($unitId);
        $this->row = (int) $payload["row"];
        $this->column = (int) $payload["column"];
    }

    public function execute()
    {
        return $this->unit->moveTo($this->row, $this->column);
    }
}

class AttackAction extends Action
{
	private $unit;
	private $row;
	private $column;

	public function __construct(User $user, Game $game, array $payload)
    {
		parent::__construct(self::TYPE_ATTACK, $user, $game, $payload);

        $unitId = (int) $payload["unit_id"];

        $this->unit = $this->game->getField()->getUnit($unitId);
		$this->row = (int) $payload["row"];
		$this->column = (int) $payload["column"];
	}

	public function execute()
    {
        return $this->unit->attackOn($this->row, $this->column);
	}
}
