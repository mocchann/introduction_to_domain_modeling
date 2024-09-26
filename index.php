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
    private readonly string $first_name;
    private readonly string $middle_name;
    private readonly string $last_name;

    public function __construct(string $first_name, string $middle_name = null, string $last_name)
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
        return $this->first_name === $other->first_name
            && $this->last_name === $other->last_name
            // 値オブジェクトを用意しておけば、条件を追加するときも以下の1行で済む
            // シンプルな条件分岐のロジックでは、リポジトリ全体で同じような処理が他にもないか確認 & 全箇所修正が必要になるが
            // 値オブジェクトが比較の手段を提供することで回避できる
            && $this->middle_name === $other->middle_name;
    }
}

$name_A = new FullName2("masanobu", "taro", "naruse");
$name_A->equals(new FullName2("john", "harman", "smith"));

class FullName3 implements IEquatable
{
    private readonly FirstName $first_name;
    private readonly LastName $last_name;

    public function __construct(FirstName $first_name, LastName $last_name)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    public function getFirstName(): string
    {
        return $this->first_name->getValue();
    }

    public function getLastName(): string
    {
        return $this->last_name->getValue();
    }

    public function equals(object $other): bool
    {
        return $other instanceof FullName3
            && $this->first_name === $other->first_name
            && $this->last_name === $other->last_name;
    }
}

class FirstName
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value) || is_null($value)) throw new InvalidArgumentException("1文字以上である必要があります");
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

class LastName
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value) || is_null($value)) throw new InvalidArgumentException("1文字以上である必要があります");
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

/**
 * 姓や名まで値オブジェクトにするのは正しいとも間違いとも言えない
 * 筆者の判断基準としては
 * 1. そこにルールが存在しているか
 * 2. それを単体で取り扱いたいか
 * 氏名だと、姓と名で構成されるルールがあり、単体で取り扱っているため値オブジェクトにする
 * 姓 or 名の場合、現在時点でシステム上の制約はないため、値オブジェクトにする必要はないと判断する
 */

// 以下のようにすれば、値オブジェクトにしなくてもルール担保が可能
class FullName4 implements IEquatable
{
    private readonly string $first_name;
    private readonly string $last_name;

    public function __construct(string $first_name, string $last_name)
    {
        if ($first_name === null) throw new InvalidArgumentException("名は必須です");
        if ($last_name === null) throw new InvalidArgumentException("姓は必須です");
        if (!$this->validateName($first_name)) throw new InvalidArgumentException("許可されていない文字が使われています");
        if (!$this->validateName($last_name)) throw new InvalidArgumentException("許可されていない文字が使われています");

        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    private function validateName(string $value): bool
    {
        return preg_match("/^[a-zA-Z]+$/", $value);
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
        return $other instanceof FullName4
            && $this->first_name === $other->first_name
            && $this->last_name === $other->last_name;
    }
}

// もし姓と名を値オブジェクトにするならば、次に考えるべきは「姓と名を分けるかどうか」
// 別物として取り扱う必要がなければ以下のようになる
class Name
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if ($value === null) throw new InvalidArgumentException("名前は必須です");
        if (!preg_match("/^[a-zA-Z]+$/", $value)) throw new InvalidArgumentException("許可されていない文字が使われています");

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

class FullName5 implements IEquatable
{
    private readonly Name $first_name;
    private readonly Name $last_name;

    public function __construct(Name $first_name, Name $last_name)
    {
        if ($first_name === null) throw new InvalidArgumentException("名は必須です");
        if ($last_name === null) throw new InvalidArgumentException("姓は必須です");

        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    public function getFirstName(): string
    {
        return $this->first_name->getValue();
    }

    public function getLastName(): string
    {
        return $this->last_name->getValue();
    }

    public function equals(object $other): bool
    {
        return $other instanceof FullName5
            && $this->first_name === $other->first_name
            && $this->last_name === $other->last_name;
    }
}

// 量と通過単位を持つお金オブジェクト
class Money
{
    private readonly int $amount;
    private readonly string $currency;

