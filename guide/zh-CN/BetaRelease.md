Yii 2.0 Beta 发布了！（译者说明：markdown符号不熟，如有不对大家可更改，谢谢。）
======================

我们很高兴的宣布 Yii 框架第二版 Beta 发布了。你可以从 [Yii 官网](www.yiiframework.com/download/) 下载。Beta 发行版在 alpha 版之后完成了上百个新功能、改动和缺陷修复。我们将在下面回顾最重要的功能等。但首先我们希望回答一些关于 Beta 的共性问题。

问过的共性问题
------------------

## Beta 意味着什么？

Beta 意味着功能和设计固定了。在Beta 后 GA (General Availability) 前，我们主要聚焦于修复 bugs 和完善文档。我们不再增加新的主要功能或对设计进行明显改变。但仍有改动可能会破坏向后兼容，我们将努力最小化影响并明显地记录会破坏兼容的改动。

## GA 什么时候发行？

我们还没有发行 GA 版的准确时间。既然我们下一个焦点主要是修复缺陷和完善文档，我们只能期望 GA 版不会太久。（译者注：意思是 GA 不会那么快啦！）

## 我能使用 Beta 到我的项目吗？

如果你的项目时间紧且你还没有熟悉 Yii 2.0 就不要使用 Beta 。否则，你可以考虑使用 Yii 2.0 Beta ,前提是你能接受偶尔的不兼容改动。我们听说已经有很多项目基于 2.0 创建，运行良好。也要注意最低的 PHP 版本要求是 5.4 。

## 2.0有文档吗?

有的，我们有[官方指南](www.yiiframework.com/doc-2.0/guide-index.html)和 [API 文档](www.yiiframework.com/doc-2.0/)，并且我们还在持续添加更多内容。

## 我怎么升级基于1.1编写的项目到2.0？

请参考[从 1.1 升级](www.yiiframework.com/doc-2.0/guide-upgrade-from-v1.html)。需要注意的是，2.0是1.1版的完全重写，升级可能是没有价值的。当然，如果你掌握了1.1，你会发现2.0非常相似，这就帮助你快速采用2.0。

## 我怎么从2.0 alpha 升级？

如果你是通过 Composer 升级 alpha 版本，你需要移除 vendor 目录的所有东西，除了 .gitignore，然后重新运行 composer。这是一次性能解决的问题，以后版本都不必这样做了。请检查版本中的[日志更新](github.com/yiisoft/yii2/blob/2.0.0-beta/CHANGELOG.md)文件，以了解不兼容改动的更多细节。

## 我怎样了解2.0开发的最新动态？

Yii 2.0 的开发活动都在 GitHub 上：https://github.com/yiisoft/yii2 。你可以查看或标记这个项目来接收开发动态。你也可以在https://twitter.com/yiiframework follow 我们的 twitter。

2.0 Alpha 后的主要改动
------------------------

你可以在[日志更新]查看完整的改动列表。以下我们将总结最重要的新功能和改动。

## 结构

