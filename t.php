<?php
class Address
{
    public string $city;

    public function __construct(string $city)
    {
        $this->city = $city;
    }
}

class User
{
    public string $name;
    public Address $address;

    public function __construct(string $name, Address $address)
    {
        $this->name = $name;
        $this->address = $address;
    }

    public function __clone()
    {
        // Addressオブジェクトもクローンする
        $this->address = clone $this->address;
    }
}

$address = new Address("Tokyo");
$user1 = new User("Alice", $address);

// ディープコピー
$user2 = clone $user1;

// コピー後に元のオブジェクトのアドレスを変更
$user1->address->city = "Osaka";

echo $user2->address->city; // 出力: Tokyo