    public function __construct(int $amount, string $currency)
    {
        if ($currency === null) throw new InvalidArgumentException("通貨は必須です");
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    // 値オブジェクトはデータを保持するコンテナではなく、ふるまいを持つことができるオブジェクト
    // 金銭の加算処理の実装例
    public function add(Money $arg): Money
    {
        if ($arg === null) throw new InvalidArgumentException("加算する金額が必要です");
        if ($this->currency !== $arg->currency) throw new InvalidArgumentException("通貨が異なります");

        // 値オブジェクトは不変であるため、計算を行った結果は新しいインスタンスとして返す
        return new Money($this->amount + $arg->amount, $this->currency);
    }
}

$myMoney = new Money(1000, "JPY");
$allowance = new Money(3000, "JPY");
$result = $myMoney->add($allowance);
echo $result->getAmount() . "\n";

/**
 * 値オブジェクトを使うモチベーション
 * 1. 表現力を増す
 */

// プリミティブな値を利用した製造番号
// これだと処理の途中で以下を見つけたときに一目で内容がわからないため、定義元を探しに行くことになる
$model_number = "a20421-100-1";

// 値オブジェクトを利用した製造番号
// クラスを見れば構成要素が何であるかが一目でわかる(自己文章化)
class ModelNumber
{
    private readonly string $product_code;
    private readonly string $branch;
    private readonly string $lot;

    public function __construct(string $product_code, string $branch, string $lot)
    {
        if ($product_code === null) throw new InvalidArgumentException("製品コードは必須です");
        if ($branch === null) throw new InvalidArgumentException("枝番は必須です");
        if ($lot === null) throw new InvalidArgumentException("ロット番号は必須です");

        $this->product_code = $product_code;
        $this->branch = $branch;
        $this->lot = $lot;
    }

    public function toString(): string
    {
        return $this->product_code . "=" . $this->branch . "=" . $this->lot;
    }
}

/**
 * 値オブジェクトを使うモチベーション
 * 2. 不正な値を存在させない
 */

// ユーザー名は3文字以上という制約がある元で存在してはいけない値
$userName = "me";
// 不正な値の存在を許すとその値を利用する箇所で都度バリデーションを行う必要がある
if (mb_strlen($userName) >= 3) {
    // 正常な値なので処理する
} else {
    // throw new Exception("ユーザー名は3文字以上である必要があります");
}

// 値オブジェクトをうまく利用すれば異常な値の存在を防げる
class UserName
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if ($value === null) throw new InvalidArgumentException("ユーザー名は必須です");
        if (mb_strlen($value) < 3) throw new InvalidArgumentException("ユーザー名は3文字以上である必要があります");

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

/**
 * 値オブジェクトを使うモチベーション
 * 3. 誤った代入を防ぐ
 */

// 単純な代入を行うコード
// このコードをみただけだと、user->idにnameが代入される処理に正当性があるのかわからない
class User
{
    public $id;
}

function createUser(string $name): User
{
    $user = new User();
    $user->id = $name;
    return $user;
}

// 値オブジェクトを利用して、自己文章化を進める
class UserId
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if ($value === null) throw new InvalidArgumentException("ユーザーIDは必須です");

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

class UserName2
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if ($value === null) throw new InvalidArgumentException("ユーザー名は必須です");

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

// 値オブジェクトを利用するようにしたUserクラス
class User2
{
    public UserId $id;
    public UserName2 $name;

