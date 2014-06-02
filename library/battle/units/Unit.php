<?php


namespace Battle\Units;

use Battle;

class Unit {
    private $id;
    private $userId;
    private $field;
    private $row;
    private $column;

    /**
     * @param Battle\Field $field
     * @param int $userId
     * @param int $row
     * @param int $column
     * @return Unit
     */
    public static function create(Battle\Field $field, $userId, $row, $column)
    {
        $bean = \R::dispense('unit');
        $bean->user_id = $userId;
        $bean->row = $row;
        $bean->column = $column;
        $bean->updated = \R::isoDateTime();
        $bean->created = \R::isoDateTime();
        \R::store($bean);

        return new Unit($field, (int) $bean->getID());
    }

    /**
     * @param Battle\Field $field
     * @param int $id
     * @throws \Exception
     */
    public function __construct(Battle\Field $field, $id)
    {
        $this->id = $id;
        $this->field = $field;

        $bean = \R::load("unit", $this->getId());
        if (! $bean) {
            throw new \Exception("Unit with id '{$this->getId()}' not found.");
        }

        $this->userId = $bean->user_id;
        $this->row = $bean->row;
        $this->column = $bean->column;
    }

    /**
     * @throws \Exception
     */
    public function store()
    {
        $bean = \R::load("unit", $this->getId());
        if (! $bean) {
            throw new \Exception("Unit with id '{$this->getId()}' not found.");
        }

        $bean->row = $this->row;
        $bean->column = $this->column;
        $bean->updated = \R::isoDateTime();
        \R::store($bean);
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
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
