授权
=============

授权是验证用户是否有足够权限做一些事情的过程。（译者注：Authentication就是验证是否注册，Authorization是检查已登录用户是否有权限）
Yii 提供了两种方法来管理授权：

**访问控制过滤器（Access Control Filter，简称 ACF）**和 **基于角色的访问控制（Role-Based Access Control，简称 RBAC）**。


访问控制过滤器
---------------------

访问控制过滤器（Access Control Filter，简称 ACF）是一种简单的授权验证方法，
最适用于那些只需要一些基本访问控制的应用。正如其名，ACF 是一个动作过滤器，
可以定义为一个 **行为（Behavior）**被附加到一个控制器或者模块上。
ACF 会检查一系列的 [[yii\filters\AccessControl::rules|access rules]]，来确认当前用户是否有权限访问当前被请求的动作。

下面这段代码展示类如何使用 ACF，通过 [[yii\filters\AccessControl]] 组件实现：

```php
use yii\filters\AccessControl;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['login', 'logout', 'signup'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    // ...
}
```

在上面的代码中，ACF 被定义为一个行为，并附加到了 `site` 控制器上。这是一种典型的使用动作过滤器的方法。

`only` 选项指定了当前 ACF 只应被应用在 `login`、`logout` 和 `signup` 这三个动作上。
`rules`选项指定了 [[yii\filters\AccessRule|access rules]]（访问规则），可以解读成以下自然语言：

- 允许所有游客（未登陆的用户）访问 'login'（登陆）和 'signup'（注册）两个动作。`roles` 选项包含一个问号 `?`
 ，问号是一个代指“游客”的指示符。
- 允许所有已登录用户访问 'logout'（登出）动作。`@` 符号是另外一个特殊指示符，他的意思是所有已登录用户。

当 ACF 允许授权检测时，他会自顶向下逐条检查各项访问规则，直到它发现与用户身份相吻合的那个条目。
紧接着它会判断，相吻合规则的 `allow` 值，以判断用户是否有权限。
如果没有任何一条规则符合用户的身份的话，则意味着该用户 *没有*访问的资格，ACF 会终止动作的继续执行。

缺省状态下，ACF 在判定用户无权访问当前动作时，只会执行以下操作：

* 若用户是游客，他会调用 [[yii\web\User::loginRequired()]] 方法，使浏览器跳转到登陆页面。
* 若用户已登录，他会抛出一个 [[yii\web\ForbiddenHttpException]]（禁用HTTP异常）。

你可以通过配置 [[yii\filters\AccessControl::denyCallback]] 属性，来自定义这种行为：

```php
[
    'class' => AccessControl::className(),
    'denyCallback' => function ($rule, $action) {
        throw new \Exception('您无权访问该页面');
    }
]
```

[[yii\filters\AccessRule|Access rules]] 支持很多选项。以下是受支持选项的总结。
你也可以扩展 [[yii\filters\AccessRule]] 类来创建一个您自定义的访问控制规则类。

 * [[yii\filters\AccessRule::allow|allow]]：指定了这是一个“准许”还是“拒绝”访问的规则。

 * [[yii\filters\AccessRule::actions|actions]]：指定了哪些动作受该规则影响。对应值应该是一个存储这些动作 ID 的数组。
 这种比对是大小写敏感的。如果该选项为空或未设置，则意味着规则适用于全体动作。（译者注：也就是不过滤或全过滤，取决于 `allow` 的值）

 * [[yii\filters\AccessRule::controllers|controllers]]：指定哪些控制器受此规则影响。对应值应该是存储控制器 ID 的数组。
 同样比对是大小写敏感的。若为空或未设置则应用于全体控制器。

 * [[yii\filters\AccessRule::roles|roles]]：指定了该规则适用于哪些用户组（角色）。
 有两种通过 [[yii\web\User::isGuest]] 检查的特殊用户组标识符：
     - `?`：对应游客用户（还未登录验证的用户）
     - `@`：对应已登录用户
