<?php

namespace DomainObject\Factory\Users;

use DomainObject\Entity\Users\User;
use DomainObject\ValueObject\Users\UserId;
use DomainObject\ValueObject\Users\UserMailAddress;
use DomainObject\ValueObject\Users\UserName;
use PDO;

class UserFactory implements IUserFactory
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
