<?php

namespace Battle\Units;

use Battle;
use Battle\Field;
use Battle\User;

class Archer extends Unit
{
    public function __construct(Field $field, User $user, $id, $row, $column)
    {
        parent::__construct($field, $user, $id, $row, $column);
    }

    public function getType()
    {
        return self::TYPE_ARCHER;
    }

    public function getMaxLife()
    {
        return 10;
    }

    public function getMoveRange()
    {
        return 6;
    }

    public function getAttackRange()
    {
        return 3;
    }

    public function getMinDamage()
    {
        return 4;
    }

    public function getMaxDamage()
    {
        return 6;
    }
}
