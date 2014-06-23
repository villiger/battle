<?php

namespace Battle\Units;

use Battle;
use Battle\Field;
use Battle\User;

class Fighter extends Unit
{
    public function __construct(Field $field, User $user, $id, $row, $column)
    {
        parent::__construct($field, $user, $id, $row, $column);
    }

    public function getType()
    {
        return self::TYPE_FIGHTER;
    }

    public function getMaxLife()
    {
        return 20;
    }

    public function getMoveRange()
    {
        return 4;
    }

    public function getAttackRange()
    {
        return 1;
    }

    public function getMinDamage()
    {
        return 3;
    }

    public function getMaxDamage()
    {
        return 5;
    }
}
