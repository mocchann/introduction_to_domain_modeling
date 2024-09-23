<?php

// プリミティブな値で「氏名」を表現する
$full_name = "naruse masanobu";
echo $full_name . "\n";

// システムによっては姓だけを表示したい
$tokens = explode(" ", $full_name);
$last_name = $tokens[0];
echo $last_name . "\n";

// このロジックではうまく動作しない場合がある
// smithが姓なのにjohnが表示されてしまう
$full_name = "john smith";
$tokens = explode(" ", $full_name);
$last_name = $tokens[0];
echo $last_name . "\n";

// このような問題を解決する手段としてオブジェクト指向プログラミングではclassが利用される
class FullName
{
    private string $first_name;
    private string $last_name;

    public function __construct(string $first_name, string $last_name)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }
}
