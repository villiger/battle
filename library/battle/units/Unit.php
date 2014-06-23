<?php

namespace Battle\Units;

use Battle;
use Battle\Field;
use Battle\User;

abstract class Unit
{
    const TYPE_FIGHTER = 0;
    const TYPE_JUGGERNAUT = 1;
    const TYPE_ARCHER = 2;
    const TYPE_MINOTAUR = 3;

    private $id;
    private $user;
    private $field;
    private $row;
    private $column;
    private $life;
    private $hasMoved;
    private $hasAttacked;

    public static function create(Field $field, User $user, $id, $row, $column, $type) {
        switch ($type) {
            case self::TYPE_FIGHTER:
                return new Fighter($field, $user, $id, $row, $column);
            case self::TYPE_JUGGERNAUT:
                return new Juggernaut($field, $user, $id, $row, $column);
            case self::TYPE_ARCHER:
                return new Archer($field, $user, $id, $row, $column);
            case self::TYPE_MINOTAUR:
                return new Minotaur($field, $user, $id, $row, $column);
            default:
                throw new \Exception("No unit with type $type available.");
        }
    }

    public function __construct(Field $field, User $user, $id, $row, $column)
    {
        $this->id = $id;
        $this->field = $field;
        $this->user = $user;
        $this->row = $row;
        $this->column = $column;

        $this->life = $this->getMaxLife();

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
    public abstract function getType();

    /**
     * @return int
     */
    public abstract function getMaxLife();

    /**
     * @return int
     */
    public abstract function getMoveRange();

    /**
     * @return int
     */
    public abstract function getAttackRange();

    /**
     * @return int
     */
    public abstract function getMinDamage();

    /**
     * @return int
     */
    public abstract function getMaxDamage();

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

                $damage = rand($this->getMinDamage(), $this->getMaxDamage());
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
