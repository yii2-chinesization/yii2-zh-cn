配置
=============

Yii 应用依靠组件来执行大多数常见任务，如连接数据库、路由浏览器请求和处理会话。这些常备的组件都可以通过 *配置* Yii 应用来调整其表现。
多数组件缺省设置是合理的，不一定需要你做非常多的配置。但仍有一些必要的配置需要你完成，如数据库连接。
应用如何配置取决于使用的应用模板，但有一些共同原则适用于所有 Yii 应用。

引导文件的配置选项
-----------------------------------------

Yii 的每个应用都有至少一个引导文件：即一个用于处理所有请求的 PHP 脚本。每个Web应用的引导文件通常是 `index.php`；
控制台应用的引导文件是 `yii`。两个引导文件执行几乎相同的工作：

1. 设置通用常量。
2. 导入 Yii 框架本身。
3. 导入 [Composer 的自动加载器（autoloader）](http://getcomposer.org/doc/01-basic-usage.md#autoloading).
4. 读取配置文件到 `$config` 变量。
5. 创建一个新的应用实例，通过 `$config` 来配置并运行这个实例。

如同你 Yii 应用中的每一个资源一样，引导文件也可以被修改来满足你的需求。一个典型的改变就是更改 `YII_DEBUG` 的值。这个常量应该在开发阶段被设定为 `true`，但是在生产环境下，始终为 `false`。

若你没有特殊指定，则默认的引导结构会设置 `YII_DEBUG` 为 `false`：

```php
defined('YII_DEBUG') or define('YII_DEBUG', false);
```

在开发阶段，你可以更改它为 `true`：

```php
define('YII_DEBUG', true); // 仅开发阶段使用 
defined('YII_DEBUG') or define('YII_DEBUG', false); //生产环境使用
```

配置应用实例
------------------------------------

当一个应用实例在引导脚本中被创建时，它就会被配置。这些配置通常被
存储在`/config` 文件夹里的一个PHP文件中，

```php
<?php
return [
    'id' => 'applicationId',
    'basePath' => dirname(__DIR__),
    'components' => [
        //应用组件的配置放在这里……
    ],
    'params' => require(__DIR__ . '/params.php'),
];
```

配置是一个超大的键值对数组。在上面的代码中，数组的键是应用属性的名字，取决于应用的类型，
你可以配置  [[yii\web\Application]] 或 [[yii\console\Application]] 中的类的属性

请注意，你不仅仅可以配置public（公共）的类属性，也可以通过setter（设值函数）来访问其所有的属性。比如，
  你可以用一个名为 `runtimePath` 的键值对，来配置runtime（运行环境）的路径。应用类中并没有名为runtime的属性，
  但类中一个对应的setter叫做 `setRuntimePath`，这样 `runtimePath`就成为了可以被设置的选项。
  任何扩展自  [[yii\base\Object]]的类都拥有通过 setters 来配置其属性的能力，而这包括了Yii框架中几乎所有的类。

配置应用的组件
----------------------------------

绝大多数 Yii 的功能性体现在其应用组件的身上。这些组件可以通过配置应用的 `components` 属性，来附加到该应用实例中：

```php
<?php
return [
    'id' => 'applicationId',
    'basePath' => dirname(__DIR__),
    'components' => [
        'cache' => ['class' => 'yii\caching\FileCache'],
        'user' => ['identityClass' => 'app\models\User'],
        'errorHandler' => ['errorAction' => 'site/error'],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    // ...
];
```

在上面的代码中，配置了四个组件：`cache`, `user`, `errorHandler`, `log`。每一个键值对的键是组件 ID。值是一个用于设置组件属性的子数组。组件 ID 同样也被用于在应用的其他位置访问该组件，像是这样：`\Yii::$app->myComponent`.

配置数组具有一个特殊键，命名为 `class`，用于标识该组件的基类。键值对的剩余的其余部分被用于
配置组件的属性，方式与配置应用的属性相同，也是以键值对的形式。

每一个应用都会预先定义一系列的组件。在配置这些中的一个时，若使用 Yii 为其提供的默认类，则 `class` 这个键可以被省略。你也可以看下应用的 `coreComponents()` 方法，
来详细看一下有哪些预定义的组件ID，和他们所对应的类。

请注意Yii会聪明地在这个组件被确实使用的时候才配置他们，举例来说，如果你在配置脚本中提供了 `cache` 组件的配置信息，但在后续代码中并没有使用这个组件，Yii不会浪费时间去配置这个还没有初始化的组件。

设置组件的默认类
------------------------------------

你可以给每一个组件都指定其所用的默认类。比如，如果你想替换掉所有被使用的 `LinkPager` 小部件的类，
你无需在每一次它被使用的时候都指定一次，你只需这样：

```php
\Yii::$container->set('yii\widgets\LinkPager', [
    'options' => [
        'class' => 'pagination',
    ],
]);
```

上面的这段代码应该放置在 `LinkPager` 小部件被使用之前。可以放置在入口脚本 `index.php`中，
应用配置文件中，或者其他什么地方。
