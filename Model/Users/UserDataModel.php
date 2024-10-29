<?php

namespace Model\Users;

class UserDataModel
{
    public string $id;
    public string $name;

    function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
