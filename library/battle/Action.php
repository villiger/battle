<?php

namespace Battle;

use \Battle\Units;

class Action {
	const TYPE_MESSAGE = 1;
	const TYPE_ATTACK = 2;
	const TYPE_MOVE = 3;
	const TYPE_PLACE = 4;

	protected $playerId;
	protected $gameId;
	protected $payload;
	protected $type;


	public static function load($id){
		$action = \R::load('action', $id);

		if(! $action){
			throw new \Exception("Action with id '$id' not found!");
		}

		$payload = json_decode($action->payload, True);

		return create($actionBean->type, $actionBean->player_id, $actionBean->game_id, $payload);
	}

	public static function create($type, $playerId, $gameId, $payload){
		switch( $type ){
			case self::TYPE_MESSAGE:
				return new MessageAction($playerId, $gameId, $payload);
			case self::TYPE_ATTACK:
				return new AttackAction($playerId, $gameId, $payload);
			case self::TYPE_MOVE:
				return new MoveAction($playerId, $gameId, $payload);
			case self::TYPE_PLACE:
				return new PlaceAction($playerId, $gameId, $payload);
			default:
				throw new \Exception("Invalid type '$type' given!");
		}
	}

	/**
	 * @param int $gameId
	 * @param int $lastActionId
	 * @return array of actions since the given action
	 */
	public static function getNewActions($gameId, $lastActionId){
		$game = \R::load('game', $gameId);
		if(! $game ){
			throw new \Exception("Game with id '$gameId' not found!");
		}

		// maybe just use \R::find('action', ...) !?
		$actions = $game->withCondition(' id > ? ', [$lastActionId])->ownActionList;

		$realActions = array();
		foreach( $actions as $action ){
			// We could just call load() but that seems to be wasted energy
			$realActions[] = Action::create($action->type, $action->player_id, $action->game_id, json_decode($action->payload, True));
		}

		return $realActions;
	}

	/**
	 * Not for external use
	 *
	 */
	protected function __construct($playerId, $gameId, $payload){
		$this->playerId = $playerId;
		$this->gameId = $gameId;
		$this->payload = $payload;
	}

	public function store(){
		$action = \R::dispense('action');

		$action->type = $this->type;
		$action->payload = json_encode($this->payload);
		$action->createdAt = \R::isoDateTime();

		// @TODO: Find a way to relate actions without having to load the game/player

		$game = \R::load('game', $this->gameId);

		if(! $game ){
			throw new \Exception("Game with id '$gameId' could not be found!");
		}

		$game->noLoad()->xownActionList[] = $action;

		\R::store( $game );

		$player = \R::load('player', $this->playerId);

		if(! $player ){
			throw new \Exception("Player with id '$playerId' could not be found!");
		}

		$player->noLoad()->xownActionList[] = $action;
		\R::store( $player );

		\R::store( $action );
	}

	/**
	 * @return int type of action
	 */
	public function getType(){
		if(! isset($this->type) ){
			throw new \Exception("Type not found! Object not properly initialized!");
		}
		return $this->type;
	}

}


class MessageAction extends Action {

	private $message;

	public function __construct($playerId, $gameId, $payload){
		parent::__construct($playerId, $gameId, $payload);

		$this->message = $payload["message"];

		$this->type = self::TYPE_MESSAGE;
	}

	public function execute(){
		// Not needed on server side for now. Thus always return True
		return True;
	}

	public function serialize(){
		return array(
			"type" => $this->type, 
			"message" => $this->message, 
			"playerId" => $this->playerId
		);
	}

}

class AttackAction extends Action {

	private $unitId;
	private $targetRow;
	private $targetColumn;

	public function __construct($playerId, $gameId, $payload){
		parent::__construct($playerId, $gameId, $payload);

		$this->unitId = $payload["unitId"];
		$this->targetRow = $payload["targetRow"];
		$this->targetColumn = $payload["targetColumn"];

		$this->type = self::TYPE_ATTACK;
	}

	public function execute(){
		$game = \Battle\Game::load($this->gameId);

		// Get specific unit.. for real!
		$unit = Units\Unit($game->getField(), $unitId);

		// Check if target is an (attackable) unit (and get it?)

		// Get cost for attack (same method as move?!)
		$attackCost = $game->getField()->calcCost($unit->getRow(), $unit->getColumn(), $targetRow, $targetColumn);

		// Check if target is in range 
		if( $unit.getMaxAttackDistance() >= $attackCost ){
			// Do the attack...
			return True;
		}
		return False;


	}

	public function serialize(){
		return array(
			"type" => $this->type, 
			"unitId" => $this->unitId, 
			"targetRow" => $this->targetRow,
			"targetColumn" => $this->targetColumn);
	}

}

class MoveAction extends Action {

	// This is a local id for the unit?!
	private $unitId;
	private $targetRow;
	private $targetColumn;

	public function __construct($playerId, $gameId, $payload){
		parent::__construct($playerId, $gameId, $payload);

		$this->unitId = $payload["unitId"];
		$this->targetRow = $payload["targetRow"];
		$this->targetColumn = $payload["targetColumn"];

		$this->type = self::TYPE_MOVE;
	}

	public function execute(){
		$game = \Battle\Game::load($this->gameId);

		// get specific unit
		// Should the unit be in the game or the field?!
		$unit = Units\Unit($game->getField(), $unitId);

		// Calculate the cost for the move
		$moveCost = $game->getField()->calcCost($unit->getRow(), $unit->getColumn(), $targetRow, $targetColumn);

		if( $unit.getMaxMovement() >= $moveCost ){
			$unit.setPosition($targetRow, $targetColumn);
			return True;
		}
		return False;
	}

	public function serialize(){
		return array(
			"type" => $this->type, 
			"unitId" => $this->unitId, 
			"targetRow" => $this->targetRow,
			"targetColumn" => $this->targetColumn);
	}
}

/**
 * Do we need it for now?
 */
class PlaceAction extends Action {

	private $unitType;
	private $targetRow;
	private $targetColumn;

	public function __construct($playerId, $gameId, $payload){
		parent::__construct($playerId, $gameId, $payload);

		$this->unitType = $payload["unitType"];
		$this->targetRow = $payload["targetRow"];
		$this->targetColumn = $payload["targetColumn"];

		$this->type = self::TYPE_PLACE;
	}

	public function execute(){
		throw new \Exception("Not yet implemented!");
	}

	public function serialize(){
		return array(
			"type" => $this->type, 
			"unitType" => $this->unitType, 
			"targetRow" => $this->targetRow,
			"targetColumn" => $this->targetColumn);
	}

}