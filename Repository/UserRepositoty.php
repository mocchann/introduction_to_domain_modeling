<?php

namespace Repository;

use DomainObject\Entity\User;
use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserMailAddress;
use DomainObject\ValueObject\UserName;
use PDO;
use PDOException;

class UserRepository implements IUserRepository
{
    private readonly PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findId(UserId $id): User
    {
        $sql = "
            SELECT id, name
            FROM users
            WHERE id = :id;
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id->getValue(), PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new PDOException('User not found.');
        }

        return new User(
            new UserId($row['id']),
            new UserName($row['name']),
            new UserMailAddress($row['mail_address'])
        );
    }

    public function findName(UserName $name): User
    {
        $sql = "
            SELECT id, name
            FROM users
            WHERE name = :name;
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':name', $name->getValue(), PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new PDOException('User not found.');
        }

        return new User(
            new UserId($row['id']),
            new UserName($row['name']),
            new UserMailAddress($row['mail_address'])
        );
    }

    public function save(User $user, ?PDO $transaction = null): void
    {
        try {
            if ($transaction !== null) {
                $this->connection->beginTransaction();
            }

            $sql = "
                INSERT INTO users (id, name)
                VALUES (:id, :name)
                ON DUPLICATE KEY UPDATE
                name = VALUES(name);
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':id', $user->getId()->getValue(), PDO::PARAM_STR);
            $stmt->bindValue(':name', $user->getName()->getValue(), PDO::PARAM_STR);

            $stmt->execute();

            if ($transaction !== null) {
                $this->connection->commit();
            }
        } catch (PDOException $e) {
            if ($transaction !== null) {
                $this->connection->rollBack();
            }

            throw $e;
        }
    }

    public function delete(User $user): void
    {
        $sql = "
            DELETE FROM users
            WHERE id = :id;
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $user->getId()->getValue(), PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new PDOException('User not found.');
        }
    }
}
