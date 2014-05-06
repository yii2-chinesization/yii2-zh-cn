模型
=====

> 注意：该章节还在开发中。

Yii 遵循 MVC 结构，在 Yii 中模型的作用是存储或表示应用暂存的数据。 Yii 模型有以下基本特性：

- 特性定义：模型定义了什么看作特性。
- 特性标签：出于显示目的每个特性可能和一个标签关联。
- 批量填充特性：一次填充多个模型特性的能力。
- 基于场景的数据校验。

Yii 的模型继承自[[yii\base\Model]]类。模型通常用来保持数据何定义数据的验证规则（又称为业务逻辑）。业务逻辑通过提供验证和错误报告极大地简化了
从复杂 web 表单到生成模型的过程。

模型类也是更多多功能高级模型的基类，如[活动记录](active-record.md)。

特性
----------

实际上模型代表的数据是存储在模型的 *特性* 中。模型特性可以像对象的变量那样访问。如， `Post` 模型包括 `title` 和 `content` 特性，如下访问：

```php
$post = new Post();
$post->title = 'Hello, world';
$post->content = 'Something interesting is happening.';
echo $post->title;
echo $post->content;
```

既然[[yii\base\Model|Model]]实现了[ArrayAccess](http://php.net/manual/en/class.arrayaccess.php)接口，也可以当作数组元素来访问：

```php
$post = new Post();
$post['title'] = 'Hello, world';
$post['content'] = 'Something interesting is happening';
echo $post['title'];
echo $post['content'];
```

默认情况下，[[yii\base\Model|Model]]要求特性声明为 *公开的* 和 *非静态的* 类成员变量。下例中， `LoginForm` 模型类声明了两个特性：`username` 和 `password`。

```php
// LoginForm 有两个特性: username 和 password
class LoginForm extends \yii\base\Model
{
    public $username;
    public $password;
}
```

模型子类可通过覆写[[yii\base\Model::attributes()|attributes()]]方法以不同方式定义特性。如，[[yii\db\ActiveRecord]]使用类关联的数据表列名来定义特性。

特性标签
----------------

特性标签主要用于显示，如，对给定的特性 `firstName` ，可定义一个 `First Name` 标签，当在表单标签或错误提示等地方显示给终端用户时更人性化。给定一个特性名就可通过调用[[yii\base\Model::getAttributeLabel()]]获取它的标签。

覆写[[yii\base\Model::attributeLabels()]]方法可自定义特性标签。该方法返回特性名到特性标签的映射表，如下例所示。如果特性不在该映射表，它的标签将使用[[yii\base\Model::generateAttributeLabel()]]方法生成。很多情况[[yii\base\Model::generateAttributeLabel()]]都能生成合适的标签（如 `username` 特性生成 `Username` 标签，`orderNumber` 特性生成 `Order Number` 标签）。

```php
// LoginForm 有两个特性: username and password
class LoginForm extends \yii\base\Model
{
    public $username;
    public $password;

    public function attributeLabels()
    {
        return [
            'username' => 'Your name',
            'password' => 'Your password',
        ];
    }
}
```

场景
---------

模型可用于不同 *场景* 。如， `User` 模型可用于收集用户登录输入数据，也可以用于用户注册。在注册场景中，每项数据都是必须的，而登录场景中，只有用户名和密码是必须的。

为简化实现不同场景的业务逻辑，每个模型都有一个 `scenario` 属性，储存了模型正被使用的场景。如将在以下部分所介绍的那样，场景这个概念主要用于数据验证和批量特性赋值。

每个场景关联了一系列在特定场景 *活动的* 特性。如，在 `login` （登录）场景，只有 `username` 和 `password` 特性是活跃的；而在`register` （注册）场景，除了用户名和密码外，其他特性如 `email` 等也是 *活动的* 。当特性是 *活动的* 就意味着这个特性要接受验证。

可能需要的场景列入 `scenarios()` 方法，该方法返回一个数组，该数组键为场景名，值为在该场景中活动的特性列表：

```php
class User extends \yii\db\ActiveRecord
{
    public function scenarios()
    {
        return [
            'login' => ['username', 'password'],
            'register' => ['username', 'email', 'password'],
        ];
    }
}
```

如 `scenarios` 方法未定义，默认场景启用。即有验证规则的特性看作是 *活动的* 。

如希望自定义场景生效后还保持默认场景生效，引用父类方法：

```php
class User extends \yii\db\ActiveRecord
{
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['login'] = ['username', 'password'];
        $scenarios['register'] = ['username', 'email', 'password'];
        return $scenarios;
    }
}
```


有时批量赋值时需要标记某个特性是不安全的（但仍想验证该特性），可以在 `scenarios()` 方法定义该特性时给特性名添加感叹号前缀来标记。如：

```php
['username', 'password', '!secret']
```

该例中 `username`, `password` 和 `secret` are *活动的* 特性，但只有 `username` 和 `password` 在批量赋值时认为是安全的。

识别活动的模型场景可使用以下方法之一：

```php
class EmployeeController extends \yii\web\Controller
{
    public function actionCreate($id = null)
    {
        // 第一种方法
        $employee = new Employee(['scenario' => 'managementPanel']);

        // 第二种方法
        $employee = new Employee();
        $employee->scenario = 'managementPanel';

        // 第三种方法
        $employee = Employee::find()->where('id = :id', [':id' => $id])->one();
        if ($employee !== null) {
            $employee->scenario = 'managementPanel';
        }
    }
}
```

上例假定模型基于[Active Record](active-record.md)。而基础的表单模型很少需要场景，因为基础表单通常直接连接到一个简单的表单，另一个原因如上所示， `scenarios()` 默认情况是返回带有活动验证规则的每一个属性，这些活动的验证规则使属性非常适用于批量赋值和验证。

验证
----------

当模型用于以特性收集用户输入数据时，通常需要验证受影响的特性以确保这些特性满足特定要求，如特性不能为空，必须只包含字母等。如验证发现错误，就会显示出来提示用户改正。以下示例演示了验证如何履行：

```php
$model = new LoginForm();
$model->username = $_POST['username'];
$model->password = $_POST['password'];
if ($model->validate()) {
    // ... 用户登录 ...
} else {
    $errors = $model->getErrors();
    // ... 显示错误信息给用户 ...
}
```

模型可用的验证规则列于 `rules()` 方法。一条验证规则适用于一个或多个特性并作用于一个或多个场景。一条规则可使用验证器对象——[[yii\validators\Validator]]子类实例或以下格式的数组指定：

```php
[
    ['特性1', '特性2', ...],
    '验证器类或别名',
    // 指定规则适用的场景
    // 未指定场景则适用于所有场景
    'on' => ['场景1', '场景2', ...],
    // 以下键值对将用于初始化验证器属性
    'property1' => 'value1',
    'property2' => 'value2',
    // ...
]
```

调用 `validate()` 时，真正执行的验证规则取决于以下两个准则：

- 规则必须关联至少一个活动的特性；
- 规则必须在当前场景是活动的。


### 建立你自己的验证器 (内联验证方法)

如果内置验证器不能满足你的需求，可以通过在模型类创建一个方法来建立你自己的验证器。这个方法可由[[yii\validators\InlineValidator|InlineValidator]]包裹并在验证时调用，然后用于验证特性并在验证失败时以[[yii\base\Model::addError()|add errors]]添加错误到模型。

自定义验证方法可以 `public function myValidator($attribute, $params)` 来识别，方法名可自由选择。

以下示例实现了一个用于验证用户年龄的验证器：

```php
public function validateAge($attribute, $params)
{
    $value = $this->$attribute;
    if (strtotime($value) > strtotime('now - ' . $params['min'] . ' years')) {
        $this->addError($attribute, 'You must be at least ' . $params['min'] . ' years old to register for this service.');
    }
}

public function rules()
{
    return [
        // ...
        [['birthdate'], 'validateAge', 'params' => ['min' => '12']],
    ];
}
```

也可在规则定义中设置[[yii\validators\InlineValidator|InlineValidator]]的其他属性。以[[yii\validators\InlineValidator::$skipOnEmpty|skipOnEmpty]]属性为例：

```php
[['birthdate'], 'validateAge', 'params' => ['min' => '12'], 'skipOnEmpty' => false],
```

### 条件验证

当某条件应用时才验证特性，如一个字段的验证依赖另一个字段的值，可以使用[[yii\validators\Validator::when|the `when` property]]来定义这个条件：

```php
['state', 'required', 'when' => function($model) { return $model->country == Country::USA; }],
['stateOthers', 'required', 'when' => function($model) { return $model->country != Country::USA; }],
['mother', 'required', 'when' => function($model) { return $model->age < 18 && $model->married != true; }],
```

如下这样写条件更易读：

```php
public function rules()
{
    $usa = function($model) { return $model->country == Country::USA; };
    $notUsa = function($model) { return $model->country != Country::USA; };
    $child = function($model) { return $model->age < 18 && $model->married != true; };
    return [
        ['state', 'required', 'when' => $usa],
        ['stateOthers', 'required', 'when' => $notUsa], // 注意不是 !$usa
        ['mother', 'required', 'when' => $child],
    ];
}
```


批量特性检索和赋值
---------------------

特性可以通过 `attributes` 属性批量检索。以下代码返回了 *所有*  `$post` 模型的键值对数组形式的特性。

```php
$post = Post::find(42);
if ($post) {
    $attributes = $post->attributes;
    var_dump($attributes);
}
```

使用 `attributes` 属性还可以从关联数组批量赋值到模型特性：

```php
$post = new Post();
$attributes = [
    'title' => 'Massive assignment example',
    'content' => 'Never allow assigning attributes that are not meant to be assigned.',
];
$post->attributes = $attributes;
var_dump($attributes);
```

以上代码赋值到相应的模型特性，特性名作为数组的键。和对所有特性总是有效的批量检索的关键区别是赋值的特性必须是 **安全的**，否则会被忽略。

验证规则和批量赋值
---------------------

Yii 2 的验证规则是和批量赋值分离的，这和 1.x 是不一样的。验证规则描述在模型的`rules()` 方法，而什么是安全的批量赋值描述在 `scenarios` 方法：

```php
class User extends ActiveRecord
{
    public function rules()
    {
        return [
            // 当相应的字段是“安全的”，规则启用
            ['username', 'string', 'length' => [4, 32]],
            ['first_name', 'string', 'max' => 128],
            ['password', 'required'],

            // 当场景是“注册”，无论字段是否“安全的”，规则启用
            ['hashcode', 'check', 'on' => 'signup'],
        ];
    }

    public function scenarios()
    {
        return [
            // 注册场景允许 username 的批量赋值
            'signup' => ['username', 'password'],
            'update' => ['username', 'first_name'],
        ];
    }
}
```

以上代码在严格遵守 `scenarios()` 后才允许批量赋值：

```php
$user = User::find(42);
$data = ['password' => '123'];
$user->attributes = $data;
print_r($user->attributes);
```

以上将返回空数组，因为在 `scenarios()`未定义默认场景。

```php
$user = User::find(42);
$user->scenario = 'signup';
$data = [
    'username' => 'samdark',
    'password' => '123',
    'hashcode' => 'test',
];
$user->attributes = $data;
print_r($user->attributes);
```

以上代码将返回下面结果：

```php
array(
    'username' => 'samdark',
    'first_name' => null,
    'password' => '123',
    'hashcode' => null, // 该特性未在场景方法中定义
)
```

防止未定义 `scenarios` 方法的措施：

```php
class User extends ActiveRecord
{
    public function rules()
    {
        return [
            ['username', 'string', 'length' => [4, 32]],
            ['first_name', 'string', 'max' => 128],
            ['password', 'required'],
        ];
    }
}
```

以上代码假设了默认场景所以批量赋值将对所有定义过 `rules` 的字段生效：

```php
$user = User::find(42);
$data = [
    'username' => 'samdark',
    'first_name' => 'Alexander',
    'last_name' => 'Makarov',
    'password' => '123',
];
$user->attributes = $data;
print_r($user->attributes);
```

以上代码将返回：

```php
array(
    'username' => 'samdark',
    'first_name' => 'Alexander',
    'password' => '123',
)
```

如果希望对默认场景设置一些字段是不安全的：

```php
class User extends ActiveRecord
{
    function rules()
    {
        return [
            ['username', 'string', 'length' => [4, 32]],
            ['first_name', 'string', 'max' => 128],
            ['password', 'required'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['username', 'first_name', '!password']
        ];
    }
}
```

批量赋值默认仍可用：

```php
$user = User::find(42);
$data = [
    'username' => 'samdark',
    'first_name' => 'Alexander',
    'password' => '123',
];
$user->attributes = $data;
print_r($user->attributes);
```

以上代码输出：

```php
array(
    'username' => 'samdark',
    'first_name' => 'Alexander',
    'password' => null, // 因为场景中该字段名前面有 !
)
```

更多内容请参考
--------

- [模型验证](validation.md)
- [[yii\base\Model]]
