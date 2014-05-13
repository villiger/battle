<?php

namespace Battle
{
    class Game
    {
        private $id;
        private $field;

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
         * @throws \Exception
         */
        public function __construct($id)
        {
            $this->id = $id;

            $bean = \R::load("game", $id);
            if (! $bean) {
                throw new \Exception("Game with id '$id' not found.");
            }

            // Set random seed to generate the Field accordingly
            mt_srand((int) $bean->seed);

            $this->field = new Field($this, (int) $bean->field_width, (int) $bean->field_height);
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
                )
            );

            return json_encode($state);
        }
    }
}
