<?php

namespace Battle\Units;

use Battle;
use Battle\Field;
use Battle\User;

class Juggernaut extends Unit
{
    public function __construct(Field $field, User $user, $id, $row, $column)
    {
        parent::__construct($field, $user, $id, $row, $column);
    }

    public function getType()
    {
        return self::TYPE_JUGGERNAUT;
    }

    public function getMaxLife()
    {
        return 40;
    }

    public function getMoveRange()
    {
        return 2;
    }

    public function getAttackRange()
    {
        return 1;
    }

    public function getMinDamage()
    {
        return 4;
    }

    public function getMaxDamage()
    {
        return 5;
    }
}
