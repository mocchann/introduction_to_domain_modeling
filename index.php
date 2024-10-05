<?php

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
