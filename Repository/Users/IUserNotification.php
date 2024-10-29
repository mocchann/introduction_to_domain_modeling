<?php

namespace Repository\Users;

use DomainObject\ValueObject\Users\UserId;
use DomainObject\ValueObject\Users\UserName;

interface IUserNotification
{
    public function notifyId(UserId $id): void;
    public function notifyName(UserName $name): void;
}
