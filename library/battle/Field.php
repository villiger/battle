<?php

namespace Battle;

use Battle\Units\Unit;

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
    private $units;

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
        $this->units = array();
        $this->lastUnitId = 0;

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
     * @return array
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param int $id
     * @return Unit
     */
    public function getUnit($id)
    {
        return $this->units[$id];
    }


    /**
     * @param int $row
     * @param int $column
     * @return Unit
     */
    public function getUnitByPosition($row, $column)
    {
        foreach ($this->units as $unit) {
            /** @var $unit Unit */
            if ($unit->getRow() == $row && $unit->getColumn() == $column) {
                return $unit;
            }
        }

        return null;
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

    public function calcCost($sourceRow, $sourceColumn, $targetRow, $targetColumn){
        $visitedTiles = array();
        $nextTiles = array();
        $currentTiles = array(array($sourceRow, $sourceColumn));
        $currentCost = 0;

        $variations = array(array(1, 0), array(-1, 0), array(0, 1), array(0, -1), );

        while( $currentTiles ){
            if( in_array(array($targetRow, $targetColumn), $currentTiles) ){
                return $currentCost;
            }
            foreach( $currentTiles as $tile ){
                foreach( $variations as $variation ){
                    $newTile = array($tile[0] + $variation[0], $tile[1] + $variation[1]);
                    if ($this->isValidTile($newTile[0], $newTile[1]) && !in_array($newTile, $visitedTiles) ){
                        $nextTiles[] = $newTile;
                    }
                }
                $visitedTiles[] = $tile; 
            }
            $currentTiles = $nextTiles;
            // @TODO: How to handle more expensive routes? eg. swamps
            $currentCost++;
        }
    }

    /**
     * @param int $row
     * @param int $column
     * @return bool
     */
    public function isValidTile($row, $column)
    {
        if ($row < 0 || $row >= $this->getWidth()) {
            return false;
        } elseif ($column < 0 || $column >= $this->getHeight()) {
            return false;
        }

        // @TODO: Add cases for obstructions! eg. mountains

        return true;
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

    /**
     * @param User $player
     * @param int $row
     * @param int $column
     * @return bool
     */
    public function createUnit(User $player, $row, $column)
    {
        if ($this->game->isPlayer($player)) {
            $unitId = ++$this->lastUnitId;
            $unit = new Unit($this, $player, $unitId, $row, $column);

            $this->units[$unitId] = $unit;

            return $unit;
        }

        return false;
    }

    /**
     * @param Unit $unit
     */
    public function removeUnit(Unit $unit) {
        unset($this->units[$unit->getId()]);
    }
}
