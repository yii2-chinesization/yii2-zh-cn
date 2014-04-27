控制台应用程序
====================

Yii完美支持控制台（译者注：控制台即win 下的 cmd（命令行）或 Unix 类 OS 中的（bash、terminal）终端，后者在 VPS 等环境中更常用一些。）应用，
其在 Yii 中的结构与 Yii 的 web 应用非常相似。一个控制台应用，包括一个或多个 [[yii\console\Controller]]（通常在控制台环境中被称作“命令”）。
如 Web Controller 一样，每一个 [[yii\console\Controller]] 也可以包含有一个或多少的动作。

用法
-----

使用下面的语法来执行控制台 Controller 的动作：

```
yii <route> [--option1=value1 --option2=value2 ... argument1 argument2 ...]
```

例如， [[yii\console\controllers\MigrateController::actionCreate()|MigrateController::actionCreate()]]
中的 [[yii\console\controllers\MigrateController::$migrationTable|MigrateController::$migrationTable]] 属性可以用下面的方法来设置：

```
yii migrate/create --migrationTable=my_migration
```

上面的 `yii` 是控制台应用程序的入口脚本

入口脚本
------------

入口脚本之于控制台应用，一如 `index.php` 引导文件之于 Web 应用。
控制台入口脚本一般被称为 `yii`， 它通常被放置于应用的根目录中（比如，`web`）。
控制台应用入口脚本所包含的代码主体如下：

```php
#!/usr/bin/env php
<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);

// fcgi doesn't have STDIN and STDOUT defined by default
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/config/console.php');

$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);

```

这个脚本会被创建为你应用程序的一部分；你可以自由地调整它以满足你的需求。
如果你不想在发生错误时看到一大段堆栈跟踪报错或者只是想单纯地提高总体性能，请将 `YII_DEBUG` 常量设置为 `false`。
为了能提供更友好的开发环境，在 basic 和 advanced 两个应用程序模板中，debug 选项默认是开启的。

配置
-------------

从上面的代码中可以看出，控制台应用程序使用它自己的配置文件，名为 `console.php`。
在这个文件中，你可以为控制台应用特别地配置一些的应用组件及其属性。

如果您的 Web 应用和控制台应用共享了很多配置和参数，
你可以考虑将公用的部分提出来，放到一个单独的文件中，并在两个应用（web和console）的配置中，都包含进这个文件。
你可以在 "advanced" 应用模板中，看到一个具体的例子。

有时候，你可能希望运行一个使用，和入口脚本中指定的不一样的配置文件的控制台命令。
比如，你可能想使用 `yii migrate` 命令在每个单独的测试套件中，更新不同的数据库数据。
要想动态地改变应用配置，你只需简单地通过 `appconfig` 选项，在命令执行时，指定一个自定义的应用配置文件即可：

```
yii <route> --appconfig=path/to/config.php ...
```


创建自己的控制台命令
----------------------------------

### 控制台的控制器和动作

定义一个控制台命令需要继承自 [[yii\console\Controller]] 类。在控制器类中，
你可以定义一个或多个动作，这些动作分别对应这个控制器的各个子命令。
在每一个动作中，你可以写入那些可以实现该子命令的目标的代码。

当运行一个命令时，你需要指定到对应的控制器的动作的路由。
例如，路由 `migrate/create` 对应指向 [[yii\console\controllers\MigrateController::actionCreate()|MigrateController::actionCreate()]] 动作的子命令。
如果路由没有提供Action ID，则会执行默认动作。（与 Web 控制器一样）

### 设置选项

通过重写 [[yii\console\Controller::options()]] 方法，你可以指定一个控制台命令(controller/actionID)的设置选项（简称，选项）。
该方法应该返回，该控制器的所有公共属性变量的名称列表。当运行一个命令时，你可以使用 `--OptionName=OptionValue` 语法指定一个选项的值。
这将设置控制器类的 `OptionName` 属性的 `OptionValue` 到控制器类的 `OptionName` 属性。

如果一个选项的默认值是数组类型的，那么你如果在运行该命令设置此选项，请输入由逗号分割的字符串，它将在之后被拆分为一个数组。

### 参数

除了选项，命令也可以接收参数。该参数将被传递到所请求的动作方法的参数中。第一个参数对应于动作的第一参数，第二对应于第二，以此类推。
如果没有提供足够的参数，则相应的参数将使用预先定义的默认值；如果也没有定义任何默认值，则命令会报错退出。

你可以使用 `array` 类型的提示，以表明参数应该是一个数组。数组类型参数请输入由逗号分割的字符串。

下面的示例展示了如何声明参数：

```php
class ExampleController extends \yii\console\Controller
{
	// 这个命令 "yii example/create test" 将调用 "actionCreate('test')"
	public function actionCreate($name) { ... }

	// 这个命令 "yii example/index city" 将调用 "actionIndex('city', 'name')"
	// 这个命令 "yii example/index city id" 将调用 "actionIndex('city', 'id')"
	public function actionIndex($category, $order = 'name') { ... }

	// 这个命令 "yii example/add test" 将调用 "actionAdd(['test'])"
	// 这个命令 "yii example/add test1,test2" 将调用 "actionAdd(['test1', 'test2'])"
	public function actionAdd(array $name) { ... }
}
```


### 退出代码

使用退出代码是控制台应用程序开发的最佳实践。通常，一个命令返回 `0` 说明一切都 OK。
如果它是一个大于零的数，可以理解为存在一个错误。返回的数值会是错误代码，它可能在发现错误详情方面会有用。
例如，`1` 一般可以代表一个未知的错误，用其他大于1的代码来代指一些特定的错误：输入错误，文件丢失等等。

为了使你的控制台命令返回一个退出代码，你只需在控制器动作中返回一个整数：

```php
public function actionIndex()
{
	if (/* 一些问题 */) {
		echo "发生一个问题!\n";
		return 1;
	}
	// 做一些事情
	return 0;
}
```