使用其他用户角色（组）需要 RBAC 的支持（会在下个板块详述），且会调用 [[yii\web\User::can()]] 方法。
若该选项为空或未设置，则意味着该选项应用于所有用户角色（组）。

 * [[yii\filters\AccessRule::ips|ips]]：指定该规则匹配哪些 [[yii\web\Request::Request::userIP|client IP addresses]] （客户端 IP 地址）
 一个 IP地址可以在结尾处包含一个通配符 `*` ，这样它可以匹配所有有相同前缀的 IP 地址。
 比如说，'192.168.*' 匹配所有前缀为 '192.168.' 的 IP 地址。
 若该选项为空或未设置，则该规则匹配所有 IP 地址。

 * [[yii\filters\AccessRule::verbs|verbs]]：指定该规则匹配哪些 request 请求方法（`GET`、`POST` 之类的）
同样比对是大小写敏感的。

 * [[yii\filters\AccessRule::matchCallback|matchCallback]]：指定当规则符合时调用哪个 PHP callable 对象
 （可调用接口的对象，也就是函数对象，各种各样的函数对象）。

 * [[yii\filters\AccessRule::denyCallback|denyCallback]]: 指定当规则需要拒绝相关访问时，应该调用哪个 PHP callable 对象。

下面是一个例子，叫你如何使用 `matchCallback` 选项，这将允许你写出非常随意的访问检测逻辑：

```php
use yii\filters\AccessControl;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['special-callback'],
                'rules' => [
                    [
                        'actions' => ['special-callback'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return date('d-m') === '11-11';
                        }
                    ],
                ],
            ],
        ];
    }

    // 匹配的回调函数被调用！当前页只能每个11月11号才能访问
    public function actionSpecialCallback()
    {
        return $this->render('happy-singles-day');
    }
}
```


基于角色的访问控制（RBAC）
--------------------------------

