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

    public function __construct(Field $field, User $user, $id, $row, $column)
    {
        $this->id = $id;
        $this->field = $field;
        $this->user = $user;
        $this->row = $row;
        $this->column = $column;

        $this->maxLife = 10;
        $this->life = $this->maxLife;
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
     * Tries to set the unit to a new position.
     * 
     * @param int $row
     * @param int $column
     * @return bool
     */
    public function moveTo($row, $column)
    {
        if ($this->field->isValidTile($row, $column)) {
            $this->row = $row;
            $this->column = $column;

            return true;
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
        if ($this->field->isValidTile($row, $column)) {
            $target = $this->field->getUnitByPosition($row, $column);

            if ($target) {
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
}
