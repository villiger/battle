<?php

namespace Battle
{
    class Field 
    {
        const TILE_GROUND = 0;
        const TILE_FOREST = 1;
        const TILE_WATER = 2;
        const TILE_MOUNTAIN = 3;
        const TILE_DESERT = 4;

        const NUM_TILES = 5;

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
         * @return array
         */
        public function getTiles()
        {
            return $this->tiles;
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
         * @param int $row
         * @param int $column
         * @return string
         */
        public function getTileType($row, $column)
        {
            switch ($this->getTile($row, $column)) {
                case self::TILE_FOREST:
                    return 'forest';
                case self::TILE_WATER:
                    return 'water';
                case self::TILE_MOUNTAIN:
                    return 'mountain';
                case self::TILE_DESERT:
                    return 'desert';
                case self::TILE_GROUND:
                default:
                    return 'ground';
            }
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
            } elseif ($rand < 70) {
                return self::TILE_FOREST;
            } elseif ($rand < 80) {
                return self::TILE_WATER;
            } elseif ($rand < 90) {
                return self::TILE_MOUNTAIN;
            } else {
                return self::TILE_DESERT;
            }
        }
    }
}
