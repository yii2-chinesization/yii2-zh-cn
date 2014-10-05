>[原文：http://www.yiiframework.com/news/](http://www.yiiframework.com/news/80/yii-2-0-rc-is-released/)  
主翻译：@qiansen1386(东方孤思子) 校对： 时间：2014年9月29

Yii 2.0 RC 发布啦！
=================

我们非常高兴地宣布：Yii 2.0 RC 版（发行候选版本）终于发布了！你可以参照
[yiiframework.com](http://www.yiiframework.com/download/) 
页面的说明来安装或升级。

该 RC 发布包含约 100 项左右的 bug 修复，以及 200 个新功能及改进。它包含了自
[Beta 版](yii-2-0-beta-is-released.md) 至今 5 个月来集中开发的成果。在这个过程中，我们收到了来自 Yii 社区的大量帮助。特此感谢
[为 Yii 做出贡献的所有人](https://github.com/yiisoft/yii2/graphs/contributors)，感谢你们让这次发布成为现实，你们是最棒的！

常见问题
-------

-   **2.0 RC 意味着什么？** 
    RC 是发行候选版本的意思。它是在 GA 
    （General Availability，正式发布的版本）前，最后一个开发发布。GA 前的工作，主要是少量反馈问题的修复，以及改善文档。

-   **2.0 GA 什么时候发布？** 
    这取决于我们从 RC 发布后所收集的问题反馈。我们有一个暂定的计划是，如果 RC 
    版本最终被证明足够稳定，则将会在大约两周左右发布 2.0 GA。

-   **我能在我的项目里使用 RC 么？** 不仅能，而且我们非常推荐你在你的新项目中试用它，并给我们你的使用反馈。不过，因为 2.0 GA 近在眼前了，所以我们建议你不要在已有的生产环境中使用它。因为我们无法确定是否会有新的不兼容修改出现，尽管这个可能性非常小。

-   **2.0 有什么文档么？**
    当然，我们有
    [官方指南](http://www.yiiframework.com/doc-2.0/guide-README.html)
    ，它包含与 Yii 2.0 相关的综合而且有深度的各种教程。还有 
    [API 文档](http://www.yiiframework.com/doc-2.0/)它是用来查阅框架中某个类库的具体用法
    （译者注：也要关注我们的[文档中文化项目](https://github.com/yii2-chinesization/yii2-zh-cn/)呦！）

-   **怎么把我的项目从 1.1 升级到 2.0？**
    请参考 
    [从 1.1 升级](http://www.yiiframework.com/doc-2.0/guide-intro-upgrade-from-v1.html) 这篇文章。
    请注意，因为 2.0 相较于 1.1 是完全重构的，所以这种升级所需的改动不会太小。若你的 1.1 应用正在稳定运行，我们建议你继续使用 
    1.1，除非你有足够的时间和资源进行这种升级。

-   **我怎么从2.0 alpha 或 beta 升级过来** 
    请参考
    [UPGRADE](https://github.com/yiisoft/yii2/blob/2.0.0-rc/framework/UPGRADE.md) 的说明。

-   **我怎样了解2.0开发的最新动态？** 
    Yii 2.0 的开发活动都在 GitHub 上：
    [https://github.com/yiisoft/yii2](https://github.com/yiisoft/yii2)。
    你可以关注（watch）或标星（star）这个项目来接收开发动态。你也可以订阅我们的 Twitter 更新
    [https://twitter.com/yiiframework](https://twitter.com/yiiframework)（译者注：前提是你会翻墙有梯子）
    或加入我们的 [Facebook 小组](https://www.facebook.com/groups/yiitalk/)。

Yii 2.0 RC 的主要改动
----------------------------

此次发布，包含了很多有用的更新和改动。而在下文中我们只会总结其中最重要的几个。你可以在[更新日志（CHANGELOG）](https://github.com/yiisoft/yii2/blob/2.0.0-rc/framework/CHANGELOG.md)查看完整的改动列表。
如果想要了解你能用 Yii 2.0 干什么，请阅读 [权威指南](http://www.yiiframework.com/doc-2.0/guide-README.html)。

### 安全

一些安全专家，包括 [Tom Worster（汤姆·沃斯特）](https://github.com/tom--) 以及 [Anthony
Ferrara（安东尼·法拉利）](https://github.com/ircmaxell)，已经帮助我们在安全方面审查了 Yii 的代码，并提供给我们了一些重要的反馈，告诉我们如何综合提升 Yii 的安全性。Tom 甚至帮助我们重写了一些安全代码，结果是更牛逼的密钥的生成与加密实现，提供了对时序攻击的保护，以及很多其他的黑科技。

为了更好地支持对一些安全功能的自定义，我们已经把之前的 `Security` 助手类，改为了 `security` 应用组件。因此，你可以用这样的 
`Yii::$app->security->encrypt()` 的代码访问安全相关的功能。

我们也做了一些其他小一点的重要改动，来进一步提升 Yii 的安全性。比如，所有 cookies 的 `httpOnly` 现在是默认打开的；CSRF 令牌，现在可以存到 sessions 而不是 cookies 中，如果你设置 `yii\web\Request::enableCsrfCookie` 为 false。

### 数据库

#### 数据库复制（主从分离）与读写分离

Yii 现在提供内建的数据库复制与读写分离的支持。有了数据库复制，数据会从一个所谓的**主服务器**复制到**从服务器**。所有的写入与更新都会发生在主服务器，而读取的操作会发生在从服务器。要使用这个功能，只需简单地配置你的数据连接为一下形式：

```php
[
    'class' => 'yii\db\Connection',

    // 主服务器的配置
    'dsn' => '主服务器的 dsn',
    'username' => 'master',
    'password' => '***',

    // 从服务器的通用配置
    'slaveConfig' => [
        'username' => 'slave',
        'password' => '***',
    ],

    // 从服务器配置列表
    'slaves' => [
        ['dsn' => '从服务器 1 的 dsn'],
        ['dsn' => '从服务器 2 的 dsn'],
        ['dsn' => '从服务器 3 的 dsn'],
    ],
]
```

有了这些配置，你可以照常像以前一样写 DB 查询。只要查询是从数据库中提取数据，其中一个从数据库会自动执行这个查询（一个简单的负载均衡算法会用于确定从服务器的选择）；如果查询是更新或插入数据，则会使用主服务器。

#### 事务

这里有很多有关数据库事务的改进。

首先，你可以用一种回调函数的风格来使用事务，像这样：

```php
$connection->transaction(function() {
    $order = new Order($customer);
    $order->save();
    $order->addItems($items);
});
```

它等效于下面这段更长的代码：

```php
$transaction = $connection->beginTransaction();
try {
    $order = new Order($customer);
    $order->save();
    $order->addItems($items);
    $transaction->commit();
} catch (\Exception $e) {
    $transaction->rollBack();
    throw $e;
}
```

其次，事务会触发几条事件。比如，开始新事务时 DB connecton 组件会触发 `beginTransaction` 事件；当事务成功提交时会触发 
`commitTransaction`。当使用事务时，你就可以响应这些事件，来进行一些预处理或后加工的任务。

最后，你可以在开始一个新事务时，设置事务隔离级别（比如，`READ COMMITTED`）。比如，

```php
$transaction = $connection->beginTransaction(Transaction::READ_COMMITTED);
```

#### 构建查询条件

当你构建一个查询条件的时候，你可以使用任意操作符。在下面的例子里，操作符 `>=` 被用来构建一个查询条件 `
age >= 30`。Yii 会正确地引用字段名，并用参数绑定功能来处理值。

```php
$query = new \yii\db\Query;
$query->where(['>=', 'age', 30]);
```

当构建 `in` 或 `not` 条件时，你可以使用子查询，像这样：

```php
$subquery = (new \yii\db\Query)
       ->select('id')
       ->from('user')
       ->where(['>=', 'age', 30]);
   
// 读取所有由 30 岁以上的客户下达的订单  
$orders = (new \yii\db\Query)
   ->from('order')
   ->where(['in', 'customer_id', $subquery])
   ->all();
```


### 前端资源管理

Yii 支持使用 [Bower](http://bower.io/) 或 [NPM](https://www.npmjs.org/)
的包。它使用牛逼的 [Composer Asset Plugin（Composer 前端资源管理插件）](https://github.com/francoispluchino/composer-asset-plugin)
来通过 Composer 的接口管理 Bower/NPM 包（如，jQuery，jQuery UI，Bootstrap 等）的依赖关系。

因为这个改变，他现在需要你先给 Composer 安装这个插件。你可以通过（在你开始安装或升级 Yii 2.0 RC 之前）运行一下指令实现（一劳永逸）：

```php
php composer.phar global require "fxp/composer-asset-plugin:1.0.0-beta2"
```

现在如果你运行这个指令，你就可以安装 jQuery Bower 包到 `vendor` 目录：

```php
php composer.phar require bower-asset/jquery:2.1.*
```

请参考 [与 Asset 有关的权威指南](http://www.yiiframework.com/doc-2.0/guide-structure-assets.html)
综合了解关于前端资源管理的更多细节。

### 格式化数据

我们深度重构了数据格式化类，并且之前的 `yii\base\Formatter` 与 `yii\i18n\Formatter` 重构为了一个 
`yii\i18n\Formatter` 类。新的 Formatter 类提供了一个统一的接口，而与是否开启 PHP intl 
扩展无关。如果扩展没有安装，他会很好地回滚为不进行国际化的数据格式化。

我们也统一了指定日期与时间格式的方式，通过
[ICU 格式](http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax)。诸如 `DateValidator` 以及 
JUI 的 `DatePicker` 等类都默认使用该格式。然而，你依旧可以使用 PHP 日期格式，只要添加 `php:` 前缀。比如，

```php
$formatter = Yii::$app->formatter;
$value = time();
echo $formatter->asDate($value, 'MM/dd/yyyy'); // 等效于 date('m/d/Y', $value)
echo $formatter->asDate($value, 'php:Y/m/d');  // 等效于 date('Y/m/d', $value)
echo $formatter->asDate($value, 'long');       // 等效于 date('F j, Y', $value)
```

### 表单

`ActiveForm` 的 JavaScript 代码进行了一系列的改进。

区别于之前在客户端验证过程中使用回调函数注入代码的过程，现在改为触发一系列的事件。你可以更容易地编写响应这些事件的 JS 代码。比如，

```php
$('#myform').on('beforeValidate', function (event, messages, deferreds) {
    // 当提交按钮被点击后，在验证整张表单之前被调用
    // 你可以在此进行一些自定义的验证过程
});

$('#myform').on('beforeSubmit', function () {
    // 在所有验证都通过后，且表单应该被提交时调用
    // 你可以在此处执行 AJAX 的表单提交。请确保你返回 false 以阻止传统的表单提交过程继续执行。
});
```

延时验证（Deferred validation）也被支持了。在上面的例子中，`beforeValidate` 事件里的 `deferreds` 参数允许你添加新的 Deferred 
对象。通过对延时验证的支持，`FileValidator` 与 `ImageValidator` （文件与图片验证器）现在也支持客户端验证功能了。

 `ActiveForm` 的 JavaScript 代码中的很多方法现在已经暴露出来，这样你可以更加容易的在客户端环境中构建动态表单，并支持对动态生成的输入控件执行验证。比如，以下 JS 代码可用于给新创建的 "address" 输入框添加验证功能：

```php
$('#myform').yiiActiveForm('add', {
    'id': 'address',
    'name': 'address',
    'container': '.field-address',
    'input': '#address',
    'error': '.field-address .help-block'
});
```

### 日志 及 错误处理

现在，你可以使用**数组**或**对象**作为日志消息了。默认的日志目标会自动把它们转换为文本显示；而你自定义的日志目标可以更加特殊化地处理这些复杂数据。

InvalidCallException，InvalidParamException，UnknownMethodException 现在继承自 SPL（标准 PHP 类库）的 BadMethodCallException
以使异常的层级结构更符合逻辑。

异常的显示也进行了改进，新添了对堆栈跟踪方法类中参数的显示。（保留原文：Exception display is improved by showing the arguments in the stack trace method class.求校对人员帮忙改进）

### 开发工具

Yii 的调试器（Debugger）对于显示当前 Yii 应用运行时的调试信息而言是很有用的工具、我们添加了一个新的调试面板以展示当前加载的前端资源包，及它们的内容。

Yii 的代码生成器 Gii 现在可以使用命令行指令调用了！之前，它只提供一个 Web 图形界面，同样非常直观且好用，但它不受一些“睾丸（高端玩家/硬核玩家）”的喜爱。现在应该每个人都开心了。更重要的是，你依旧可以照常创建自定义的 Gii 生成器，而且他可以同时在 Web 和命令行模式中调用，而无需其他任何额外的修改。

要在命令行模式中试用 Gii，只需执行以下命令：

```bash
# 更改当前路径为你应用的基目录
cd path/to/AppBasePath

# 显示 Gii 的帮助信息
yii help gii

# 显示 Gii 中的模型生成器的帮助信息
yii help gii/model

# 根据 city 表生成 City 模型
yii gii/model --tableName=city --modelClass=City
```

### 行为（Behaviors）

我们添加了一个新的行为 `yii\behaviors\SluggableBehavior`，它可以用直译并调整过的数据填充模型特性，比如说可用于直译 URLs。你可以这样使用它：

```php
use yii\behaviors\SluggableBehavior;

public function behaviors()
{
   return [
       [
           'class' => SluggableBehavior::className(),
           'attribute' => 'title',
           // 'slugAttribute' => 'alias',   // 存储 slug 到 "alias" 字段
           // 'ensureUnique' => true,       // 确保生成不重复的 slugs
        ],
    ];
}
```

行为现在可以匿名定义并配属，比如，

```php
$component->attachBehaviors([
    'myBehavior1' => new MyBehavior,  // 命名的行为
    MyBehavior::className(),          // 匿名的行为
]);
```

### 模版引擎

Smarty 和 Twig 的视图渲染器都得到了显著的提升。针对许多 Yii 的概念引入了许多特殊语法，并且我们已经收到了反馈，这些新的语法可以帮助开发者像使用普通 PHP 模版一样高效地操作 Smarty 与 Twig。要了解更多，请查阅 [权威指南](http://www.yiiframework.com/doc-2.0/guide-tutorial-template-engines.html)。
