<?php

namespace Battle\Units;

use Battle;
use Battle\Field;
use Battle\User;

class Unit
{
    private $id;
    private $user;
    private $field;
    private $row;
    private $column;
    private $maxLife;
    private $life;
    private $moveRange;
    private $attackRange;
    private $hasMoved;
    private $hasAttacked;

    public function __construct(Field $field, User $user, $id, $row, $column)
    {
        $this->id = $id;
        $this->field = $field;
        $this->user = $user;
        $this->row = $row;
        $this->column = $column;

        $this->maxLife = 10;
        $this->life = $this->maxLife;

        $this->moveRange = 5;
        $this->attackRange = 5;

        $this->hasMoved = false;
        $this->hasAttacked = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getMaxLife()
    {
        return $this->maxLife;
    }

    /**
     * @return int
     */
    public function getLife()
    {
        return $this->life;
    }

    /**
     * @param int $life
     */
    public function setLife($life)
    {
        $this->life = $life;
    }

    /**
     * @return int
     */
    public function getMoveRange()
    {
        return $this->moveRange;
    }

    /**
     * @param int $range
     */
    public function setMoveRange($range)
    {
        $this->moveRange = $range;
    }

    /**
     * @return int
     */
    public function getAttackRange()
    {
        return $this->attackRange;
    }

    /**
     * @param int $range
     */
    public function setAttackRange($range)
    {
        $this->attackRange = $range;
    }

    public function hasMoved()
    {
        return $this->hasMoved;
    }

    public function hasAttacked()
    {
        return $this->hasAttacked;
    }

    /**
     * Tries to set the unit to a new position.
     * 
     * @param int $row
     * @param int $column
     * @return bool
     */
    public function moveTo($row, $column)
    {
        if ($this->hasMoved() || $this->hasAttacked()) {
            return false;
        }

        if ($this->field->isValidTile($row, $column)) {
            if (! $this->field->getUnitByPosition($row, $column)) {
                $this->row = $row;
                $this->column = $column;

                $this->hasMoved = true;

                return true;
            }
        }

        return false;
    }

    /**
     * Tries to attack a position.
     *
     * @param int $row
     * @param int $column
     * @return int
     */
    public function attackOn($row, $column)
    {
        if ($this->hasAttacked()) {
            return false;
        }

        if ($this->field->isValidTile($row, $column)) {
            $target = $this->field->getUnitByPosition($row, $column);

            if ($target) {
                $this->hasAttacked = true;

                $damage = rand(3, 6);
                $currentLife = $target->getLife();
                $newLife = $currentLife - $damage;

                $target->setLife($newLife);

                if ($newLife <= 0) {
                    $this->field->removeUnit($target);
                }

                return $damage;
            }
        }

        return -1;
    }

    /**
     * Makes the unit ready for the next turn.
     */
    public function replenish()
    {
        $this->hasMoved = false;
        $this->hasAttacked = false;
    }
}