基于角色的访问控制（Role-Based Access Control，简称 RBAC）
提供一个简单且强大的集中式访问控制。请参考这篇[维基百科文章](http://en.wikipedia.org/wiki/Role-based_access_control)
来了解 RBAC 相较于其他一些更传统的访问控制策略有何具体的不同之处。
（英文，抱歉没有找到合适的中文资料，wiki本身有一篇简化版的[中文释义](https://zh.wikipedia.org/zh-cn/%E4%BB%A5%E8%A7%92%E8%89%B2%E7%82%BA%E5%9F%BA%E7%A4%8E%E7%9A%84%E5%AD%98%E5%8F%96%E6%8E%A7%E5%88%B6)
，略现学术，请姑且一看。国内百毒百科等，不是胡扯就是资料太老）

Yii 实现了一个通用的层级 RBAC，依据的是[美国国家标准局做的 NIST RBAC 模型](http://csrc.nist.gov/rbac/sandhu-ferraiolo-kuhn-00.pdf)
（英文，且无翻译，只找到[另一篇中文的翻译文章](http://williamou.iteye.com/blog/248660)）。
它通过 [[yii\rbac\ManagerInterface|authManager]] 应用组件，提供 RBAC 系统的基本功能。

使用 RBAC 主要涉及两部分工作。第一部分是构建 RBAC 授权所用数据。
另一部分是用这些授权数据，在需要的地方执行访问许可检查。
Using RBAC involves two parts of work. The first part is to build up the RBAC authorization data, and the second
part is to use the authorization data to perform access check in places where it is needed.

为了帮助我们更好地描述接下来的内容，我们会首先介绍一些基本的 RBAC 概念。


### 基本概念

一个角色对应一个包含许多具体*权限*（例如：查看报告，创建报告等等）的集合。一个角色可以被指定给一个或若干个用户。
为了检查用户是否拥有特定的权限，我们可以检查该用户是否被分配有一个包含该权限的角色。
A role represents a collection of *permissions* (e.g. viewing reports, creating reports). A role may be assigned
to one or multiple users. To check if a user has a specified permission, we may check if the user is assigned
with a role that contains that permission.

每个角色或权限可能与一个*规则*关关联。一个规则即是一段代码，它将在访问过程中执行，用来检查当前用户相应的角色或权限。例如，“更新报告”的权限，可能有一个规则用来检查当前用户是不是报告的创建者。在访问检查过程中，如果用户不是报告的创建者，他/她会被认为没有“更新报告”权限。
Associated with each role or permission, there may be a *rule*. A rule represents a piece of code that will be
executed during access check to determine if the corresponding role or permission applies to the current user.
For example, the "update report" permission may have a rule that checks if the current user is the report creator.
During access checking, if the user is NOT the report creator, he/she will be considered not having the "update report" permission.

Both roles and permissions can be organized in a hierarchy. In particular, a role may consist of other roles or permissions;
and a permission may consist of other permissions. Yii implements a *partial order* hierarchy which includes the
more special *tree* hierarchy. While a role can contain a permission, it is not true vice versa.


### 配置 RBAC 管理器

在我们准备定义授权数据和执行访问检查前，我们需要配置 [[yii\base\Application::authManager|authManager]] 应用组件。 Yii提供两种类型的授权管理器：[[yii\rbac\PhpManager]] 和 [[yii\rbac\DbManager]]。前者使用一个PHP脚本文件来存储授权数据，后者在数据库中存储授权数据。如果你的应用程序并不需要非常动态的角色和权限管理，你可以考虑使用前者。
Before we set off to define authorization data and perform access checking, we need to configure the
[[yii\base\Application::authManager|authManager]] application component. Yii provides two types of authorization managers: 
[[yii\rbac\PhpManager]] and [[yii\rbac\DbManager]]. The former uses a PHP script file to store authorization
data, while the latter stores authorization data in database. You may consider using the former if your application
does not require very dynamic role and permission management.

下面的代码显示了在应用程序配置中如何配置`authManager`：
The following code shows how to configure `authManager` in the application configuration:

```php
return [
    // ...
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
        ],
        // ...
    ],
];
```

`authManager` 现在可以通过 `\Yii::$app->authManager` 来访问。
The `authManager` can now be accessed via `\Yii::$app->authManager`.


### 构建授权数据（Authorization Data）

Building authorization data is all about the following tasks:

- defining roles and permissions;
- establishing relations among roles and permissions;
- defining rules;
- associating rules with roles and permissions;
- assigning roles to users.

Depending on authorization flexibility requirements the tasks above could be done in different ways.

If your persmissions hierarchy doesn't change at all and you have a fixed number of users you can create a console
command that will initialize authorization data once via APIs offered by `authManager`:

```php
<?php
namespace app\commands;

use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // add "createPost" permission
        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'create a post';
        $auth->add($createPost);

        // add "readPost" permission
        $readPost = $auth->createPermission('readPost');
        $readPost->description = 'read a post';
        $auth->add($readPost);

        // add "updatePost" permission
        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'update post';
        $auth->add($updatePost);

        // add "reader" role and give this role the "readPost" permission
        $reader = $auth->createRole('reader');
        $auth->add($reader);
        $auth->addChild($reader, $readPost);

        // add "author" role and give this role the "createPost" permission
        // as well as the permissions of the "reader" role
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createPost);
        $auth->addChild($author, $reader);

        // add "admin" role and give this role the "updatePost" permission
        // as well as the permissions of the "author" role
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $author);

        // Assign roles to users. 10, 14 and 26 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        $auth->assign($reader, 10);
        $auth->assign($author, 14);
        $auth->assign($admin, 26);
    }
}
```

If your application allows user signup you need to assign roles to these new users once. For example, in order for all
signed up users to become authors you in advanced application template you need to modify `common\models\User::create()`
as follows:

```php
public static function create($attributes)
{
    /** @var User $user */
    $user = new static();
    $user->setAttributes($attributes);
    $user->setPassword($attributes['password']);
    $user->generateAuthKey();
    if ($user->save()) {

        // the following three lines were added:
        $auth = Yii::$app->authManager;
        $adminRole = $auth->getRole('author');
        $auth->assign($adminRole, $user->getId());

        return $user;
    } else {
        return null;
    }
}
```

For applications that require complex access control with dynamically updated authorization data, special user interfaces
(i.e. admin panel) may need to be developed using APIs offered by `authManager`.


> Tip: By default, [[yii\rbac\PhpManager]] stores RBAC data in the file `@app/data/rbac.php`.
  Sometimes when you want to make some minor changes to the RBAC data, you may directly edit this file.


### 使用规则（Rules）

As aforementioned, rules add additional constraint to roles and permissions. A rule is a class extending
from [[yii\rbac\Rule]]. It must implement the [[yii\rbac\Rule::execute()|execute()]] method. Below is
an example:

```php
namespace app\rbac;

use yii\rbac\Rule;

/**
 * Checks if authorID matches user passed via params
 */
class AuthorRule extends Rule
{
    public $name = 'isAuthor';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['post']) ? $params['post']->createdBy == $user : false;
    }
}
```

The rule above checks if the `post` is created by `$user`. It can be used in the following
to create a special permission `updateOwnPost`:

```php
// add the rule
$rule = new \app\rbac\AuthorRule;
$auth->add($rule);

// add the "updateOwnPost" permission and associate the rule with it.
$updateOwnPost = $this->auth->createPermission('updateOwnPost');
$updateOwnPost->description = 'update own post';
$updateOwnPost->ruleName = $rule->name;
$auth->add($updateOwnPost);

// allow "author" to update their own posts
$auth->addChild($author, $updateOwnPost);
```


### 访问检查（Access Check）

With the authorization data ready, access check is as simple as a call to the [[yii\rbac\ManagerInterface::checkAccess()]]
method. Because most access check is about the current user, for convenience Yii provides a shortcut method
[[yii\web\User::can()]], which can be used like the following:

```php
if (\Yii::$app->user->can('createPost')) {
    // create post
}
```

To check the `updateOwnPost` permission, an extra parameter is required by the `AuthorRule` described before.

```php
if (\Yii::$app->user->can('updateOwnPost', ['post' => $post])) {
    // update post
}
```


### 使用默认角色（Roles）

A default role is a role that is *implicitly* assigned to *all* users. The call to [[yii\rbac\ManagerInterface::assign()]]
is not needed, and the authorization data does not contain its assignment information.

A default role is usually associated with a rule which determines if the role applies to the user being checked.

Default roles are often used in applications which already have some sort of role assignment. For example, an application
may have a "group" column in its user table to represent which privilege group each user belongs to.
If each privilege group can be mapped to a RBAC role, you can use the default role feature to automatically
assign each user to a RBAC role. Let's use an example to show how this can be done.

Assume in the user table, you have a `group` column which uses 1 to represent the administrator group and 2 the author group.
You plan to have two RBAC roles `admin` and `author` to represent the permissions for these two groups, respectively.
You can create set up the RBAC data as follows,


```php
namespace app\rbac;

use yii\rbac\Rule;

/**
 * Checks if authorID matches user passed via params
 */
class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    public function execute($user, $item, $params)
    {
        $group = \Yii::$app->user->identity->group;
        if ($item->name === 'admin') {
            return $group == 1;
        } elseif ($item->name === 'author') {
            return $group == 1 || $group == 2;
        } else {
            return false;
        }
    }
}

$rule = new \app\rbac\UserGroupRule;
$auth->add($rule);

$author = $auth->createRole('author');
$author->ruleName = $rule->name;
$auth->add($author);
// ... add permissions as children of $author ...

$admin = $auth->createRole('admin');
$admin->ruleName = $rule->name;
$auth->add($admin);
$auth->addChild($admin, $author);
// ... add permissions as children of $admin ...
```

Note that in the above, because "author" is added as a child of "admin", when you implement the `execute()` method
of the rule class, you need to respect this hierarchy as well. That is why when the role name is "author",
the `execute()` method will return true if the user group is either 1 or 2 (meaning the user is in either "admin"
group or "author" group).

Next, configure `authManager` by listing the two roles in [[yii\rbac\ManagerInterface::defaultRoles]]:

```php
return [
    // ...
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['admin', 'author'],
        ],
        // ...
    ],
];
```

Now if you perform an access check, both of the `admin` and `author` roles will be checked by evaluating
the rules associated with them. If the rule returns true, it means the role applies to the current user.
Based on the above rule implementation, this means if the `group` value of a user is 1, the `admin` role
would apply to the user; and if the `group` value is 2, the `author` role would apply.
