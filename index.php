<?php

/**
 * Chapter2: Value Object
 */

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

// FullNameは氏名を表現したオブジェクトで値の表現
// FullNameクラスに渡す引数さえ間違えなければ、確実に姓を取得できる
$full_name = new FullName("john", "smith");
echo $full_name->getLastName() . "\n";

// 値オブジェクトの変更方法(値の交換が可能)
// 値オブジェクトが不変であるあがゆえに、代入操作による交換以外の手段を表現できなくなっている
// 例.setFullName(full_name)のようなオブジェクトの値を直接変更するメソッドは値オブジェクトに実装してはならない
$full_name = new FullName("john", "smith");
$full_name = new FullName("john", "doe");

// 値オブジェクト同士の比較
$name_A = new FullName("john", "smith");
$name_B = new FullName("john", "smith");

// これはtrue
if ($name_A == $name_B) {
    echo "name_Aとname_Bは同じ値です" . "\n";
} else {
    echo "name_Aとname_Bは異なる値です" . "\n";
}

// 属性を取り出して比較
$name_A = new FullName("john", "smith");
$name_B = new FullName("masanobu", "naruse");

$compareResult = $name_A->getFirstName() === $name_B->getFirstName() && $name_A->getLastName() === $name_B->getLastName();

if ($compareResult) {
    echo "name_Aとname_Bは同じ値です" . "\n";
} else {
    echo "name_Aとname_Bは異なる値です" . "\n";
}

// これは一見自然なコードに見えるが、値オブジェクトの値を比較している
// つまり、0という値の値を比較する以下のコードと同じ意味合いになってしまう
// 0.value === 1.value
// 値オブジェクトはシステム固有の「値」である
// その属性を取り出して比較するよりも、値オブジェクト同士を比較することが自然な記述となる

$name_A = new FullName("masanobu", "naruse");
$name_B = new FullName("john", "smith");

$compareResult = $name_A === $name_B;
if ($compareResult) {
    echo "name_Aとname_Bは同じ値です" . "\n";
} else {
    echo "name_Aとname_Bは異なる値です" . "\n";
}

// このように自然な記述を行うには、値オブジェクトを比較するためのメソッドを提供する必要がある
// このようなメソッドを提供することで、値オブジェクトは値と同じように比較できるようになる

interface IEquatable
{
    public function getFirstName(): string;
    public function getLastName(): string;
    public function equals(object $other): bool;
}

class FullName2 implements IEquatable
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

    public function equals(object $other): bool
    {
        if (!($other instanceof FullName2)) return false;
        if ($other === null) return false;
        if ($this === $other) return true;
        return $this->first_name === $other->first_name && $this->last_name === $other->last_name;
    }
}
