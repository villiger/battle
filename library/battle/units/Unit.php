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

    public function __construct(Field $field, User $user, $id, $row, $column)
    {
        $this->id = $id;
        $this->field = $field;
        $this->user = $user;
        $this->row = $row;
        $this->column = $column;
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
     * Tries to set the unit to a new position.
     * 
     * @param int $row
     * @param int $column
     * @return bool
     */
    public function moveTo($row, $column)
    {
        if ($this->field->isEmptyTile($row, $column)) {
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
     * @return bool
     */
    public function attackOn($row, $column)
    {
        if (! $this->field->isEmptyTile($row, $column)) {
            // TODO: do the actuel attack

            return true;
        }

        return false;
    }
}
