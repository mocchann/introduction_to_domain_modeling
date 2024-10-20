<?php

namespace DomainObject\Factory;

use DomainObject\Entity\User;
use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserMailAddress;
use DomainObject\ValueObject\UserName;
use PDO;

class UserFactory
{
    public function create(UserName $name, UserMailAddress $mail_address): User
    {
        $seq_id = "";

        $connectionString = "mysql:host=localhost;dbname=test";
        $connection = new PDO($connectionString, "my_db_username", "my_db_password");
        $command = $connection->prepare("SELECT nextval('user_id_seq')");
        // ...略

        $id = new UserId($seq_id);
        return new User($id, $name, $mail_address);
    }
}
