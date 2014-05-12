<?php

namespace Battle
{
    class Field 
    {
        const TILE_GROUND = 0;
        const TILE_WATER = 1;
        const TILE_FOREST = 2;
        const TILE_HILL = 3;
        const TILE_MOUNTAIN = 4;
        const TILE_DESERT = 5;

        const MIN_TILE = 0;
        const MAX_TILE = 5;

        private $game;
        private $width;
        private $height;
        private $tiles;

        /**
         * @param Game $game
         * @param int $width
         * @param int $height
         */
        public function __construct(Game $game, $width, $height)
        {
            $this->game = $game;
            $this->width = $width;
            $this->height = $height;

            $this->generate();
        }

        /**
         * @return int
         */
        public function getWidth()
        {
            return $this->width;
        }

        /**
         * @return int
         */
        public function getHeight()
        {
            return $this->height;
        }

        /**
         * @param int $row
         * @param int $column
         * @return int
         */
        public function getTile($row, $column)
        {
            return $this->tiles[$row][$column];
        }

        /**
         * @throws \Exception
         */
        private function generate()
        {
            if ($this->tiles) {
                throw new \Exception("Field already generated.");
            }

            $this->tiles = array();

            for($row = 0; $row < $this->getHeight(); $row++) {
                $this->tiles[$row] = array();

                for($column = 0; $column < $this->getWidth(); $column++) {
                    // top and bottom row must be 'ground' because of initial unit placement
                    if ($row === 0 or $row === $this->getHeight() - 1) {
                        $this->tiles[$row][$column] = self::TILE_GROUND;
                    } else {
                        $this->tiles[$row][$column] = self::getRandomTile();
                    }
                }
            }
        }

        /**
         * @return int
         */
        private static function getRandomTile()
        {
            $rand = mt_rand(0, 100);

            if ($rand < 50) {
                return self::TILE_GROUND;
            } elseif ($rand < 60) {
                return self::TILE_WATER;
            } elseif ($rand < 80) {
                return self::TILE_FOREST;
            } elseif ($rand < 90) {
                return self::TILE_HILL;
            } elseif ($rand < 95) {
                return self::TILE_MOUNTAIN;
            } else {
                return self::TILE_DESERT;
            }
        }
    }
}