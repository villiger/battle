<?php
namespace Battle
{

    class Game 
    {
        
        public $gameId;

        private $field;
        private $playerOne;
        private $playerTwo;
        private $playerOneUnits;
        private $playerTwoUnits;

        public function __construct($gameId = null){
            $this->field = new \Battle\Field();

            if($gameId === null){
                $this->field->generate();

                # Other initialization for new game goes here..

                $this->initialSave();
            } else {
                $this->gameId = $gameId;
                $game = \R::load('game', $gameId);
                $this->field->load(json_decode($game->field));
            }
            
        }

        public function getField(){
            return $this->field->get();
        }

        public function getFieldSize(){
            return \Battle\Field::FIELD_SIZE;
        }

        public function initialSave(){
            $game = \R::dispense('game');

            $game->field = json_encode($this->field->get());

            $this->gameId = \R::store($game);

        }

        public function save(){
            $game = \R::load('game', $this->gameId);
            $game->field = json_encode($this->field->get());
            $this->gameId = \R::store($game);
        }

        public function loadPost($post){
            $fieldSize = $post['field_size'];

            $field = array();

            for($i = 0; $i < $fieldSize; $i++){
                $field[$i] = array();
                for($j = 0; $j < $fieldSize; $j++){
                    $field[$i][$j] = $post['cell_' . $i . '_' . $j];
                }
            }

            $this->field->load($field);

            $this->save();
        }

    }
}