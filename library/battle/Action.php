<?php

namespace Battle;

class Action {
	const TYPE_MESSAGE = 0;
	const TYPE_ATTACK = 1;
	const TYPE_MOVE = 2;
	const TYPE_PLACE = 3;

	private $player;
	private $game;
	private $payload;


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

}


class MessageAction extends Action {

	private $message;
	private $playerId;
	private $gameId;

	public function __construct($playerId, $gameId, $payload){
		$this->$message = $payload["message"];
		$this->$playerId = $playerId;
		$this->$gameId = $gameId;
	}

	public function execute(){

		return False;
	}

	public function serialize(){
		return array(
			"type" => Action::TYPE_MESSAGE, 
			"message" => $this->$message, 
			"playerId" => $this->$playerId);

	}
}

class AttackAction extends Action {
	
}

class PlaceAction extends Action {
	
}

class MoveAction extends Action {
	
}