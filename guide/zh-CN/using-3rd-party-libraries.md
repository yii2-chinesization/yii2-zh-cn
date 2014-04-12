使用第三方库
=========================

Yii 设计周密，容易整合第三方库以进一步扩展 Yii 的功能。
TODO: 命名空间和包管理器 composer 的介绍

在第三方系统使用 Yii
------------------------------

Yii 也能以独立自足的库来支持发展和增强已有的第三方系统，如 WordPress, Joomla等。要这样做，在第三方系统的引导文件引入（include）以下代码即可：

```php
$yiiConfig = require(__DIR__ . '/../config/yii/web.php');
new yii\web\Application($yiiConfig); // 不要运行 'run()' !
```

以上代码非常类似典型 Yii 应用所使用的引导代码，除了：在创造 Web 应用实例后不调用 `run()` 方法。

现在当开发和增强第三方时可以使用 Yii 提供的大多数特性。例如，使用 `Yii::$app` 来访问应用实例；使用数据库特性如活动记录；使用模型和验证特性；等等。

 Yii2 和 Yii1 混合使用
--------------------

Yii2 能和 Yii1 共同用于同一个项目。既然 Yii2 使用了命名空间形式的类名，所以不会和 Yii1 的任何类冲突。然而确实有一个类在 Yii1 和 Yii2 使用的名字都是一样的，即 'Yii' 。为同时使用 Yii1 和 Yii2，必须解决这个冲突。为解决此冲突，必须定义你自己的 'Yii' 类，把 1.x 和 2.x 的 'Yii' 内容整合到你自己的 'Yii' 类。

使用 composer 包管理器时需要添加以下代码到你的 composer.json 文件以便添加 yii 两个版本的代码到你的项目中：

```json
"require": {
    "yiisoft/yii": "*",
    "yiisoft/yii2": "*",
},
```

从定义你的[[yii\BaseYii]]子类开始:

```php
$yii2path = '/path/to/yii2';
require($yii2path . '/BaseYii.php');

class Yii extends \yii\BaseYii
{
}

Yii::$classMap = include($yii2path . '/classes.php');
```

现在我们有一个类，适合 Yii2 ，但会引起 Yii1 的致命错误。所以，首先需要引入 Yii1 的 `YiiBase` 源码到我们的 'Yii' 类定义文件：

```php
$yii2path = '/path/to/yii2';
require($yii2path . '/BaseYii.php'); // Yii 2.x
$yii1path = '/path/to/yii1';
require($yii1path . '/YiiBase.php'); // Yii 1.x

class Yii extends \yii\BaseYii
{
}

Yii::$classMap = include($yii2path . '/classes.php');
```

以上代码定义了所有必须的常量和 Yii1 的自动加载器。现在需要添加 Yii1 的 `YiiBase` 所有属性和方法到我们的 'Yii' 类。不幸的是，没有好的方式添加只能在 'Yii' 类内复制粘贴 Yii1 的 `YiiBase` 内容：

```php
$yii2path = '/path/to/yii2';
require($yii2path . '/BaseYii.php');
$yii1path = '/path/to/yii1';
require($yii1path . '/YiiBase.php');

class Yii extends \yii\BaseYii
{
    public static $classMap = [];
    public static $enableIncludePath = true;
    private static $_aliases = ['system'=>YII_PATH,'zii'=>YII_ZII_PATH];
    private static $_imports = [];
    private static $_includePaths;
    private static $_app;
    private static $_logger;

    public static function getVersion()
    {
        return '1.1.15-dev';
    }

    public static function createWebApplication($config=null)
    {
        return self::createApplication('CWebApplication',$config);
    }

    public static function app()
    {
        return self::$_app;
    }

    //  \YiiBase 剩下的内部代码位于此
    ...
}

Yii::$classMap = include($yii2path . '/classes.php');
Yii::registerAutoloader(['Yii', 'autoload']); //通过 Yii1 注册 Yii2 自动加载器
```

注意：复制方法时 *不应该* 复制 "autoload()"方法!也应避免复制 "log()", "trace()", "beginProfile()", "endProfile()" 以防你想用 Yii2 的日志取代 Yii1 的。

现在有了同时符合 Yii 1.x 和 Yii 2.x 的‘Yii’类了，因此你的应用使用的引导代码是这样的：

```php
require(__DIR__ . '/../components/my/Yii.php'); // 引入已建立的 'Yii' 类

$yii2Config = require(__DIR__ . '/../config/yii2/web.php');
new yii\web\Application($yii2Config); // 创建 Yii 2.x 应用

$yii1Config = require(__DIR__ . '/../config/yii1/main.php');
Yii::createWebApplication($yii1Config)->run(); // 创建 Yii 1.x 应用
```

然后在你的项目的任何部分使用```Yii::$app``` 来引用 Yii 2.x 应用，而使用 ```Yii::app()``` 来引用 Yii 1.x 应用。

```php
echo get_class(Yii::app()); // 输出 'CWebApplication'
echo get_class(Yii::$app); // 输出 'yii\web\Application'
```
