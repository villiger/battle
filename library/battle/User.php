<?php

namespace Battle;

class User {
    private $id;
    private $email;
    private $name;
    private $picture;

    public static function create($email, $name, $picture)
    {
        $bean = \R::find('user', 'email LIKE ?', [ $email ]);

        if (! $bean) {
            $bean = \R::dispense('user');
            $bean->email = $email;
            $bean->name = $name;
            $bean->picture = $picture;
            \R::store($bean);
        }

        return new User((int) $bean->getID());
    }

    public function __construct($id)
    {
        $this->id = $id;

        $bean = \R::load('user', $id);
        if (! $bean) {
            throw new \Exception("User with id '$id' not found.'");
        }

        $this->email = $bean->email;
        $this->name = $bean->name;
        $this->picture = $bean->picture;
    }
}
