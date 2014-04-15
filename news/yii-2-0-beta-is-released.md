[原文：http://www.yiiframework.com/news/](http://www.yiiframework.com/news/77/yii-2-0-beta-is-released/)  
主翻译：@aliciamiao 校对：@qiansen1386(东方孤思子) 时间：2014年4月13
# Yii 2.0 Beta 发布啦！

我们很高兴的宣布 Yii 框架第二版 Beta 发布了。你可以从 [Yii 官网处下载][1]。

Beta 发行版在 [alpha 版][2] 的基础上，又实现了上百个新功能、改动和缺陷修复。
我们将在下面回顾下有哪些最重要的改变。但首先我们想回答一些有关 Beta 版经常被问到的问题。

## 常见问题

  * **Beta 意味着什么？** Beta 意味着功能和设计确定下来了。在Beta 后 GA (General Availability) 前，我们主要聚焦于修复 bugs 和完善文档。我们不会再增加新的主要功能或对设计进行大幅修改。但仍有改动可能会破坏向后兼容性（BC：Backward Compatibility），我们将努力把影响减到最小并明确记录下那些会破坏兼容性的改动。

  * **GA 什么时候发行？** 我们还没有发行 GA 版的准确时间。既然我们下一个焦点主要是 Bug 修复和完善文档，我们只能预期说 GA 版的到来应该不会太久。

  * **我能把 Beta 运用到我的项目吗？** 如果你的项目时间紧且你还没有熟悉 Yii 2.0 就不要使用 Beta 。不然的话，你可以考虑使用 Yii 2.0 Beta，当然前提是你能接受偶尔的不兼容改动。我们听说目前已经有很多基于 2.0 master 分支创建的项目，且运行良好。同时千万记得 PHP 版本的最低要求是 5.4 哦。

  * **2.0有文档吗?** 当然，我们有[官方指南][3]和 [API 文档][4]，并且我们还在持续添加更多内容。（译者注：也要关注我们的[文档中文化项目](https://github.com/yii2-chinesization/yii2-zh-cn/)呦！）

  * **怎么把我的项目从 1.1 升级到 2.0？** 请参考手册中[从 Yii 1.1 升级][5]的章节。请注意，因为 2.0 相较于 1.1 是完全重构的，所以这种升级所需的改动不会太小。但是如果你掌握了 1.1，你会在 2.0 里发现很多相似之处，他们会帮助你更迅速地接受 2.0。

  * **我怎么从2.0 alpha 升级** 如果你是通过 Composer 升级 alpha 版本，你需要移除 vendor 目录里，除了`.gitignore`以外的所有东西，然后重新运行 composer。这是一次性的，以后发布的版本都不会要求这样做了。请查看此次发布中的[CHANGLOG（更新日志）][6]文件，以了解那些有关影响兼容性的变动的更多细节。

  * **我怎样了解2.0开发的最新动态？** Yii 2.0 的开发活动都在 GitHub 上：https://github.com/yiisoft/yii2 。你可以关注（watch）或标星（star）这个项目来接收开发动态。你也可以在 https://twitter.com/yiiframework follow 我们的 twitter。

## 2.0 Alpha 至今的主要改动

你可以在[更新日志（CHANGELOG）][7]查看完整的改动列表。我们将只在下面总结最重要的新功能和改动。

### 结构

现在 Yii 2 的类自动加载会遵循 [PSR-4 标准][8]。这将带来三点改进：

  * 框架目录结构更简洁。
  * 扩展目录结构更简洁。
  * 我们已经遗弃了 PEAR 风格的类命名，以使类的自动加载更简洁更快。

控制器类现在必须有命名空间且必须位于  `Module::controllerNamespace`，除非你通过 `Module::controllerMap` 来引用控制器映射机制。

我们还把用子目录分组控制器的支持添加回来了，和 1.1 一样。

### 易用性（或可用性 Usability）

易用性是 Yii 开发团队的最高优先目标之一。这就是为什么我们一直在花费大量时间给每个元件挑选合适的名称、让代码对 IDE 更友好和让开发者们日复一日的工作体验更愉悦。我们已经接受使用 PSR-1 和 PSR-2 编码风格，现在能非常好地直接支持不同 IDE 、[代码风格校验器][9]和[自动代码格式化工具][10]。

### 性能

最显著的改动是Session会话会等到真正被使用时才开启。这样使得应用不必为非必要的 Session 会话启动浪费资源。

如果你在你项目中使用了 MarkDown，你会发现 MarkDown 的渲染速度有明显改善。这是因为 Carsten Brandt（cebe，Yii 核心开发团队成员）分析了当下所有已有的解决方案后重新建立了一个全新的 MarkDown 库。这个新库快很多，且更易于扩展，还支持诸如 GitHub 风格格式和更多其他功能。

### 安全

Yii 现在使用 _隐蔽的（masked）_  CSRF 令牌来阻止 [BREACH][11] 形式的欺骗攻击。

RBAC 业务规则被重构了，重构后的 RBAC 提供了一个更灵活且安全的解决方案。我们消除了所有在业务规则中 `eval()` 方法的使用。

### RESTful API 架构

一个长期以来一直被被要求加入的功能就是对 RESTful API 开发的支持。这个功能随着 Beta 的发布终于实现了。由于篇幅限制，这里就不展开说细节了。你可以参阅[官方指南][12]了解更多细节。下面我们主要总结了目前已经支持的功能：

- 为活动记录（ActiveRecord）快速构建带有通用 API 支持的原型；
- 响应格式协商（原生支持 JSON 和 XML 两种格式）；
- 可自定义的对象序列化，并支持可选输出字段；
- 以适当格式收集数据与错误验证；
- 附带相应 HTTP 动作检查功能的高效路由；
- 支持 `OPTIONS` 和 `HEAD` 动作；
- 身份验证；
- 用户权限；
- 支持 HATEOAS（超媒体作为应用程序状态引擎）；
- HTTP 缓存；
- 速率限制。

### 依赖注入和服务定位器

许多用户曾问到为什么 Yii 不提供依赖注入（DI）容器。事实上 Yii 很久前就已经提供了一个类似的工具，被称作Service Locator - 也就是 Yii 应用实例本身。现在我们正式提取出服务定位器作为一个可重用组件 `yii\di\ServiceLocator`。和以前一样， Yii 应用主体（Application）和其模块（Module）都是服务定位器。你可以使用 `Yii::$app->get('something')` 这个表达式获取服务（1.1 的术语里又称为应用组件）。

除了服务定位器，我们还实现了一个 DI 容器 `yii\di\Container` 来帮助你开发更低耦合的代码。我们的内部分析表明该 DI 容器是所有知名的 PHP DI 实现中最快速的之一。你可以使用 `Yii::$container->set()` 来配置类的缺省设置。有了新的实现，旧的 `Yii::$objectConfig` 自然也就停止使用了。

### 测试

Yii 整合了 [Codeception 测试框架][13]，它允许你测试一个应用程序作为一个整体模拟用户操作和验证输出是否正确。和 PhpUnit's selenium 支持相反，这个测试框架不需要浏览器，所以它更容易安装到持续集成（CI）服务器且运行更快。

Yii 还增加了对构建测试文件夹（test fixtures）更多的支持，构建测试时，这往往是一个繁琐和费时的任务。特别的，[fixture 框架][14]被开发来统一 fixture 的定义和管理。我们通过整合 “faker” 库创建了 [faker 扩展][15]来帮助你创造一些拟真的 fixture 假数据。

基础应用模板和高级应用模板（"apps-basic" and "apps-advanced"）都实现了测试功能，包括单元测试、功能测试和验收测试。这将为测试驱动开发（TDD）提供一个很好的起点。

### 模型验证

模型测试功能多了很多实用的改进。

`UniqueValidator` 和 `ExistValidator` 现在支持验证多列了。以下是关于 `unique` 验证规则声明的一些示例：

```php
// a1 必须是唯一的
['a1', 'unique']
 
// a1 必须唯一，但 a2 列将用于检查 a1 值的唯一性
['a1', 'unique', 'targetAttribute' => 'a2']
 
// a1 和 a2 必须同时唯一且都接收错误信息
[['a1', 'a2'], 'unique', 'targetAttribute' => ['a1', 'a2']]
 
// a1 和 a2 必须同时唯一，只有 a1 接收错误信息
['a1', 'unique', 'targetAttribute' => ['a1', 'a2']]
 
 // a1 通过检查 a2 和 a3 的唯一性（使用 a1 值）来确保唯一
['a1', 'unique', 'targetAttribute' => ['a2', 'a1' => 'a3']]
```

验证可以有条件地完成（又称条件验证）。这通过添加两个属性 `when` 和 `whenClient` 到每个验证器实现。以下示例展示了如何在只在国家选为 “USA” 时才要求 "state" 输入项：

```php
['state', 'required',
    'when' => function ($model) {
        return $model->country == Country::USA;
    },
    'whenClient' =>  "function (attribute, value) {
        return $('#country').value == 'USA';
    }",
]
```

有时你可能需要做一些临时的数据验证又想避免编写新模型类的麻烦，你可以借助新 `yii\base\DynamicModel` 的帮助来完成。例如：

```php
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

### 数据库和活动记录

数据库相关的功能是 Yii 最强大的方面之一。他们在 alpha 发布时就非常引人注意了，现在 beta 带来了更多功能和改进。除了对 SQL 数据库的支持，我们还已经为[elasticsearch RESTful 搜索引擎][16]、[redis 键值对存储数据库][17]和[Sphinx search 全文检索引擎][18]提供了活动记录设计模式的实现。Beta 版现在又增加了对[mongodb 分布式文件存储数据库][19]的支持。

#### 嵌套事务支持

Yii 现在支持嵌套事务。因此，你可以安全地开启一个事务而无需担心是否有现有的事务包围它了。

#### 连接查询

我们添加了 `ActiveQuery::joinWith()` 方法，来支持使用已声明的 AR 关系创建 JOIN SQL 语句。当你需要以外表的列筛选或排序时这个方法特别有用。比如：

```php
// 查找所有订单并以顾客 id 和订单 id 排序，同时要预先加载 "customer"
$orders = Order::find()->joinWith('customer')->orderBy('customer.id, order.id')->all();
 
// 查找所有包括书籍的订单并预先加载 "books"
$orders = Order::find()->innerJoinWith('books')->all();
```
这个功能在栅格视图（GridView）中显示关联列时特别有用，通过使用 `joinWith()` 来筛选和排序关联列特别简单.

#### 数据类型转换

在活动记录能将数据库检索出的数据转换为正确的数据类型。例如，如果你有个整型列 `type`，当相应的活动记录实例填充后，你将发现 `type` 数据属性会得到一个整型值，而不是字符串值。

#### 搜索

为方便建立搜索功能，我们添加了 `Query::filterWhere()` 方法来自动移除空的过滤值。例如，如果你有个包括 `name` 和 `email` 过滤字段的搜索表单。你可以使用以下代码来建立搜索查询语句。如果没有这个方法，你就必须检查用户是否在过滤字段中输入任何东西，且没有输入的话你就不能把它放到查询条件。`filterWhere()` 将只添加非空字段到查询条件。

```php
$query = User::find()->filterWhere([
    'name' => Yii::$app->request->get('name'),
    'email' => Yii::$app->request->get('email'),
]);
```

#### 批量查询

要支持大数据量查询，我们添加了批查询功能来分批取得数据以取代一次取出全部数据。批查询允许你将服务器内存的使用限制在一定范围内。例如：

```php
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

你也可以用活动记录进行批量查询。例如：

```php
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

#### 子查询支持

查询生成器已改进来支持子查询。你可以建立一个子查询作为常规的 `Query` 对象，然后在另一个查询中的适当位置使用它。例如：

```php
$subQuery = (new Query())->select('id')->from('user')->where('status=1');
$query->select('*')->from(['u' => $subQuery]);
```

#### 逆关系

关系通常成对定义。比如，`Customer` 有一个关系名为 `orders`，而 `Order` 也有一个关系名为 `customer`。下例中，我们会发现某一订单的 `customer` 和关联那些订单的 `customer` 并不是同一个客户对象。且访问 `customer->orders` 将触发一个 SQL 执行，而访问一个订单的 `customer` 将触发另一个： 

```php
// SELECT * FROM customer WHERE id=1
$customer = Customer::findOne(1);

// 结果输出 “不等于”
// SELECT * FROM order WHERE customer_id=1
// SELECT * FROM customer WHERE id=1
if ($customer->orders[0]->customer === $customer) {
    echo '等于';
} else {
    echo '不等于';
}
```

为避免最后一条 SQL 语句不必要的执行，我们可以为 `customer` 客户和 `orders` 订单关系声明逆关系，通过如下这样调用 `inverseOf()` 方法实现：

```php
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

```php
// SELECT * FROM customer WHERE id=1
$customer = Customer::findOne(1);

// 结果输出 “等于”
// SELECT * FROM order WHERE customer_id=1
// SELECT * FROM customer WHERE id=1
if ($customer->orders[0]->customer === $customer) {
    echo '等于';
} else {
    echo '不等于';
}
```

#### 更一致的关联查询 API

在 2.0 alpha，我们介绍了活动记录对关系数据库（如 MySQL）和 NoSQL 数据库（如 redis,elasticsearch,MongoDB）的支持。Beta版本，我们重构了相关代码以保持接口更加一致。尤其是，我们弃用了 `ActiveRelation` 活动关系并让 `ActiveQuery` 担任建立活动记录关联查询和声明关系的入口角色。我们还添加了 `ActiveRecord::findOne()` 和 `findAll()` 方法以支持主键或列值的快速查询。以前，这个功能由 `ActiveRecord::find()` 承担，这个方法有时会因不一致的返回类型导致一些困惑。

### 高级 Ajax 支持

我们已经决定采用优秀的 [Pjax][20] 库并创建 `yii\widgets\Pjax` 小部件。这是一个通用小部件，能够给它所包裹的任何东西启用 ajax 支持。例如，你可以用 `Pjax` 包裹栅格视图 `GridView` 来启动基于 ajax 的网格分页和排序：

```php
use yii\widgets\Pjax;
use yii\grid\GridView;

Pjax::begin();
echo GridView::widget([ /*...*/ ]);
Pjax::end();
```

### 请求和响应（Request and response）

除了很多内部缺陷的修复和改进，请求和响应还有一些其他明显的改动。最明显的是现在操作请求数据要这样做：、

```php
// 从请求获得 GET 参数，缺省为 1
$page = Yii::$app->request->get('page', 1);
// 从请求获得 POST 参数，缺省为 null
$name = Yii::$app->request->post('name');
```

另一个根本变化是响应直到应用的生命周期终止那一刻才真正发出，这允许你自由地修改你想要的 HTTP 头部和主体的细节及位置。

请求类现在也能够理解不同 body 类型语法，如 JSON 请求。

### 过滤器

整个动作过滤机制已经被更新修订了。现在不仅在控制器层面，甚至是应用层面或模块层面也都能使用动作过滤了。这允许你分层过滤动作流。例如，你可以安装过滤器到模块，以便该模块的所有动作服从这个过滤器；你也能进一步安装其他的过滤器到模块的控制器，以便只有这些控制器的动作被过滤。

我们重新组织了代码并建立整套过滤器到 `yii\filters` 命名空间。例如，你能通过在控制器或模块声明 `yii\filters\HttpBasicAuth` 过滤器，来启动基于 HTTP 的基础授权：

```php
public function behaviors()
{
    return [
        'basicAuth' => [
            'class' => \yii\filters\auth\HttpBasicAuth::className(),
            'exclude'=> ['error'],   // 不要用在 "error" 动作（译者注：用来显示错误信息的页面，千万别给过滤掉）
        ],
    ];
}
```

### 引导组件（Bootstrap Components）

我们引入了应用生命周期中重要的 “引导” 步骤。（译者注：这里的bootstrap，不是指那个前端框架）Yii 的扩展只要在 `Application::$bootstrap` 定义了，就可以通过在 `composer.json` 文件声明，从而注册到引导类。。

一个引导组件在应用开始处理请求前就初始化了。这给了组件一个注册重要事件处理器和参与应用生命周期的机会。

### URL 处理

由于开发人员正在处理的 URL 很多，我们已经提取大部分 URL 相关方法到 Url 助手类，从而打造一个更好的 API 。

```php
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

// 为当前页获取规范 URL
// 示例： /index.php?r=management/default/users
echo Url::canonical();

// 获取主页 URL
// 示例： /index.php?r=site/index
echo Url::home();

Url::remember(); // 保存将被使用的页面 URL
Url::previous(); // 获取前一保存页的 URL
```

URL 规则也有改进。你能使用新的 `yii\web\GroupUrlRule` 把他们合并在一起，一次性地定义好的它们的共通部分，而无需多次重复：

```php
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

### 基于用户角色的访问控制（RBAC）

我们修改了 RBAC 的实现以使其更紧密地跟随 original NIST RBAC 模型。特别地，我们摒弃了 _operation（操作）_ 和 _task（任务）_的概念，并以 _permission(许可)_ 来替换他们，许可是 NIST RBAC 所使用的概念。

且如前所述，我们还通过把业务规则（biz rule）从 RBAC 中分离出来，单独进行管理，重新设计了它的功能。

### 翻译

首先我们由衷地感谢参与翻译框架核心信息的所有社区成员。现在核心信息已经有 26 种语言版本，这是非常令人钦佩的数字。（东方孤思子注：这里面也包括我的努力哦！）

信息翻译现在支持语言的失败回滚（fallback）。例如，如果你的应用使用 `fr-CA` 语言而你只有 `fr` 的翻译版本，Yii 首先搜寻 `fr-CA` 翻译文件，如果没有，将尝试寻找 `fr` 。

一个新的选项添加到了每一个 Gii 生成器，该选项允许你选择是否需要通过 Yii::t() 生成带翻译信息的代码。

信息抽取工具现在支持编写字符串到 `.po` 文件和数据库。

### 扩展和工具

我们建立了一个文档生成器扩展，取名为 `yii2-apidoc`，它可以用来帮助你生成界面好看的 API 文档，以及基于 MarkDown 的教程指南 。该生成器易于定制并可方便扩展以满足你的特定需求。它也用来生成官方文档和 API 文件，你可以在 http://www.yiiframework.com/doc-2.0/ 处查看。

Yii 调试器经过许多微小的改进后更加好用。它现在也在它的总结页面装备了 email 面板，以及数据库查询和邮件总结列。

除了上面提到过的新的翻译支持， Yii 代码生成器工具 Gii 现在也能够用来生成新的扩展。会注意到代码预览窗口也加强了，现在可以快速刷新与在不同文件之间跳转。它也支持很方便地复制粘贴和支持快捷键的。来试一试吧！

## 鸣谢！

Yii 2.0 Beta 版的发行是一个重要的里程碑，凝聚了各方极大的努力。我们认为没有我们优秀社区的[所有有价值的贡献][21] ，Beta 版就不可能发行。感谢所有让此版本的发行成为现实的人们！

   [1]: http://www.yiiframework.com/download/
   [2]: http://www.yiiframework.com/news/76/yii-2-0-alpha-is-released/
   [3]: http://www.yiiframework.com/doc-2.0/guide-index.html
   [4]: http://www.yiiframework.com/doc-2.0/
   [5]: http://www.yiiframework.com/doc-2.0/guide-upgrade-from-v1.html
   [6]: https://github.com/yiisoft/yii2/blob/2.0.0-beta/CHANGELOG.md
   [7]: https://github.com/yiisoft/yii2/blob/2.0.0-beta/framework/CHANGELOG.md
   [8]: https://github.com/php-fig/fig-standards/blob/master/proposed/psr-4-autoloader/psr-4-autoloader.md
   [9]: https://github.com/squizlabs/PHP_CodeSniffer
   [10]: https://github.com/fabpot/PHP-CS-Fixer
   [11]: http://breachattack.com/
   [12]: http://www.yiiframework.com/doc-2.0/guide-rest.html
   [13]: https://github.com/yiisoft/yii2-codeception
   [14]: http://www.yiiframework.com/doc-2.0/guide-test-fixture.html
   [15]: https://github.com/yiisoft/yii2-faker
   [16]: http://www.elasticsearch.org/
   [17]: http://redis.io
   [18]: http://sphinxsearch.com/docs/
   [19]: https://www.mongodb.org/
   [20]: https://github.com/yiisoft/jquery-pjax
   [21]: https://github.com/yiisoft/yii2/graphs/contributors
  
