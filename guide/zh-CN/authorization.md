授权
=============

授权是验证用户是否有足够权限做一些事情的过程。Yii 提供了一些方法来管理授权。

访问控制基础
---------------------

基本的访问控制用[[yii\filters\AccessControl]]实现是非常简单的：

```php
class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['login', 'logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    // ...
```

以上代码附加了一个访问控制行为给控制器。既然 `only` 选项被指定，该行为将只应用到 'login', 'logout' 和 'signup' 这三个动作。一系列规则是[[yii\filters\AccessRule]]的基础选项，读取说明如下：

- 允许所有游客（未认证）用户访问 'login' 和 'signup' 动作。
- 允许认证用户访问 'logout' 动作。

规则从上到下依次核对，如果规则匹配，动作立即发生。否则将核对下一个规则。如果没有任何规则匹配，访问被拒绝。

[[yii\filters\AccessRule]]非常灵活，又允许被验证者核对 IP 和请求方法（如 POST, GET）。如果还不够，可以通过匿名函数指定你自己的核对项：

```php
class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['special-callback'],
                'rules' => [
                    [
                        'actions' => ['special-callback'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return date('d-m') === '31-10';
                        }
                    ],
```

动作：

```php
    // ...
    // 匹配的回调函数被调用！该页面只能在每年10月31日访问。
    public function actionSpecialCallback()
    {
        return $this->render('happy-halloween');
    }
```

Sometimes you want a custom action to be taken when access is denied. In this case you can specify `denyCallback`.
有时想要在访问被拒绝时执行一个自定义动作。这种情况可以指定 `denyCallback` 。

基于角色的访问控制 (RBAC)
--------------------------------

基于角色的访问控制是控制访问非常灵活的方法，非常完美地匹配可定制许可的复杂系统。

### 为 RBAC 使用基于文件的配置

要开始使用 RBAC ，一些额外的步骤是必须的。首先需要在应用配置文件（根据你所使用的模板分别是`web.php` 或 `main.php` ）配置`authManager` 应用组件：

```php
'authManager' => [
    'class' => 'app\components\PhpManager',
    'defaultRoles' => ['guest'],
],
```

通常用户角色存储的数据表和其他用户数据相同。这种情况可以通过创建我们自己的组件（`app/components/PhpManager.php`）定义：

```php
<?php
namespace app\components;

use Yii;

class PhpManager extends \yii\rbac\PhpManager
{
    public function init()
    {
        parent::init();
        if (!Yii::$app->user->isGuest) {
            // 我们假设用户角色存储在 identity
            $this->assign(Yii::$app->user->identity->id, Yii::$app->user->identity->role);
        }
    }
}
```

现在创建自定义的角色类：

```php
namespace app\rbac;

use yii\rbac\Rule;
use Yii;

class NotGuestRule extends Rule
{
    public $name = 'notGuestRule';

    public function execute($params, $data)
    {
        return !Yii::$app->user->isGuest;
    }
}
```

然后在 `@app/data/rbac.php`创建许可层级：

```php
<?php
use yii\rbac\Item;
use app\rbac\NotGuestRule;

$notGuest = new NotGuestRule();

return [
    'rules' => [
        $notGuest->name => serialize($notGuest),
    ],
    'items' => [
        // 这里是管理任务
        'manageThing0' => ['type' => Item::TYPE_OPERATION, 'description' => '...', 'ruleName' => null, 'data' => null],
        'manageThing1' => ['type' => Item::TYPE_OPERATION, 'description' => '...', 'ruleName' => null, 'data' => null],
        'manageThing2' => ['type' => Item::TYPE_OPERATION, 'description' => '...', 'ruleName' => null, 'data' => null],
        'manageThing3' => ['type' => Item::TYPE_OPERATION, 'description' => '...', 'ruleName' => null, 'data' => null],

        // 这里是角色
        'guest' => [
            'type' => Item::TYPE_ROLE,
            'description' => 'Guest',
            'ruleName' => null,
            'data' => null
        ],

        'user' => [
            'type' => Item::TYPE_ROLE,
            'description' => 'User',
            'children' => [
                'guest',
                'manageThing0', // 用户可以编辑 thing0
            ],
            'ruleName' => $notGuest->name,
            'data' => null
        ],

        'moderator' => [
            'type' => Item::TYPE_ROLE,
            'description' => 'Moderator',
            'children' => [
                'user',         // 用户可以做的任何事，该角色也可以
                'manageThing1', // 和 thing1
            ],
            'ruleName' => null,
            'data' => null
        ],

        'admin' => [
            'type' => Item::TYPE_ROLE,
            'description' => 'Admin',
            'children' => [
                'moderator',    // 可以做 moderator 能做的任何事
                'manageThing2', // 和 thing2
            ],
            'ruleName' => null,
            'data' => null
        ],

        'godmode' => [
            'type' => Item::TYPE_ROLE,
            'description' => 'Super admin',
            'children' => [
                'admin',        // 能做 admin 能做的任何事
                'manageThing3', // 和 thing3
            ],
            'ruleName' => null,
            'data' => null
        ],
    ],
];
```

现在可以在控制器的访问控制配置指定 RBAC 角色：

```php
public function behaviors()
{
    return [
        'access' => [
            'class' => 'yii\filters\AccessControl',
            'except' => ['something'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['manageThing1'],
                ],
            ],
        ],
    ];
}
```

另一个方法是调用适当的[[yii\web\User::checkAccess()]]方法。

### 为 RBAC 使用基于数据库的存储

Storing RBAC hierarchy in database is less efficient performancewise but is much more flexible. It is easier to create
a good management UI for it so in case you need permissions structure that is managed by end user DB is your choice.
用数据库存储 RBAC 层级，性能略低但更加灵活。（翻译者未理解下一句的原文意思）为此创建友好的管理界面更容易，因此万需要被终端用户 DB 管理的许可结构就成了你的选择。

开始使用需要配置 `db` 组件的数据库连接。完成后[为自己的数据库获取 `schema-*.sql` 文件](https://github.com/yiisoft/yii2/tree/master/framework/rbac)并执行。

下一步是在应用配置文件(官方基础模板是 web.php,高级模板是 main.php)配置 `authManager` 应用组件：

```php
'authManager' => [
    'class' => 'yii\rbac\DbManager',
    'defaultRoles' => ['guest'],
],
```

TBD

### 如何工作

TBD: 用图片说明它如何工作 :)

### 避免太多 RBAC

为保持授权层级的简单和高效，应避免创建和使用太多的级数。更多情况下应使用简单的核对清单来替代。像这样的代码使用 RBAC ：

```php
public function editArticle($id)
{
  $article = Article::findOne($id);
  if (!$article) {
    throw new NotFoundHttpException;
  }
  if (!\Yii::$app->user->checkAccess('edit_article', ['article' => $article])) {
    throw new ForbiddenHttpException;
  }
  // ...
}
```

可以替换为不使用 RBAC 更简单的代码：

```php
public function editArticle($id)
{
    $article = Article::findOne(['id' => $id, 'author_id' => \Yii::$app->user->id]);
    if (!$article) {
      throw new NotFoundHttpException;
    }
    // ...
}
```