    public function __construct(UserId $id, UserName2 $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): UserName2
    {
        return $this->name;
    }
}

// このようにすることで、代入時に型が違うとエラーが出る
function createUser2(UserName2 $name): User2
{
    $user = new User2($name); // TypeErrorが出る(静的型付け言語ならランタイムでなく、コンパイルエラーが出るがphpなので仕方なし。。)
    return $user;
}

/**
 * 値オブジェクトを使うモチベーション
 * 4. ロジックの散在を防ぐ
 */

// 入力値の確認を伴うユーザの作成処理
function createUser3(string $name): void
{
    if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");
    if (mb_strlen($name) < 3) throw new InvalidArgumentException("ユーザー名は3文字以上である必要があります");

    $user = new User($name);
    //　...略
}

// 局所的にこのコードに問題はないが、ユーザー情報を更新する処理があると、同様のコードを記述することになる
function updateUser(string $id, string $name)
{
    if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");
    if (mb_strlen($name) < 3) throw new InvalidArgumentException("ユーザー名は3文字以上である必要があります");

    // ...略
}

// 上の例では2箇所変更で済むが実際のアプリケーションでは複数箇所に同じようなコードが潜んでいる可能性がある
// 影響を調査して、変更を行うのは非常に面倒で労力が要求される

// 値オブジェクトにルールをまとめることで、ロジックの散在を防ぐ
class UserName3
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if ($value === null) throw new InvalidArgumentException("ユーザー名は必須です");
        if (mb_strlen($value) < 3) throw new InvalidArgumentException("ユーザー名は3文字以上である必要があります");

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

// 値オブジェクトを利用した新規作成処理と更新処理
function createUser4(string $name): void
{
    $user_name = new UserName3($name);
    // $user = new User2($user_name);
    // ...略
}

function updateUser2(string $id, string $name)
{
    $user_name = new UserName3($name);
    // ...略
}

/**
 * chapter3: Entity
 */

// 値オブジェクトとは異なり、Entityは可変である
// ユーザーを表すclass
class User3
{
    private string $name;

    public function __construct(string $name)
    {
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");
        if (mb_strlen($name) < 3) throw new InvalidArgumentException("ユーザー名は3文字以上である必要があります");

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

// あとから思いついた素敵なユーザー名を登録できるようにするために可変なオブジェクトに変化させる
class User4
{
    private string $name;

    public function __construct(string $name)
    {
        $this->changeName($name);
    }

    public function changeName(string $name): void
    {
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");
        if (mb_strlen($name) < 3) throw new InvalidArgumentException("ユーザー名は3文字以上である必要があります");

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

// 値オブジェクトは同姓同名の人間も区別できないが、エンティティは同姓同名であっても区別できる
// 人間同様にシステム上のユーザーも区別できるようにするためには、識別子を追加する
class UserId2
{
    private string $value;

    public function __construct(string $value)
    {
        if ($value === null) throw new InvalidArgumentException("ユーザーIDは必須です");

        $this->value = $value;
    }
}

class User5
{
    private readonly UserId2 $id;
    private string $name;

    public function __construct(UserId2 $id, string $name)
    {
        if ($id === null) throw new InvalidArgumentException("ユーザーIDは必須です");
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");

        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): UserId2
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

interface IEquatable2
{
    public function equals(object $other): bool;
}

class User6 implements IEquatable2
{
    private readonly UserId2 $id;
    private string $name;

    public function __construct(UserId2 $id, string $name)
    {
        if ($id === null) throw new InvalidArgumentException("ユーザーIDは必須です");

        $this->id = $id;
        $this->changeUserName($name);
    }

    public function changeUserName(string $name): void
    {
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");
        if (mb_strlen($name) < 3) throw new InvalidArgumentException("ユーザー名は3文字以上である必要があります");

        $this->name = $name;
    }

    public function getId(): UserId2
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function equals(object $other): bool
    {
        return $other instanceof User6
            && $this->id === $other->id;
    }
}

// エンティティの比較処理では同一性を表す識別子(id)だけが比較対象となる
function check(User6 $left_user, User6 $right_user): void
{
    if ($left_user->equals($right_user)) {
        echo "同じユーザーです" . "\n";
    } else {
        echo "異なるユーザーです" . "\n";
    }
}

/**
 * chapter4: Domain Service
 */
