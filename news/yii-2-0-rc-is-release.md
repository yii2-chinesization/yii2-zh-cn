[原文：http://www.yiiframework.com/news/](http://www.yiiframework.com/news/80/yii-2-0-rc-is-released/)  
主翻译：@qiansen1386(东方孤思子) 校对： 时间：2014年9月29

# Yii 2.0 RC 发布啦！

我们非常高兴地宣布：Yii 2.0 RC 版（发行候选版本）终于发布了！你可以参照
[yiiframework.com](http://www.yiiframework.com/download/) 
页面的说明来安装或升级。

该 RC 发布包含约 100 项左右的 bug 修复，以及 200 个新功能及改进。它包含了自
[Beta 版](yii-2-0-beta-is-released.md) 至今 5 个月来集中开发的成果。在这个过程中，我们收到了来自 Yii 社区的大量帮助。特此感谢
[为 Yii 做出贡献的所有人](https://github.com/yiisoft/yii2/graphs/contributors)，感谢你们让这次发布成为现实，你们是最棒的！

常见问题
-------

-   **2.0 RC 意味着什么？** RC means Release Candidate. It is the
    last development release before the GA (General Availability)
    release. The remaining work for us to release GA mainly include
    minor issue fixing and documentation.

-   **2.0 GA 什么时候发布？** It depends on the feedback we
    receive about this RC release. We have a tentative plan to release
    2.0 GA in about two weeks, if the RC version is proven to be stable
    enough.

-   **我能在我的项目里使用 RC 么？** Yes, and we strongly recommend you
    to try it out in your new projects and give us feedback about it. As
    2.0 GA is just around the corner, we suggest you do not use RC for
    production use because we may still introduce breaking changes, even
    though this possibility is very small.

-   **2.0 有什么文档么？**  当然，我们有
    [官方指南](http://www.yiiframework.com/doc-2.0/guide-README.html)
    ，它包含与 Yii 2.0 相关的综合而且有深度的各种教程。还有 
    [API 文档](http://www.yiiframework.com/doc-2.0/)它是用来查阅框架中某个类库的具体用法
    （译者注：也要关注我们的[文档中文化项目](https://github.com/yii2-chinesization/yii2-zh-cn/)呦！）

-   **怎么把我的项目从 1.1 升级到 2.0？** Please
    refer to [Upgrading from Yii
    1.1](http://www.yiiframework.com/doc-2.0/guide-intro-upgrade-from-v1.html).
    Note that since 2.0 is a complete rewrite of 1.1, the upgrade will
    not be trivial. If your application in 1.1 is already running
    stably, we suggest you keep using 1.1 unless you have enough time
    and resource to do the upgrade.

-   **我怎么从2.0 alpha 或 beta 升级过来** Please follow the
    instructions in
    [UPGRADE](https://github.com/yiisoft/yii2/blob/2.0.0-rc/framework/UPGRADE.md).

-   **我怎样了解2.0开发的最新动态？?** All development activities
    of Yii 2.0 occur on GitHub:
    [https://github.com/yiisoft/yii2](https://github.com/yiisoft/yii2).
    You may watch or star this project to receive development updates.
    You may also follow our Twitter updates at
    [https://twitter.com/yiiframework](https://twitter.com/yiiframework)
    and/or join our [Facebook
    Group](https://www.facebook.com/groups/yiitalk/).

Yii 2.0 RC 的主要改动
----------------------------

In this release, we have included many useful features and changes.
Below we summarize some of the most important ones. Complete list of
changes in this release can be found in
[CHANGELOG](https://github.com/yiisoft/yii2/blob/2.0.0-rc/framework/CHANGELOG.md).
Please read [the Definitive
Guide](http://www.yiiframework.com/doc-2.0/guide-README.html) if you
want to learn what you can do with Yii 2.0 in general.

### 安全

Several security experts, including [Tom
Worster](https://github.com/tom--) and [Anthony
Ferrara](https://github.com/ircmaxell), have helped review the Yii code
about its security aspect and left many important feedbacks on how to
improve the security of Yii in general. Tom even helped us rewrite some
of the security code, which results in better key generation and
encryption, protection from timing attacks, and many other things.

To support customization of some security features, we have turned the
previous `Security` helper class into the `security` application
component. As a result, you can access security-related features through
expressions such as `Yii::$app->security->encrypt()`.

We have also made several other minor yet important changes to further
improve the security of Yii. For example, `httpOnly` is now turned on
for all cookies by default; CSRF tokens can be stored in sessions
instead of cookies if you set `yii\web\Request::enableCsrfCookie` to be
false.

### 数据库

#### Database Replication and Read-Write Splitting

Yii now has built-in support for database replication and read-write
splitting. With database replication, data are replicated from the
so-called *master servers* to *slave servers*. All writes and updates
must take place on the master servers, while reads may take place on the
slave servers. To use this feature, simply configure your DB connection
like the following:

```php
[
    'class' => 'yii\db\Connection',

    // configuration for the master
    'dsn' => 'dsn for master server',
    'username' => 'master',
    'password' => '',

    // common configuration for slaves
    'slaveConfig' => [
        'username' => 'slave',
        'password' => '',
    ],

    // list of slave configurations
    'slaves' => [
        ['dsn' => 'dsn for slave server 1'],
        ['dsn' => 'dsn for slave server 2'],
        ['dsn' => 'dsn for slave server 3'],
    ],
]
```
With this configuration, you can continue writing DB query code as
usual. If a query is fetching data from database, one of the slaves will
be used automatically to perform the query (a simple load balancing
algorithm is implemented regarding slave selection); if the query is
updating or inserting data into database, the master will be used.

#### 事务

There are several enhancements about using DB transactions.

First, you can now work with transactions in a callback style like the
following:

```php
$connection->transaction(function() {
    $order = new Order($customer);
    $order->save();
    $order->addItems($items);
});
```

This is equivalent to the following lengthy code:

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

Second, a few events are triggered for transactions. For example, a
`beginTransaction` event is triggered by a DB connection when you start
a new transaction; and a `commitTransaction` event is triggered when the
transaction is successfully committed. You can respond to these events
to perform some preprocessing and post-processing tasks when using
transactions.

Last, you can set transaction isolation levels (e.g. `READ COMMITTED`)
when starting a new transaction. For example,

```php
$transaction = $connection->beginTransaction(Transaction::READ_COMMITTED);
```

#### 构建查询条件

You can use arbitrary operators when building a query condition. In the
following example, the operator `>=` is used to build a query condition
`age >= 30`. Yii will properly quote the column name and use parameter
binding to handle the value.

```php
$query = new \yii\db\Query;
$query->where(['>=', 'age', 30]);
```

When building an `in` or `not` condition, you can use sub-queries like
the following:

```php
$subquery = (new \yii\db\Query)
       ->select('id')
       ->from('user')
       ->where(['>=', 'age', 30]);
   
// fetch orders that are placed by customers who are older than 30  
$orders = (new \yii\db\Query)
   ->from('order')
   ->where(['in', 'customer_id', $subquery])
   ->all();
```


### 前端资源管理

Yii is embracing [Bower](http://bower.io/) and
[NPM](https://www.npmjs.org/) packages. It uses the excellent [Composer
Asset Plugin](https://github.com/francoispluchino/composer-asset-plugin)
to manage the dependencies on Bower/NPM packages (e.g. jQuery, jQuery
UI, Bootstrap) through the interface of Composer.

Because of this change, it is required that you install the plugin first
by running the following command (once for all), before you start to
install or upgrade to Yii 2.0 RC:

```php
php composer.phar global require "fxp/composer-asset-plugin:1.0.0-beta2"
```

Now if you run the following command, you will be able to install the
jQuery Bower package under the `vendor` directory:

```php
php composer.phar require bower-asset/jquery:2.1.*
```

Please refer to the [Definitive Guide about
assets](http://www.yiiframework.com/doc-2.0/guide-structure-assets.html)
for more details about asset management in general.

### 格式化数据

We did significant refactoring of the data formatting classes and the
previous `yii\base\Formatter` and `yii\i18n\Formatter` classes into a
single one `yii\i18n\Formatter`. The new formatter class provides a
consistent interface regardless whether the PHP intl extension is
installed or not. If the extension is not installed, it will fallback
nicely to support data formatting without internationalization.

We also unified the way of specifying date and time formats to be the
[ICU
format](http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax).
Classes such as `DateValidator` and the JUI `DatePicker` all use such
format by default. You can, however, still use PHP date format by
prefxing it with `php:`. For example,

```php
$formatter = Yii::$app->formatter;
$value = time();
echo $formatter->asDate($value, 'MM/dd/yyyy'); // same as date('m/d/Y', $value)
echo $formatter->asDate($value, 'php:Y/m/d');  // same as date('Y/m/d', $value)
echo $formatter->asDate($value, 'long');       // same as date('F j, Y', $value)
```

### 表单

Several improvements were made to the JavaScript code for `ActiveForm`.

Instead of using callbacks to inject code during the client-side
validation process, a set of events are triggered. You can easily write
JavaScript code to respond to these events. For example,

```php
$('#myform').on('beforeValidate', function (event, messages, deferreds) {
    // called before validating the whole form when the submit button is clicked
    // You may do some custom validation here
});

$('#myform').on('beforeSubmit', function () {
    // called after all validations pass and the form should be submitted
    // You may perform AJAX form submission here. Make sure you return false to prevent the default form submission.
});
```

Deferred validation is also supported. In the above example, the
`deferreds` parameter for the `beforeValidate` event allows you to add
new Deferred object. Utilizing this deferred validation support,
`FileValidator` and `ImageValidator` both support client-side validation
now.

Several methods in the `ActiveForm` JavaScript code are now exposed so
that you can more easily build dynamic forms on the client and support
validation of input fields that are dynamically created. For example,
the following JavaScript can be used to add validation support for a
newly created input field "address":

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

You can now use arrays or objects as log messages. The default log
targets will automatically convert them into text display; while your
customize log target classes can handle such complex data specially.

InvalidCallException, InvalidParamException, UnknownMethodException are
now extending from SPL BadMethodCallException to make exception
hierarchy more logical.

Exception display is improved by showing the arguments in the stack
trace method class.

### 开发工具

The Yii debugger is a useful tool to show you detailed debug information
when a Yii application runs. We have added a new debugger panel to show
the loaded asset bundles and their content.

The Yii code generator Gii can now be run as a console command!
Previously it only provides a Web interface, which although very
intuitive and easy to use, is not liked by some hardcore users. Now
everyone should be happy. More importantly, you can still create a new
Gii generator as usual, and it can be used in both Web and console modes
without any extra work.

To try Gii in console mode, run the following commands:

```bash
# change path to your application's base path
cd path/to/AppBasePath

# show help information about Gii
yii help gii

# show help information about the model generator in Gii
yii help gii/model

# generate City model from city table
yii gii/model --tableName=city --modelClass=City
```

### 行为（Behaviors）

We have added a new behavior `yii\behaviors\SluggableBehavior` which can
fill the specified model attribute with the transliterated and adjusted
version so that it can be used in URLs. You may use this behavior like
follows,

```php
use yii\behaviors\SluggableBehavior;

public function behaviors()
{
   return [
       [
           'class' => SluggableBehavior::className(),
           'attribute' => 'title',
           // 'slugAttribute' => 'alias',   // store slug in "alias" column
           // 'ensureUnique' => true,       // ensure generation of unique slugs
        ],
    ];
}
```

Behaviors can now be specified and attached anonymously. For example,

```php
$component->attachBehaviors([
    'myBehavior1' => new MyBehavior,  // a named behavior
    MyBehavior::className(),          // an anonymous behavior
]);
```

### 模版引擎

Both Smarty and Twig view renderers have received significant
improvements. Special syntaxes are introduced for many Yii concepts, and
we have received feedback that these new syntaxes allow one to work with
Smarty and Twig as efficiently as normal PHP templates. To learn more
about new syntaxes, please refer to the [Definitive
Guide](http://www.yiiframework.com/doc-2.0/guide-tutorial-template-engines.html).
