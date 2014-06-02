<?php

namespace Battle;

class Action {
	const TYPE_MESSAGE = 0;
	const TYPE_ATTACK = 1;
	const TYPE_MOVE = 2;
	const TYPE_PLACE = 3;

	private $playerId;
	private $gameId;
	private $payload;
	private $type;


	public static function load($id){
		$action = \R::load('action', $id);
		if(! $action){
			throw new \Exception("Action with id '$id' not found!");
		}

		return create($action);
	}

	public static function create($actionBean){
		$playerId = $actionBean->player_id;
		$gameId = $actionBean->game_id;
		$payload = json_decode($actionBean->payload, True);
		switch( $actionBean->type ){
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

	public static function save($type, $playerId, $gameId, $payload){
		$action = \R::dispense('action');

		$action->type = $type;
		$action->payload = json_encode($payload);
		$action->createdAt = \R::isoDateTime();

		// @TODO: Find a way to relate actions without having to load the game/player

		$game = \R::load('game', $gameId);

		if(! $game ){
			throw new \Exception("Game with id '$gameId' could not be found!");
		}

		$game->noLoad()->xownActionList[] = $action;

		\R::store( $game );

		$player = \R::load('player', $playerId);

		if(! $player ){
			throw new \Exception("Player with id '$playerId' could not be found!");
		}

		$player->noLoad()->xownActionList[] = $action;
		\R::store( $player );

		\R::store( $action );
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
			$realActions[] = Action::create($action);
		}

		return $realActions;
	}

	/**
	 * @return int type of action
	 */
	public function getType(){
		if(! isset($this->$type) ){
			throw new \Exception("Type not found! Object not properly initialized!");
		}
		return $this->$type;
	}

}


class MessageAction extends Action {

	private $message;

	public function __construct($playerId, $gameId, $payload){
		$this->$playerId = $playerId;
		$this->$gameId = $gameId;
		$this->$message = $payload["message"];

		$this->$type = self::TYPE_MESSAGE;
	}

	public function execute(){
		// Not needed on server side for now. Thus always return True
		return True;
	}

	public function serialize(){
		return array(
			"type" => $this->getType(), 
			"message" => $this->$message, 
			"playerId" => $this->$playerId
		);
	}

}

class AttackAction extends Action {

	private $unitId;
	private $targetRow;
	private $targetColumn;

	public function __construct($playerId, $gameId, $payload){
		
		$this->$playerId = $playerId;
		$this->$gameId = $gameId;
		$this->$unitId = $payload["unitId"];
		$this->$targetRow = $payload["targetRow"];
		$this->$targetColumn = $payload["targetColumn"];

		$this->$type = self::TYPE_ATTACK;
	}

	public function execute(){
		$game = \Battle\Game::load($gameId);

		// Get specific unit.. for real!
		$unit = $game->getUnit($unitId);

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
			"type" => $this->getType(), 
			"unitId" => $this->$unitId, 
			"targetRow" => $this->$targetRow,
			"targetColumn" => $this->$targetColumn);
	}

}

class MoveAction extends Action {

	// This is a local id for the unit?!
	private $unitId;
	private $targetRow;
	private $targetColumn;

	public function __construct($playerId, $gameId, $payload){
		
		$this->$playerId = $playerId;
		$this->$gameId = $gameId;
		$this->$unitId = $payload["unitId"];
		$this->$targetRow = $payload["targetRow"];
		$this->$targetColumn = $payload["targetColumn"];

		$this->$type = self::TYPE_MOVE;
	}

	public function execute(){
		$game = \Battle\Game::load($gameId);

		// get specific unit
		// Should the unit be in the game or the field?!
		$unit = $game.getUnit($unitId);

		// Game get field

		// calc costs for move \w field
		$moveCost = $game->getField()->calcCost($unit->getRow(), $unit->getColumn(), $targetRow, $targetColumn);

		if( $unit.getMaxMovement() >= $moveCost ){
			$unit.setPosition($targetRow, $targetColumn);
			return True;
		}
		return False;
	}

	public function serialize(){
		return array(
			"type" => $this->getType(), 
			"unitId" => $this->$unitId, 
			"targetRow" => $this->$targetRow,
			"targetColumn" => $this->$targetColumn);
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
		
		$this->$playerId = $playerId;
		$this->$gameId = $gameId;

		$this->$unitType = $payload["unitType"];
		$this->$targetRow = $payload["targetRow"];
		$this->$targetColumn = $payload["targetColumn"];

		$this->$type = self::TYPE_PLACE;
	}

	public function execute(){
		throw new \Exception("Not yet implemented!");
	}

	public function serialize(){
		return array(
			"type" => $this->getType(), 
			"unitType" => $this->$unitType, 
			"targetRow" => $this->$targetRow,
			"targetColumn" => $this->$targetColumn);
	}

}