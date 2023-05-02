<?php

use Phalcon\Mvc\Model;

// user database
class Users extends Model
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
}