Yii 2 的类自动加载现在遵循[ PSR-4 标准](https://github.com/php-fig/fig-standards/blob/master/proposed/psr-4-autoloader/psr-4-autoloader.md)。这导致三点改进：
- 框架目录结构更简单
- 扩展目录结构更简单
- 停止 PEAR 风格的类命名以更简单和更快的自动加载类
控制器类现在必须有命名空间且必须位于 Module::controllerNamespace，除了使用 Module::controllerMap映射控制器。我们还以子目录来添加回群控制器的支持，和1.1一样。

## 易用性

易用性是 Yii 开发团队的最高优先事项之一。这就是为什么我们一直在花费大量时间选择每个合适的名字、让代码对IDE更友好和日复一日更努力地开发。
我们已经停止使用PSR-1 和 PSR-2，现在能非常好的支持不同 IDE ，代码风格校验器和自动格式化程序。

## 性能

最显著的改动是会话到真正使用才开启。这样使应用没有在开启不必要的会话中浪费资源。
如果你在项目中使用 MarkDown ，你会发现 MarkDown 格式化速度明显改善。这是因为 Carsten Brandt (cebe，Yii 核心开发团队成员)分析了所有已有解决方案后从头建立了一个新的 MarkDown 库。这个新库更快速更易于扩展，还支持 GitHub 风格的格式和其他许多功能。

## 安全
Yii 现在使用隐藏的 CSRF 占位符来防止 BREACH 类型的漏洞攻击。
RBAC 商业规则被重构了，重构的 RBAC 提供了一个更灵活而安全的解决方案。我们取消使用商业规则中的 eval() 方法。

## RESTful API 架构

Yii 长期以来被要求的一个功能就是对 RESTful API 开发的内置支持。这个功能随着 Beta 的发布终于实现了。由于该文章的篇幅限制，这里就不展开说细节了。你可以参考[官方指南]以了解更多细节。以下主要总结了现在支持的功能：
- 在用于活动记录（ActiveRecord）的通用 API 支持下的快速构建原型；
- 响应格式可协商（默认支持 JSON 和 XML ）；
- 以可选的输出字段支持可自定义的对象序列化；
- 数据收集和错误验证的恰当格式；
- 以适当的 HTTP 动词检查实现高效路由；
- 支持 OPTIONS 和 HEAD 动词；
- 身份验证；
- 权限设置；
- 支持 HATEOAS（超媒体作为应用程序状态引擎）；
- HTTP 缓存；
- 速率限制。

## 依赖注入和服务定位器

许多用户曾问 Yii 为什么不提供依赖注入（DI）容器。事实上 Yii 很久前就已经提供了一个类似的设备，即服务定位器- Yii应用实例。现在我们正式提取出服务定位器作为一个可重用组件 yii\di\ServiceLocator 。和以前一样， Yii 应用和模块都是服务定位器。你可以使用 Yii::$app->get('something')这个表达式获取服务（1.1 的术语又称为应用组件）。
除了服务定位器，我们还实现了一个 DI 容器 yii\di\Container 来帮助你开发更低耦合的代码。我们的内部分析表明该 DI 容器是 最著名的 PHP DI 实现中最快速之一。你可以使用 Yii::$container->set() 来配置类的缺省设置。有了新的实现，旧的 Yii::$objectConfig 就停止使用了。

## 测试

Yii 整合了 Codeception 测试框架，它允许你测试一个应用程序作为一个整体模拟用户操作和验证输出是否正确。和 PhpUnit's selenium 支持相反，这个测试框架不需要浏览器，所以它更容易安装到持续集成服务器且运行更快。

Yii 还增加了对构建测试文件夹(test fixtures)更多的支持，构建测试时，这往往是一个繁琐和费时的任务。尤其是，fixture 框架被开发来统一 fixture 的定义和管理。我们通过整合 “faker”库创建了 faker 扩展来帮助你创造一些 现实地看是虚假的 fixture 数据。

基础应用模板和高级应用模板都实现了测试功能，包括单元测试、功能测试和验收测试。这将为测试驱动开发提供很好的起点。

## 模型验证

模型测试功能多了很多有用的增强。UniqueValidator 和 ExistValidator 现在支持验证多列。以下是关于 *unique* 验证规则声明的一些示例：

```
php

// a1 必须是唯一的
['a1', 'unique']

// a1 必须唯一但 a2 列将用于检查 a1 值的唯一性
['a1', 'unique', 'targetAttribute' => 'a2']

// a1 和 a2 必须同时唯一且都接收错误信息
[['a1', 'a2'], 'unique', 'targetAttribute' => ['a1', 'a2']]

// a1 和 a2 必须同时唯一，只有 a1 接收错误信息（译者注：示例有没有错？）
['a1', 'unique', 'targetAttribute' => ['a1', 'a2']]

// a1 通过检查 a2 和 a3 的唯一性（使用 a1 值）来确保唯一
['a1', 'unique', 'targetAttribute' => ['a2', 'a1' => 'a3']]

```

验证可以有条件地完成（又称条件验证）。这通过添加两个属性 *when* 和 *whenClient* 到每个验证器实现支持。以下示例展示了如何在只有国家选为 “USA”时才必须  "state" 输入项：

```
php

['state', 'required',
    'when' => function ($model) {
        return $model->country == Country::USA;
    },
    'whenClient' =>  "function (attribute, value) {
        return $('#country').value == 'USA';
    }",
]
```

有时你可能需要做一些临时的数据验证又想避免编写新模型类的麻烦，你可以借助新 *yii\base\DynamicModel* 的帮助来完成。例如：

```
php

public function actionSearch($name, $email)
{
    $model = DynamicModel::validateData(compact('name', 'email'), [
        [['name', 'email'], 'string', 'max' => 128],
        ['email', 'email'],
    ]);
    if ($model->hasErrors()) {
        // 验证失败
    } else {
        // 验证成功
    }
}

```

## 数据库和活动记录

数据库相关的功能是 Yii 最强大的方面之一。他们在 alpha 发布时就非常有趣了，现在 beta 带来了更多功能和改进。其中对 SQL 数据库的支持，我们已经为[elasticsearch-RESTful搜索引擎](http://www.elasticsearch.org/)、[redis键值对数据库](http://redis.io/)和[Sphinx search-全文检索引擎](http://sphinxsearch.com/docs/)提供活动记录设计模式的实现。Beta 版现在增加了对[mongodb-分布式文件存储数据库](https://www.mongodb.org/)的支持。

## 嵌套事务支持

Yii 现在支持嵌套事务。因此，你可以安全地开启一个事务而无需担心是否有现有的事务包围它了。

## 连接查询

我们添加了 *ActiveQuery::joinWith()* 方法来支持使用已声明的 AR 关系创建 JOIN SQL 语句。当你需要以外表的列筛选或排序时这个方法特别有用。例如：

```
// 查找所有订单并以顾客 id 和订单 id 排序，同时要预先加载 "customer"
$orders = Order::find()->joinWith('customer')->orderBy('customer.id, order.id')->all();

// 查找所有包括书籍的订单并预先加载 "books"
$orders = Order::find()->innerJoinWith('books')->all();

```

这个功能在 网格视图（GridView）中显示关联列时特别有用，通过使用 *joinWith()* 来筛选和排序关联列特别简单。

## 数据类型转换

现在活动记录能将数据库检索出的数据转换为正确的数据类型。例如，如果你有个整型列 *类型*，当相应的活动记录实例填充后，你将发现特性得到一个整型值 *类型* ，而不是字符串值。

## 搜索

为方便建立搜索功能，我们添加了 *Query::filterWhere()* 方法来自动移除过滤空值。例如，如果你有个包括 *name* 和 *email* 过滤字段的搜索表单，你可以使用以下代码来建立搜索查询语句。如果没有这个方法，你就必须检查用户是否在过滤字段中输入任何东西，且没有输入的话你就不能把它放到查询条件。*Query::filterWhere()* 将只添加非空字段到查询条件。

 ```
 $query = User::find()->filterWhere([
     'name' => Yii::$app->request->get('name'),
     'email' => Yii::$app->request->get('email'),
 ]);

 ```

## 批查询

要支持大数据量查询，我们添加了批查询功能来分批取得数据以取代一次取出全部数据。批查询允许你维持服务器内存的使用在限定值。例如：

```
use yii\db\Query;

$query = (new Query())
    ->from('user')
    ->orderBy('id');

foreach ($query->batch() as $users) {
    // $users 是用户表取出100条记录以内的数组
}

// 或者你需要逐行遍历记录
foreach ($query->each() as $user) {
    // $user 表示用户表取出的一行数据
}
```

你也可以用活动记录批查询。例如：

```
// 一次取回 10 个客户
foreach (Customer::find()->batch(10) as $customers) {
    // $customers 是10个以内的 Customer 对象数组
}
// 一次取回 10 个客户并逐个遍历
foreach (Customer::find()->each(10) as $customer) {
    // $customer 是一个 Customer 对象
}
// 使用预先加载的批查询
foreach (Customer::find()->with('orders')->each() as $customer) {
}

```

## 子查询支持

查询生成器已改进来支持子查询。你可以建立一个子查询作为常规的 Query 对象，然后在另一个查询中的适当位置使用它。例如：

```
$subQuery = (new Query())->select('id')->from('user')->where('status=1');
$query->select('*')->from(['u' => $subQuery]);

```

## 逆关系

关系通常成对定义。如 Customer 有一个关系名为 orders 而 Order 也有一个关系名为 customer 。下例中，我们会发现订单的 customer 不是那些订单的同一个客户对象，且访问 customer->orders 将触发一个 SQL 执行，而访问一个订单的 customer 将触发另一个 SQL 执行：

```
// SELECT * FROM customer WHERE id=1
$customer = Customer::findOne(1);

// echoes "not equal" 输出 “不等于”
// SELECT * FROM order WHERE customer_id=1
// SELECT * FROM customer WHERE id=1
if ($customer->orders[0]->customer === $customer) {
    echo 'equal';
} else {
    echo 'not equal';
}

```

为避免最后一条 SQL 语句不必要的执行，我们可以为客户和订单关系声明逆关系，通过如下这样调用 inverseOf() 方法实现：

```

class Customer extends ActiveRecord
{
    // ...
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['customer_id' => 'id'])->inverseOf('customer');
    }
}

```

现在我们执行以上所示的相同查询语句，我们将得到：

```

// SELECT * FROM customer WHERE id=1
$customer = Customer::findOne(1);
// echoes "equal" 输出 “等于”
// SELECT * FROM order WHERE customer_id=1
if ($customer->orders[0]->customer === $customer) {
    echo 'equal';
} else {
    echo 'not equal';
}

```

## 更一致的关联查询 API

在 2.0 alpha，我们介绍了活动记录对关系数据库（如 MySQL）和非关系数据库（如 redis,elasticsearch,MongoDB）的支持。Beta版本，我们重构了相关代码以保持接口一致。尤其是，我们弃用活动关系并让 ActiveQuery 担任建立活动记录关联查询和声明关系的入口角色。我们还添加了ActiveRecord::findOne() 和 findAll() 方法以支持主键或列值的快速查询。以前，这个功能由ActiveRecord::find()承担，这个方法有时会因不一致的返回类型导致困惑。


## 高级 Ajax 支持

我们决定使用优秀的 Pjax 库并创建 yii\widgets\Pjax 小部件。这是一个通用小部件，能够给它所包裹的任何东西启用 ajax 支持。例如，你可以用 Pjax 包裹网格视图（GridView）来启动基于 ajax 的网格分页和排序：

```
use yii\widgets\Pjax;
use yii\grid\GridView;

Pjax::begin();
echo GridView::widget([ /*...*/ ]);
Pjax::end();

```

## 请求和响应

除了修复内部缺陷和改进，请求和响应还有一些明显的改动。最明显的是现在操作请求数据要这样做：

```

// 从请求获得 GET 参数，缺省为 1
$page = Yii::$app->request->get('page', 1);
// 从请求获得 POST 参数，缺省为 null
$name = Yii::$app->request->post('name');

```

另一个根本变化是响应直到应用的生命周期终止那一刻才真正发出，这允许你修改你想修改的 HTTP 头和内容及位置。

请求类现在也能够理解不同 body 类型语法，如 JSON 请求。

## 过滤器

整个动作过滤机制已经被更新了。你现在能在控制器层或应用层面和模块层面使用动作过滤。这允许你分层过滤动作流。例如，你可以安装过滤器到模块，以便该模块的所有动作服从这个过滤器；你也能进一步安装其他的过滤器到模块的控制器，以便只有这些控制器的动作被过滤。

我们重新组织了代码并建立整套过滤器到 yii\filters 命名空间。例如，你能使用 yii\filters\HttpBasicAuth 过滤器通过在控制器或模块声明它来启动基于 HTTP 的基础授权：

```
public function behaviors()
{
    return [
        'basicAuth' => [
            'class' => \yii\filters\auth\HttpBasicAuth::className(),
            'exclude'=> ['error'],   // 不要用在 "error" 动作
        ],
    ];
}
```


## 引导组件

我们推介了应用生命周期中重要的 “引导” 步骤。扩展通过在 composer.json 文件声明可以注册到引导类。一个普通组件也可以注册为引导组件，只需在Application::$bootstrap 定义。

一个引导组件在应用开始处理请求前就初始化了。这给了组件一个注册重要事件处理器和参与应用生命周期的机会。

## URL 处理

Since developers are dealing with URLs a lot we've extracted most of URL-related methods into a Url helper class resulting in a nicer API.
既然开发人员需要处理很多 URL 相关操作，我们提取了 URL 相关使用最多的方法到 Url 助手类来提供更好的 API 。

```

use yii\helpers\Url;

// 当期活动路由
// 示例： /index.php?r=management/default/users
echo Url::to('');

// 同一个控制器，不同动作
// 示例： /index.php?r=management/default/page&id=contact
echo Url::toRoute(['page', 'id' => 'contact']);


// 同一个模块，不同控制器和动作
// 示例： /index.php?r=management/post/index
echo Url::toRoute('post/index');

// 不管哪个控制器调用这个方法的绝对路径
// 示例： /index.php?r=site/index
echo Url::toRoute('/site/index');

// 当前控制器区分大小写动作 `actionHiTech` 的 url
// 示例： /index.php?r=management/default/hi-tech
echo Url::toRoute('hi-tech');

// 区分大小写控制器和动作 `DateTimeController::actionFastForward` 的 url
// 示例： /index.php?r=date-time/fast-forward&id=105
echo Url::toRoute(['/date-time/fast-forward', 'id' => 105]);

// 从别名获取 URL
Yii::setAlias('@google', 'http://google.com/');
echo Url::to('@google/?q=yii');

// 为当前页获取 canonical URL
// 示例： /index.php?r=management/default/users
echo Url::canonical();

// 获取主页 URL
// 示例： /index.php?r=site/index
echo Url::home();

```

```
Url::remember(); // 保存将被使用的页面 URL
Url::previous(); // 获取前一保存页的 URL

```

URL 规则也有改进。你能使用新的 yii\web\GroupUrlRule 来组团一次定义规则的共同部分而不是重复它们：

```

new GroupUrlRule([
    'prefix' => 'admin',
    'rules' => [
        'login' => 'user/login',
        'logout' => 'user/logout',
        'dashboard' => 'default/dashboard',
    ],
]);

// 以上规则等价于下面三条规则：
[
    'admin/login' => 'admin/user/login',
    'admin/logout' => 'admin/user/logout',
    'admin/dashboard' => 'admin/default/dashboard',
]

```

## 基于用户的访问控制（RBAC）

我们修改了 RBAC 的实现以更紧密地跟随 original NIST RBAC 模型。特别是，我们丢弃了操作和任务的概念，并以许可来替换他们，许可是 NIST RBAC 使用的概念。

且如前所述，我们还通过从 RBAC 分离来重新设计了商业规则功能。


## 翻译

首先我们由衷地感谢参与翻译框架核心信息的所有社区成员。现在核心信息已经有 26 种语言版本，这是非常令人钦佩的数字。

信息翻译现在支持语言撤退。例如，如果你的应用使用 fr-CA 语言而你只有 fr 的翻译版本，Yii 首先搜寻 fr-CA 翻译文件，如果没有，将尝试寻找 fr 。

一个新的选项添加到了每一个 Gii 生产器，该选项允许你选择是否需要通过 Yii::t() 生成信息已翻译的代码。

信息抽取工具现在支持编写字符串到 .po 文件和数据库。

## 扩展和工具

我们建立了一个文档生产器扩展，取名为 yii2-apidoc ，它可以用来帮助你生成和基于 MarkDown 指南一样界面好看的 API 文档。该生产器易于定制并可方便扩展以满足你的特定需求。它也用来生成官方文档和 API 文件，你可以在http://www.yiiframework.com/doc-2.0/ 查看。

Yii 调试器经过许多微小的改进后更加好用。它现在也像数据库查询和邮件列表那样在它的主页装备了邮件面板。

除了以上新的翻译支持， Yii 代码生长器工具 Gii 现在能够用来生成新扩展。你会注意到代码预览窗口也加强了，以便你能快速刷新和导航不同文件。它也是很方便复制粘贴和支持快捷键的。来试一试吧！


感谢你的支持！
==============
Yii 2.0 Beta 版的发行是一个重要的里程碑，凝聚了各方极大的努力。我们认为没有我们优秀社区的[所有有价值的贡献](https://github.com/yiisoft/yii2/graphs/contributors)，Beta 版就不可能发行。感谢为此版本的发行付出努力的所有人。
