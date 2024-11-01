<?php

namespace Chapter2to7;

/**
 * Chapter2: Value Object
 */

use Illuminate\Database\Schema\MySqlBuilder;

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
function createUser2(UserName2 $name)
{
    // $user = new User2($name); // TypeErrorが出る(静的型付け言語ならランタイムでなく、コンパイルエラーが出るがphpなので仕方なし。。)
    // return $user;
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

// 値オブジェクトやエンティティに記述すると不自然なふるまいはドメインサービスに記述する
class User7
{
    private readonly UserId2 $id;
    private UserName3 $name;

    public function __construct(UserId2 $id, UserName3 $name)
    {
        if ($id === null) throw new InvalidArgumentException("ユーザーIDは必須です");
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");

        $this->id = $id;
        $this->name = $name;
    }

    // 追加した重複確認の振る舞い
    public function exists(User7 $user)
    {
        // 重複を確認するコード
    }
}

// ↑のオブジェクトを使って重複確認をしてみる
$user_id = new UserId2("id");
$user_name = new UserName3("nrs");
$user = new User7($user_id, $user_name);

// 生成したオブジェクト自身に問い合わせをすることになる
$duplicate_check_result = $user->exists($user);
echo $duplicate_check_result . "\n"; // true? false?

// 重複確認用のインスタンスを用意するのはどうか
$check_id = new UserId2("check");
$check_name = new UserName3("checker");
// これはUserオブジェクトでありながら、ユーザーではないオブジェクト
$check_object = new User7($check_id, $check_name);

$user_id = new UserId2("id");
$user_name = new UserName3("nrs");
$user = new User7($user_id, $user_name);

$duplicate_check_result = $check_object->exists($user);
echo $duplicate_check_result . "\n";

// このような不自然さを解決するのがドメインサービス
class UserService
{
    public function exists(User7 $user)
    {
        // 重複を確認するコード
    }
}

$user_service = new UserService();
$user_id = new UserId2("id");
$user_name = new UserName3("nrs");
$user = new User7($user_id, $user_name);

// ドメインサービスに問い合わせ
$duplicate_check_result = $user_service->exists($user);
echo $duplicate_check_result . "\n";

// ドメインサービスにはすべてのふるまいを記述できてしまう
class User8
{
    private readonly UserId2 $id;
    public UserName3 $name;

    public function __construct(UserId2 $id, UserName3 $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): UserId2
    {
        return $this->id;
    }

    public function getName(): UserName3
    {
        return $this->name;
    }

    public function setName(UserName3 $name)
    {
        $this->name = $name;
    }
}

// ドメインサービスにユーザー名変更のふるまいを記述するとUserオブジェクトからふるまいやルールを読み取ることができなくなる
// これをドメインモデル貧血症といって、オブジェクト指向のデータとふるまいをまとめる戦略の逆を行っている
// ユーザー名を変更するふるまいはUserクラスに定義するべき
class UserService2
{
    public function changeName(User8 $user, UserName3 $name)
    {
        if ($user === null) throw new InvalidArgumentException("ユーザーは必須です");
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");

        $user->name = $name;
    }

    public function createUser(UserId2 $id, UserName3 $name): User8
    {
        return new User8($id, $name);
    }
}

// ユースケースを組み立てる
// まずはユーザーを作成
class User9
{
    private readonly UserId3 $id;
    private UserName4 $name;

    public function __construct(UserName4 $name)
    {
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");

        $this->id = new UserId3(uniqid());
        $this->name = $name;
    }

    public function getId(): UserId3
    {
        return $this->id;
    }

    public function getName(): UserName4
    {
        return $this->name;
    }
}

class UserId3
{
    private string $value;

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

class UserName4
{
    private string $value;

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

// ユーザー作成の具体的な処理
class Program
{
    public function createUser(string $user_name): void
    {
        $user_name = new UserName4($user_name);
        $user =  new User9($user_name);

        $user_service = new UserService3();
        if ($user_service->exists($user)) {
            throw new Exception($user . "は既に存在しています");
        }

        $connectionString = "mysql:host=localhost;dbname=test";
        $username = "my_db_username";
        $password = "my_db_password";

        try {
            $connection = new PDO($connectionString, $username, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO users (id, name) VALUES (:id, :name)";
            $statement = $connection->prepare($sql);
            $statement->bindParam(":id", $user->getId()->getValue());
            $statement->bindParam(":name", $user->getName()->getValue());

            $statement->execute();
        } catch (PDOException $e) {
            throw new Exception("データベースエラー:" . $e->getMessage());
        }
    }
}

// ドメインサービスの実装
// このコードだと柔軟性に乏しい
//例えばデータストアがRDBからNoSQLに変わった場合、ユーザー作成処理の本質は変わらないにも関わらず、このコードは全て書き換える必要がある
class UserService3
{
    public function exists(User9 $user): bool
    {
        $connectionString = "mysql:host=localhost;dbname=test";
        $username = "my_db_username";
        $password = "my_db_password";

        try {
            $connection = new PDO($connectionString, $username, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT * FROM users WHERE id = :id";
            $statement = $connection->prepare($sql);
            $statement->bindParam(":id", $user->getId()->getValue());

            $statement->execute();
            $count = $statement->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            throw new Exception("データベースエラー:" . $e->getMessage());
        }
    }
}

// データストアといったインフラストラクチャが絡まないドメインオブジェクトの操作に徹した例
// 物流拠点のエンティティ
class PhysicalDistributionBase
{
    // ...略

    public function ship(Baggage $baggage) // 出庫
    {
        // ...略
    }

    public function receive(Baggage $baggage) // 入庫
    {
        // ...略
    }

    // 物流拠点に輸送のふるまいを定義する
    public function transport(PhysicalDistributionBase $to, Baggage $baggage)
    {
        $shippedBaggage = $this->ship($baggage);
        $to->receive($shippedBaggage);
    }
}

// 物流拠点から物流拠点に直接荷物を渡すのは違和感を覚える
// このようなふるまいは輸送を執り行うドメインサービスに記述する
class TransportService
{
    public function transport(PhysicalDistributionBase $from, PhysicalDistributionBase $to, Baggage $baggage)
    {
        $shippedBaggage = $from->ship($baggage);
        $to->receive($shippedBaggage);

        // 輸送の記録を残す
        // ...略
    }
}

/**
 * chapter5: Repository
 */

// UserService3のような具体的でややこしいデータ永続化の処理は抽象的に扱うと処理の趣旨が際立つ
class Program2
{
    private IUserRepository $user_repository;

    public function __construct(IUserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function createUser(string $user_name): void
    {
        $user = new User9(new UserName4($user_name));

        $user_service = new UserService4($this->user_repository);
        if ($user_service->exists($user)) {
            throw new Exception($user . "は既に存在しています");
        }

        $user_service->save($user);
    }
}

// リポジトリを利用したドメインサービスの実装
// このように永続化をリポジトリを用いて抽象化することで、ビジネスロジックはより純粋なものに昇華される
class UserService4
{
    private IUserRepository $user_repository;

    public function __construct(IUserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function exists(User9 $user): bool
    {
        $found = $this->user_repository->find($user->getName());

        return $found !== null;
    }
}

// Userクラスのリポジトリインターフェース
// インスタンスを保存するふるまいとユーザー名によるインスタンスの復元を提供している
interface IUserRepository
{
    public function find(UserName4 $name): User9;
    public function save(User9 $user): void;
    /**
     * 重複チェックという目的を鑑みるとexistsメソッドをリポジトリに実装するアイディアもあるが、リポジトリの責務はあくまでオブジェクト永続化
     * ユーザーの重複チェックはドメインに近く、をれをリポジトリに実装するのは責務としてふさわしくない
     * 
     * public function exists(User9 $user): bool;
     */
}

// もしドメインサービスにインフラストラクチャにまつわる処理を嫌って、リポジトリにexistsメソッドを実装したい場合
interface IUserRepository2
{
    public function find(UserName4 $name): User9;
    public function save(User9 $user): void;
    // 重複確認のキーを渡すようにすれば、ドメインサービス側から見ても何によって重複確認を行っているかが明確になる
    public function exists(UserName4 $user): bool;
}

// SQLを利用したリポジトリ
class UserRepository implements IUserRepository
{
    private string $connectionString = "mysql:host=localhost;dbname=test";

    public function save(User9 $user): void
    {
        $connection = new PDO($this->connectionString, "my_db_username", "my_db_password");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO users (id, name) VALUES (:id, :name)";
        $statement = $connection->prepare($sql);
        $statement->bindParam(":id", $user->getId()->getValue());
        $statement->bindParam(":name", $user->getName()->getValue());

        $statement->execute();
    }

    public function find(UserName4 $name): User9
    {
        $connection = new PDO($this->connectionString, "my_db_username", "my_db_password");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM users WHERE name = :name";
        $statement = $connection->prepare($sql);
        $statement->bindParam(":name", $name->getValue());
        $statement->execute();

        return $statement->fetchObject(User9::class);
    }
}

// リポジトリをProgramクラスに渡す
$user_repository = new UserRepository();
$program = new Program2($user_repository);
$program->createUser("nrs");

class User10
{
    private UserId $id;
    private UserName4 $name;

    public function __construct(UserId $id, UserName4 $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): UserName4
    {
        return $this->name;
    }

    public function __clone()
    {
        // 必要に応じて、ディープコピーのためのカスタムクローン処理を追加
        $this->id = clone $this->id;
        $this->name = clone $this->name;
    }
}

interface IUserRepository3
{
    public function find(UserName4 $name): ?User10;
    public function save(User10 $user): void;
}

// テスト用のリポジトリ
class InMemoryUserRepository implements IUserRepository3
{
    private array $store = [];

    public function find(UserName4 $user_name): ?User10
    {
        foreach ($this->store as $user) {
            if ($user_name === $user->getName()) {
                return clone $user;
            }
        }
        return null;
    }

    public function save(User10 $user): void
    {
        $this->store[$user->getId()->getValue()] = clone $user;
    }
}

// ユーザー作成処理をテストする
$user_repository = new InMemoryUserRepository();
$program = new Program2($user_repository);
$program->createUser("nrs");

// データを取り出して確認
$head = $user_repository->store[0];
assert($head->getName()->getValue() === "nrs");

// ORMを利用したリポジトリ
class ORMUserRepository implements IUserRepository3
{
    private readonly MySqlBuilder $builder;

    public function __construct(MySqlBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function find(UserName4 $name): ?User10
    {
        $target = $this->builder->table("users")->where("name", $name->getValue())->first();
        if (is_null($target)) return null;
        return new User10(new UserId($target->id), new UserName4($target->name));
    }

    public function save(User10 $user): void
    {
        $found = $this->builder->table("users")->where("id", $user->getId()->getValue())->first();
        if (is_null($found)) {
            $this->builder->table("users")->insert([
                "id" => $user->getId()->getValue(),
                "name" => $user->getName()->getValue()
            ]);
        } else {
            $this->builder->table("users")->where("id", $user->getId()->getValue())->update([
                "name" => $user->getName()->getValue()
            ]);
        }
    }
}

// ORMリポジトリを利用したテスト
$user_repository = new ORMUserRepository(new MySqlBuilder());
$program = new Program2($user_repository);
$program->createUser("nrs");

// データを取り出して確認
$head = $user_repository->find(new UserName4("nrs"));
assert($head->getName()->getValue() === "nrs");

namespace BuildingSoftware;

/**
 * chapter8: Building Software
 */

require "vendor/autoload.php";

use Chapter2to7\InMemoryUserRepository;
use Chapter2to7\IUserRepository;
use Chapter2to7\UserService;
use DI\Container;
use DI\ContainerBuilder;
use UseCase\UserApplicationService;

class Program
{
    private static Container $container;

    public function main()
    {
        self::startup();

        while (true) {
            echo "ユーザー名を入力してください" . "\n";
            $user_name = trim(fgets(STDIN));

            echo "メールアドレスを入力してください" . "\n";
            $mail_address = trim(fgets(STDIN));

            $user_application_service = self::$container->get(UserApplicationService::class);
            $user_application_service->register($user_name, $mail_address);

            echo "ユーザーを登録しました" . "\n";
            echo "続けて登録しますか？(y/n)" . "\n";
            $continue = trim(fgets(STDIN));
            if ($continue !== "y") break;
        }
    }

    public static function startup(): void
    {
        $container_builder = new ContainerBuilder();
        $container_builder->addDefinitions([
            IUserRepository::class => \DI\autowire(InMemoryUserRepository::class),
            UserService::class => \DI\autowire(UserService::class),
            UserApplicationService::class => \DI\autowire(UserApplicationService::class),
        ]);

        self::$container = $container_builder->build();
    }
}

/**
 * Chapter9: Factory
 */

namespace Factory;

use Chapter2to7\IUserRepository;
use Chapter2to7\UserId;
use Chapter2to7\UserName;
use Chapter2to7\UserService;
use Exception;
use PDO;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use UseCase\Command\UserRegisterCommand;

interface IUserFactory
{
    public function create(UserName $name): User;
}

// シーケンスを利用したファクトリ
class UserFactory implements IUserFactory
{
    public function create(UserName $name): User
    {
        $seq_id = "";

        $connectionString = "mysql:host=localhost;dbname=test";
        $connection = new PDO($connectionString, "my_db_username", "my_db_password");
        $command = $connection->prepare("SELECT nextval('user_id_seq')");
        // ...略

        $id = new UserId($seq_id);
        return new User($id, $name);
    }
}

// ファクトリを用いればUserのコンストラクタはひとつになる
class User
{
    private readonly UserId $id;
    private readonly UserName $name;

    public function __construct(UserId $id, UserName $name)
    {
        if ($id === null) throw new InvalidArgumentException("ユーザーIDは必須です");
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");

        $this->id = $id;
        $this->name = $name;
    }

    // ...略
}

// ファクトリを利用するUserApplicationService
class UserApplicationService
{
    private readonly IUserFactory $user_factory;
    private readonly IUserRepository $user_repository;
    private readonly UserService $user_service;

    public function __construct(IUserFactory $user_factory, IUserRepository $user_repository, UserService $user_service)
    {
        $this->user_factory = $user_factory;
        $this->user_repository = $user_repository;
        $this->user_service = $user_service;
    }

    public function register(UserRegisterCommand $command): void
    {
        $user_name = new UserName($command->getName());
        // ファクトリを経由してインスタンスを生成する
        $user = $this->user_factory->create($user_name);

        if ($this->user_service->exists($user)) {
            throw new Exception($user . "は既に存在しています");
        }

        $this->user_repository->save($user);
    }
}

// registerをテストする際はDB接続しないでインメモリでテストしたい
class InMemoryUserFactory implements IUserFactory
{
    private int $current_id;

    public function create(UserName $name): User
    {
        $this->current_id++;

        return new User(
            new UserId((string)$this->current_id),
            $name
        );
    }
}

// サークルにはそのオーナーとなるユーザーがいる、オーナーの目印にユーザーIDを持たせる
$circle = new Circle($user->getId(), new CircleName("my circle"));

class User
{
    private readonly UserId $id;

    // ...略

    public function createCircle(CircleName $circle_name): Circle
    {
        return new Circle($this->id, $circle_name);
    }
}

/**
 * Chapter10: Transaction
 */

namespace Transaction;

use Chapter2to7\IUserRepository;
use Chapter2to7\UserName;
use Chapter2to7\UserRepository;
use Chapter2to7\UserService;
use Exception;
use Factory\IUserFactory;
use PDO;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use UseCase\Command\UserRegisterCommand;

// ユニットオブワークを利用したトランザクション
class UnitOfWork
{
    public static function registerNew(object $value): void {}
    public static function registerDirty(object $value): void {}
    public static function registerClean(object $value): void {}
    public static function registerDeleted(object $value): void {}
    public static function commit(): void {}
}

// マーキングのための手段を提供するエンティティの基底クラス
abstract class Entity
{
    protected function markNew(): void
    {
        UnitOfWork::registerNew($this);
    }

    protected function markDirty(): void
    {
        UnitOfWork::registerDirty($this);
    }

    protected function markClean(): void
    {
        UnitOfWork::registerClean($this);
    }

    protected function markDeleted(): void
    {
        UnitOfWork::registerDeleted($this);
    }
}

// エンティティは↑を継承し、データ変更時などに適宜マーキング作業を行う
class User extends Entity
{
    public function __construct(private UserName $name)
    {
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");

        $this->name = $name;
        $this->markNew();
    }

    public function getName(): UserName
    {
        return $this->name;
    }

    private function setName(UserName $name): void
    {
        $this->name = $name;
    }

    public function changeName(UserName $name): void
    {
        if ($name === null) throw new InvalidArgumentException("ユーザー名は必須です");

        $this->name = $name;
        $this->markDirty();
    }
}

// ユニットオブワークを利用したトランザクションの実装
class UserApplicationService
{
    public function __construct(
        private readonly UnitOfWork $uow,
        private readonly UserService $user_service,
        private readonly IUserFactory $user_factory,
        private readonly IUserRepository $user_repository,
    ) {
        $this->uow = $uow;
        $this->user_service = $user_service;
        $this->user_factory = $user_factory;
        $this->user_repository = $user_repository;
    }

    public function register(UserRegisterCommand $command): void
    {
        $user_name = new UserName($command->getName());
        $user = $this->user_factory->create($user_name);

        if ($this->user_service->exists($user)) {
            throw new Exception($user . "は既に存在しています");
        }

        $this->user_repository->save($user);
        $this->uow->commit();
    }
}

// リポジトリに変更の追跡を移譲したユニットオブワーク
class UnitOfWork implements IUnitOfWork
{
    public function __construct(
        private readonly PDO $connection,
        private UserRepository $user_repository,
    ) {
        $this->connection = $connection;
        $this->user_repository = $user_repository;
    }

    public function getUserRepository(): UserRepository
    {
        if ($this->user_repository === null) {
            $this->user_repository = new UserRepository($this->connection);
        }

        return $this->user_repository;
    }

    public function commit(): void
    {
        $this->connection->commit();
    }
}

/**
 * Chapter13: Evaluation
 */

namespace Evaluation;

use Chapter2to7\IUserRepository;
use DateTime;
use DomainObject\ValueObject\Circle\CircleId;
use Exception;
use Repository\Circle\ICircleRepository;
use UseCase\Circle\Command\CircleJoinCommand;

// * ユーザーにはプレミアムユーザーと通常ユーザーがいる
// * サークルの上限は30人まで
// * しかし、プレミアムユーザーが10人以上いるサークルの上限は50人

class CircleApplicationService
{
    private readonly ICircleRepository $circle_repository;
    private readonly IUserRepository $user_repository;

    // ...略

    public function join(CircleJoinCommand $command): void
    {
        $circle_id = new CircleId($command->getCircleId());
        $circle = $this->circle_repository->findById($circle_id);

        $users = $this->user_repository->find($circle->getMembers());
        // サークルに所属しているプレミアムユーザーの人数により上限が変わる
        $premium_user_number = count(array_filter($users, function ($user) {
            return $user->isPremium();
        }));
        $circle_upper_limit = $premium_user_number < 10 ? 30 : 50;
        if ($circle->countMembers() >= $circle_upper_limit) {
            throw new Exception("サークルの上限人数に達しています");
        }

        // ...略
    }
}

// この実装ではドメインのルールがサービスに記述して、ドメインの重要なルールがサービスに散在してしまう
// 次にCircleにルールを記述することを考える

class Circle
{
    // プレミアムユーザーの人数を探したいが保持しているのはUserIdのコレクションだけだとする(phpは型がないので仮定)
    public array $members;

    // ...略

    // ユーザーのリポジトリを受け取る？
    public function isFull(IUserRepository $user_repository): bool
    {
        $users = $user_repository->find($this->members);
        $premium_user_number = count(array_filter($users, function ($user) {
            return $user->isPremium();
        }));
        $circle_upper_limit = $premium_user_number < 10 ? 30 : 50;
        return $this->countMembers() >= $circle_upper_limit;
    }
}

// リポジトリはドメイン由来のものではなく、Circleがドメインモデルの表現に徹していないため良くない実装
// これを「仕様」により解決する

class CircleFullSpecification
{
    public function __construct(private readonly IUserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function isSatisfiedBy(Circle $circle): bool
    {
        $users = $this->user_repository->find($circle->getMembers());
        $premium_user_number = count(array_filter($users, function ($user) {
            return $user->isPremium();
        }));
        $circle_upper_limit = $premium_user_number < 10 ? 30 : 50;
        return $circle->countMembers() >= $circle_upper_limit;
    }
}

// この仕様オブジェクトを利用したときのアプリケーションサービスは以下のようになる

class CircleApplicationService
{
    private readonly ICircleRepository $circle_repository;
    private readonly IUserRepository $user_repository;

    // ...略

    public function join(CircleJoinCommand $command): void
    {
        $circle_id = new CircleId($command->getCircleId());
        $circle = $this->circle_repository->findById($circle_id);

        $circle_full_specification = new CircleFullSpecification($this->user_repository);
        if ($circle_full_specification->isSatisfiedBy($circle)) {
            throw new Exception("サークルの上限人数に達しています");
        }

        // ...略
    }
}

// このように仕様オブジェクトを用意することで、複雑な評価手順はカプセル化され、コードの意図が明確になる

// おすすめサークルの検索機能
interface ICircleRepository
{
    // ...略
    public function findRecommended(DateTime $now): array;
}

// おすすめサークルを探すサービスの処理
class CircleApplicationService
{
    private readonly DateTime $now;

    // ...略

    public function getRecommend(CircleGetRecommendRequest $request): CircleGetRecommendResult
    {
        // リポジトリに依頼するだけ
        $recommend_circles = $this->circle_repository->findRecommended($this->now);

        return new CircleGetRecommendResult($recommend_circles);
    }
}

// この実装でも正しく動作するが、ドメインの重要なルール(おすすめサークル検索)がインフラストラクチャの領域に染み出している
// ドメインの重要な知識はドメインのオブジェクトとして表現すべきなので
// おすすめサークルかどうかを判断する処理はオブジェクトの評価で仕様として定義できる

class CircleRecommendSpecification
{
    public function __construct(private readonly DateTime $executeDatetime)
    {
        $this->executeDatetime = $executeDatetime;
    }

    public function isSatisfiedBy(Circle $circle): bool
    {
        if ($circle->countMembers() < 10) return false;
        return $circle->created() > $this->executeDatetime->modify("-1 month");
    }
}

class CircleApplicationService
{
    private readonly ICircleRepository $circle_repository;
    private readonly DateTime $now;

    // ...略

    public function getRecommend(CircleGetRecommendRequest $request): CircleGetRecommendResult
    {
        $recommend_circle_spec = new CircleRecommendSpecification($this->now);

        $circles = $this->circle_repository->findAll();
        $recommend_circles = $circles->where($recommend_circle_spec->isSatisfiedBy())->take(10)->toArray();

        return new CircleGetRecommendResult($recommend_circles);
    }
}

// サークル一覧を取得する処理
class CircleApplicationService
{
    public function getSummaries(CircleGetSummariesCommand $command): CircleGetSummariesResult
    {
        $all = $this->circle_repository->findAll();
        $circles = $all->skip(($command->getPage() - 1) * $command->getSize())->take($command->getSize());

        $summaries = [];
        foreach ($circles as $circle) {
            // サークルのオーナーを改めて検索
            $owner = $this->user_repository->find($circle->getOwnerId());
            $summaries[] = new CircleSummaryData($circle->getId(), $owner->getName());
        }

        return new CircleGetSummariesResult($summaries);
    }
}

// ↑は全件取得やループ内でfindしたり最適化には問題がある
// 最適化のために直接クエリを実行する
class CircleQueryService
{
    // ...略
    public function getSummaries(CircleGetSummariesCommand $command): CircleGetsummariesResult
    {
        // ここで直接SQLを書いてクエリを実行...
        $summaries = [];

        return new CircleGetSummariesResult($summaries);
    }
}
