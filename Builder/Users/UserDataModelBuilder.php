<?php

namespace Builder\Users;

use DomainObject\ValueObject\Users\UserId;
use DomainObject\ValueObject\Users\UserName;
use Model\Users\UserDataModel;
use Repository\Users\IUserNotification;

class UserDataModelBuilder implements IUserNotification
{
    // 通知されたデータはインスタンス変数で保持される
    private UserId $id;
    private UserName $name;

    public function notifyId(UserId $id): void
    {
        $this->id = $id;
    }

    public function notifyName(UserName $name): void
    {
        $this->name = $name;
    }

    // 通知されたデータからデータモデルを生成するメソッド
    public function build(): UserDataModel
    {
        return new UserDataModel($this->id->getValue(), $this->name->getValue());
    }
}
