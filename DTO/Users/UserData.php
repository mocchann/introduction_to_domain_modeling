<?php

namespace DTO\Users;

use DomainObject\Entity\Users\User;

class UserData
{
    private string $id;
    private string $name;
    private string $mail_address;

    // コンストラクタでUserオブジェクトを受け取れば、プロパティが増えても変更箇所が少なくて済む
    public function __construct(User $user)
    {
        $this->id = $user->getId()->getValue();
        $this->name = $user->getName()->getValue();
        $this->mail_address = $user->getMailAddress()->getValue(); // 属性への代入処理
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMailAddress(): string // 追加された属性
    {
        return $this->mail_address;
    }
}
