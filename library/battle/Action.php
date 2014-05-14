<?php

namespace Battle;

class Action {
	const TYPE_MESSAGE = 0;
	const TYPE_ATTACK = 1;
	const TYPE_MOVE = 2;

	private $player;
	private $game;
	private $payload;


	public static function load($id){
		$action = \R::load('action', $id)
		if(! $action){
			throw new \Exception("Action with id '$id' not found!")
		}
		return $action
	}

	public static function create($type, $player, $game, $payload){
		// @TODO: write it :)
	}

}
