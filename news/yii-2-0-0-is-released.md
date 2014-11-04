> [原文：http://www.yiiframework.com/news/](http://www.yiiframework.com/news/81/yii-2-0-0-is-released/)  
主翻译：@qiansen1386 (东方孤思子) 校对：也是这货~ 时间：2014年10月13

> 特别提醒：文中 Yii 文档之外的外部链接绝大多数为原文链接，并未提供中文链接。如有需要可以自己找找大多数都有全世界的华人志愿者提供的汉化版本。如有特别需要，也可以给我们[提交翻译请求，不过最好是本身带 Markdown 格式的文件](https://github.com/yii2-chinesization/yii2-zh-cn/issues)。

Yii 2.0.0 终于发布了！
=================

经过三年多的密集开发，饱含了 [300 余位贡献者](https://github.com/yiisoft/yii2/graphs/contributors)的总计超过
[10,000 次提交](https://github.com/yiisoft/yii2/commits/master)的 Yii 2.0.0 终于来了！感谢各位的支持与耐心！

你们应该也都知道了，Yii 2.0 是前作 1.1 之后的完全重写。我们这么选择主要是为了构建出一个代表当下最先进水平的 PHP
框架，在保持 Yii 原本的简洁与可扩展性的基础上，又加入了最新的科技与最新的功能，从而使得它较之之前更胜一筹。今天我们非常荣幸地宣布，我们已经达到了我们的设计目标。

下面是一些关于 Yii 与 Yii 2.0 的链接：

-   [Yii 项目官网](http://www.yiiframework.com)
-   [Yii 2.0 GitHub 项目仓库](https://github.com/yiisoft/yii2)：你可以 star（星标）、watch （关注）它来跟踪了解 Yii 开发的最新动态。
-   [Yii Facebook 小组](https://www.facebook.com/groups/yiitalk/)
-   [Yii Twitter 微博](https://twitter.com/yiiframework)
-   [Yii 领英（LinkedIn）小组](https://www.linkedin.com/groups/yii-framework-1483367)

在下面我们会总结一下这个大家期待已久的发布的一些闪光点。你若你实在迫不及待，也可以直接参考 [指南-入门篇](http://www.docwithcn.com/guide-index.html#getting-started) 快速上手把玩。

闪光点
------

### 采用各项标准与最新科技

Yii 2.0 应用了 PHP 命名空间与 Trait（特质），[PSR
推荐开发标准](http://www.php-fig.org/psr/)，
[Composer 依赖管理工具](https://getcomposer.org/)，[Bower](http://bower.io/) 与
[NPM](https://www.npmjs.org/)前端依赖管理工具。所有这些改变都使得框架自身变得更清爽，与第三方类库之间更易协作。

### 可靠的基础类库

跟 1.1 时代一样，Yii 2.0 支持用 getter 和
setter，[配置数组](http://www.docwithcn.com/guide-concept-configurations.html)，
[事件（events）](http://www.docwithcn.com/guide-concept-events.html)以及
[行为（behaviors）](http://www.docwithcn.com/guide-concept-behaviors.html)来配置或改变[对象的属性（Object
Properties）](http://www.docwithcn.com/guide-concept-properties.html)。而新的实现则更加高效，且更具表现力。举例而言，你可以这样来响应事件：

```php
$response = new yii\web\Response;
$response->on('beforeSend', function ($event) {
    // 在此处响应 "beforeSend" 事件
});
```

Yii 2.0 实现了[依赖注入容器](http://www.docwithcn.com/guide-concept-di-container.html)和[服务定位器](http://www.docwithcn.com/guide-concept-service-locator.html)。这些功能让使用 Yii 构建的应用更加容易定制与测试。

### 开发工具

Yii 2.0 包含了一系列的开发工具，让程序猿的人生不再那么苦逼。

新的 [Yii debugger](http://www.docwithcn.com/guide-tool-debugger.html)
允许你检视你应用内部的运行状况。它也可以用于性能调教，来找出影响应用性能的瓶颈所在。

如 1.1，Yii 2.0 也提供 Gii，也就是[代码生成器](http://www.docwithcn.com/guide-tool-gii.html)，它可以帮你省去开发中的大块时间。Gii 非常易于扩展，允许你自定义或创建不一样的代码生成器。它还同时提供 Web 与命令行两种界面，以适应不同的用户偏好。

Yii 1.1 的 API 文档收到过很多的积极反馈。很多人反馈说他们也想为他们的应用提供类似的文档系统。Yii 2.0
实现了他们的愿望，带来了[文档生成器](https://github.com/yiisoft/yii2/tree/master/extensions/apidoc)扩展。该生成器支持
Markdown 语法，它可以让你以一种更为简单明了且富有充分表现力的时髦方式撰写文档。

### 安全

Yii 2.0 可以帮助你写出更加安全的代码。它包含内建的安全组件有效防止蛀牙（东方孤思子在搞什么鬼），SQL 注入，XSS 攻击，CSRF 攻击，Cookie 篡改，等等攻击。安全砖家 [汤姆·沃斯特（Tom 
Worster）](https://github.com/tom--)和[安东尼·法拉利（Anthony Ferrara）](https://github.com/ircmaxell)
帮我们审阅并重写了部分安全相关的代码哦。

### 数据库

玩转数据库从来没有这么容易过。Yii 2.0 支持 [数据库迁移（DB 
Migration）](http://www.docwithcn.com/guide-db-migrations.html)，[数据访问对象
(DAO)](http://www.docwithcn.com/guide-db-dao.html)，[查询构造器（query
builder）](http://www.docwithcn.com/guide-db-query-builder.html)
和 [活动记录（Active
Record）](http://www.docwithcn.com/guide-db-active-record.html)多种工具。相较于 1.1，Yii 2.0 改进了 AR 的性能，并且通过 ActiveRecord（AR） 和 Query Builder(QB，Q币？) 统一了查询数据的语法。下面的例子展现了你能如何用 AR 或 QB 方便地查询顾客（Customer）的数据。如你所见，两种方式均使用了跟 SQL 语法一脉相承的链式方法调用。

```php
use yii\db\Query;
use app\models\Customer;
 
$customers = (new Query)->from('customer')
    ->where(['status' => Customer::STATUS_ACTIVE])
    ->orderBy('id')
    ->all();
 
$customers = Customer::find()
    ->where(['status' => Customer::STATUS_ACTIVE])
    ->orderBy('id')
    ->asArray()
    ->all();
```

下面的代码展示了如何用 AR 实现关系查询：

```php
namespace app\models;
 
use app\models\Order;
use yii\db\ActiveRecord;
 
class Customer extends ActiveRecord
{
    public static function tableName()
    {
        return 'customer';
    }
 
    // 定义和 Order 模型类之间的一对多关系
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['customer_id' => 'id']);
    }
}
 
// 返回 id 为 100 的客户
$customer = Customer::findOne(100);
// 返回该用户的所有过往订单
$orders = $customer->orders;
```

还有下面的代码展示了如何更新一个客户记录。内部运行的时候，会用参数绑定来防止 SQL 注入攻击，并只向数据库中保存修改过的字段。

```php
$customer = Customer::findOne(100);
$customer->address = '123 Anderson St';
$customer->save();  // 执行 SQL：UPDATE `customer` SET `address`='123 Anderson St' WHERE `id`=100
```

Yii 2.0 支持各种路子的数据库。除了传统的关系型数据库，Yii 2.0 还增加了对 Cubrid，ElasticSearch，Sphinx 的支持。它还支持 
NoSQL 的数据库，包括 Redis 和 MongoDB。更重要的是，所有的数据库使用同一套 Query Builder 和 ActiveRecord 的 
API，这使得你在多个数据库之间的切换变得小菜一碟了。而且，在使用 AR 的时候，你甚至可以关联不同数据库之间的数据（比如 
MySQL 和 Redis）。

对于拥有大型数据库和对高性能有一定要求的应用，Yii 
2.0 也提供了原生的[数据库复制（主从分离）与读写分离](http://www.docwithcn.com/guide-db-dao.html#replication-and-read-write-splitting)支持。

### RESTful APIs

仅需几行代码，Yii 2.0 让你快速构建一系列全功能，遵从最新协议的 [RESTful
APIs](http://www.docwithcn.com/guide-rest-quick-start.html)。下面的例子展示了如何创建一个提供用户数据的
RESTful API 服务。

首先，创建一个控制器类 `app\controllers\UserController`，然后制定
`app\models\User` 为提供服务的模型：

```php
namespace app\controllers;
 
use yii\rest\ActiveController;
 
class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';
}
```

之后，修改你应用配置中 `urlManager` 组件的配置数组，加入以 Pretty URL 的形式提供 user 数据的配置项：

```php
'urlManager' => [
    'enablePrettyUrl' => true,
    'enableStrictParsing' => true,
    'showScriptName' => false,
    'rules' => [
        ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
    ],
]
```

然后就没有然后了！这个你刚刚创建的 API 支持以下功能：

-   `GET /users`: （逐页）列出全部用户；
-   `HEAD /users`：显示 user 列表的概览信息；
-   `POST /users`：创建新用户；
-   `GET /users/123`：返回用户 123 的详细资料；
-   `HEAD /users/123`：显示 user 123 的概述；
-   `PATCH /users/123` 以及 `PUT /users/123`：更新 user 123;
-   `DELETE /users/123`：删除 user 123;
-   `OPTIONS /users`：显示对于 `/users` 端点所支持的动作；
-   `OPTIONS /users/123`: 显示对于 `/users/123` 端点所支持的动作。

你也可以像这样通过 `curl` 命令访问你的 API，

```
$ curl -i -H "Accept:application/json" "http://localhost/users"

HTTP/1.1 200 OK
Date: Sun, 02 Mar 2014 05:31:43 GMT
Server: Apache/2.2.26 (Unix) DAV/2 PHP/5.4.20 mod_ssl/2.2.26 OpenSSL/0.9.8y
X-Powered-By: PHP/5.4.20
X-Pagination-Total-Count: 1000
X-Pagination-Page-Count: 50
X-Pagination-Current-Page: 1
X-Pagination-Per-Page: 20
Link: <http://localhost/users?page=1>; rel=self, 
      <http://localhost/users?page=2>; rel=next, 
      <http://localhost/users?page=50>; rel=last
Transfer-Encoding: chunked
Content-Type: application/json; charset=UTF-8

[
    {
        "id": 1,
        ...
    },
    {
        "id": 2,
        ...
    },
    ...
]
```

### 缓存

和 1.1 一样，Yii 2.0 支持各式各样的缓存选项，从服务器端的缓存，比如[片段缓存](http://www.docwithcn.com/guide-caching-fragment.html) 和
[查询缓存](http://www.docwithcn.com/guide-caching-data.html#query-caching)，到客户端的 [HTTP
缓存](http://www.docwithcn.com/guide-caching-http.html)。它们的底层包含多种缓存驱动，比如：APC，Memcache，文件缓存，数据库缓存，等等。

### 表单

在 1.1 中，你可以非常便捷地创建同时支持客户端与服务器端验证的 HTML 表单。在 
Yii 2.0 中，[操作表单](http://www.docwithcn.com/guide-input-forms.html)
已变得更加容易。下面的例子显示了你可以如何创建登陆表单。

首先撸一个 `LoginForm` 模型用于保持要收集的数据。在这个类中你要列举你要用来验证用户输入的规则。这些规则将来会用于自动生成所需客户端 JavaScript 验证逻辑。

```php
use yii\base\Model;
 
class LoginForm extends Model
{
    public $username;
    public $password;
 
    /**
     * @return array 验证规则
     */
    public function rules()
    {
        return [
            // 用户名和密码为必填项
            [['username', 'password'], 'required'],
            // 密码需要用 validatePassword() 验证
            ['password', 'validatePassword'],
        ];
    }
 
    /**
     * 验证密码
     * 该方法用于作为密码栏的行内验证器
     */
    public function validatePassword()
    {
        $user = User::findByUsername($this->username);
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError('password', '用户名或密码错误。');
        }
    }
}
```

然后创建该登录表单的视图代码:

```php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
 
<?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'username') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= Html::submitButton('Login') ?>
<? ActiveForm::end() ?>
```

### 验证与授权

与 1.1 时一样，Yii 2.0 提供了内建的用户验证与授权支持。它支持诸如登陆登出，基于 cookie 或 token 的[登录验证](http://www.docwithcn.com/guide-security-authentication.html)，[访问控制过滤](http://www.docwithcn.com/guide-security-authorization.html#access-control-filter)以及[基于角色的访问控制(RBAC)](http://www.docwithcn.com/guide-security-authorization.html#role-based-access-control-rbac)等功能。

Yii 2.0 也同样提供[通过外部凭证提供者进行验证](https://github.com/yiisoft/yii2-authclient)（第三方授权验证）的能力。它支持 OpenID、OAuth1 和 OAuth2 等协议。

### 小部件（Widgets）

Yii 2.0 提供了包含丰富的 UI 元素的集合，称为[小部件（Widgets）](http://www.docwithcn.com/guide-structure-widgets.html)，用以帮助我们快速构建可交互的用户界面。Yii 
2.0 已经内建了对于 [Bootstrap](http://getbootstrap.com/) 小部件和 [jQuery 
UI](http://jqueryui.com/) 小部件的原生支持。它也提供如分页，grid view（网格视图），list 
view（列表视图），Details（详情视图）等常用的小部件，它们一起让开发 Web 
应用变成又速度，又享受的过程。举例来说的话，你可以用以下短短几行代码创建出一个全功能的使用战斗民族语言皮肤的 jQuery UI 日期选择器：

```php
use yii\jui\DatePicker;
 
echo DatePicker::widget([
    'name' => 'date',
    'language' => 'ru',// 译者注：ru 就是 Russian 的语言代码，就像 zh 是中文，-CN表示简体，-TW表示正体
    'dateFormat' => 'yyyy-MM-dd',
]);
```

### 助手类（Helpers）

Yii 2.0 提供了很多实用的[助手类](https://github.com/yiisoft/yii2/tree/master/framework/helpers)用来简化常见工作。比如，著名的
`Html` 助手就包含了一系列的静态方法用来创建不同种类的 HTML 标签。然后是，`Url` 助手类让你可以很容易地创建各种 URL，比如下面所示的那种：

```php
use yii\helpers\Html;
use yii\helpers\Url;
 
// 创建一个包含很多国家的多选框列表
echo Html::checkboxList('country', 'USA', $countries);
 
// 生成一个类似于 "/index?r=site/index&src=ref1#name" 的 URL
echo Url::to(['site/index', 'src' => 'ref1', '#' => 'name']);
```

### 国际化

Yii 包含对网站国际化需求的有力支持，也因此，它广泛流行于世界各地。它支持[消息翻译](http://www.docwithcn.com/guide-tutorial-i18n.html#message-translation)，以及 [视图翻译](http://www.docwithcn.com/guide-tutorial-i18n.html#views)。它也支持基于地理位置的[单复数变化以及日期格式化](http://www.docwithcn.com/guide-tutorial-i18n.html#advanced-placeholder-formatting)，并遵从 [ICU
标准](http://icu-project.org/apiref/icu4c/classMessageFormat.html)。举例，

```php
// 带有日期格式化的消息翻译
echo \Yii::t('app', 'Today is {0, date}', time());
 
// 带有复数形式的消息翻译，“plural”单词的意思是复数。
echo \Yii::t('app', 'There {n, plural, =0{are no cats} =1{is one cat} other{are # cats}}!', ['n' => 0]);
```

### 模版引擎（Template Engines）

Yii 2.0 使用 PHP 作为默认的模版语言。但同时也通过官方提供的[模版引擎扩展](http://www.docwithcn.com/guide-tutorial-template-engines.html)支持了 [Twig](http://twig.sensiolabs.org/)
和 [Smarty](http://www.smarty.net/) 两种模版引擎。而且，你自己做支持其他模版引擎的扩展也是可以的。

### 测试

Yii 2.0 通过集成 [Codeception 测试框架](http://codeception.com/)和 [Faker 
数据拟真器](https://github.com/fzaninotto/Faker)进一步加强了对代码测试的支持力度。它同时包含有一个 Fixture 
（译者注：PHPunit 翻译为基境，工程领域一般称为测试夹具，用来提供一个模拟的测试数据环境）框架，它与数据库迁移相绑定，允许你更灵活地管理你的模拟测试数据。

### 应用模板

为了进一步帮你缩减开发时间，Yii 
发布有两款应用程序模版，每一个拿出来都是全功能的互联网应用。[基础应用模版](http://www.docwithcn.com/guide-start-installation.html#installing-via-composer)可以作为开发小型或简单网站的起点，比如公司门户，个人站点等等。而[高级应用模板](http://www.docwithcn.com/guide-tutorial-advanced-app.html)则更适用于开发包含多个业务层，或由大型开发团体负责的大型企业级应用。

### 扩展（Extensions）

尽管 Yii 2.0 已经提供了很多屌炸天的功能，它的扩展结构使得它可以更屌更牛逼。Yii 
的扩展是指专门为 Yii 应用设计，提供即用功能的可再发行的软件包。很多 Yii 的内建功能也是通过（官方）扩展的形式提供的，比如 
[邮件扩展](http://www.docwithcn.com/guide-tutorial-mailing.html) 和 
[Bootstrap 前端框架扩展](https://github.com/yiisoft/yii2-bootstrap)。Yii 一直也以庞大的用户贡献[扩展库](http://www.yiiframework.com/extensions/)而自豪，截止目前为止，该库有将近 1700 个扩展。我们同时也在 [packagist.org](https://packagist.org/search/?q=yii) 上发现有超过 1300 个 Yii 相关的扩展包。

上手入门
---------

要上手 Yii 2.0，可以简单地运行下面的命令：

```bash
# 在 Composer 全局安装 composer-asset-plugin 前端资源插件。（此步骤全局生效，一劳永逸）如果该插件后续有更新请自行更新
php composer.phar global require "fxp/composer-asset-plugin:1.0.0-beta3"

# 安装基础应用模版
php composer.phar create-project yiisoft/yii2-app-basic basic 2.0.0
```

上面的命令假定你已经安装有 [Composer](https://getcomposer.org/) 了。如果没有，请参考 [Composer 安装说明](http://getcomposer.org/doc/00-intro.md#installation-nix)安装一下。

请注意在安装过程中可能会提示说要你输入你的 GitHub 用户名和密码。这是正常的，输入完继续就可以了。（译者注：没有 GitHub 
账号的程序猿没法愉快的玩耍了，当然依旧可以用传统方式复制粘贴）

经过上面的指令之后，你就拥有了一个已经可以运行的 Web 应用。可以通过 `http://localhost/basic/web/index.php` 地址访问。

升级（Upgrading）
---------

如果你是从之前 Yii 2.0 的开发预览版本升级（比如 2.0.0-beta，2.0.0-rc），请参考下面的[升级说明](https://github.com/yiisoft/yii2/blob/master/framework/UPGRADE.md)。

如果你是要从 Yii 1.1 升级上来，那么我们必须要先提醒你，这个过程可能并不简单，这主要是由于 Yii 2.0 是完全的重写，甚至相当多的语法都不一样。当然，绝大多数 Yii 1.1 的知识仍然适用于 2.0。请阅读[升级说明](http://www.docwithcn.com/guide-intro-upgrade-from-v1.html)以了解 2.0 中引入了哪些主要的更改。

文档（Documentation）
-------------

Yii 2.0 提供 [权威指南（Definitive Guide，有时也被称为手册）](http://www.docwithcn.com/guide-README.html) 以及 [类库参考（class reference，用于查询 API 和类库的功能和源码）](http://www.docwithcn.com/index.html)两种类型的文档。其中权威指南正在被翻译为[多种语言](https://github.com/yiisoft/yii2/tree/master/docs)。（译者注：包含了数十位亲爱的小伙伴们贡献的简体中文版的成果哦。）

这里还有一些与 Yii 2.0 相关的书籍[刚刚出版](https://www.packtpub.com/web-development/web-application-development-yii-2-and-php)或正在被知名大牛（如[Larry Ullman（拉瑞·厄尔曼）](http://www.larryullman.com/)）撰写。拉瑞兄还费时费力地帮我们润色了一些权威指南中的描述。还有 Alexander Makarow（亚历山大·马卡洛夫，GitHub名 samdark，来自战斗民族，核心团队中的二号人物）也在协调一本社区贡献的 [cookbook about Yii 2.0（Yii 2.0 菜谱）](https://github.com/samdark/yii2-cookbook)，它的前作 Yii 1.1 菜谱也曾广受好评。

鸣谢
-------

特此感谢[为 Yii 做过贡献的所有人](https://github.com/yiisoft/yii2/graphs/contributors)。你们的支持与贡献是无价的！
