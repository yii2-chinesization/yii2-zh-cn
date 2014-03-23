控制台应用程序
====================

Yii完美支持控制台应用。在Yii中控制台应用程序的结构与web应用程序非常相似。它包括一个或多个 [[yii\console\Controller]] （通常被称为命令）。每一个 [[yii\console\Controller]] 
 又有一个或多少操作。

用法
-----

你可以使用下面的语法来执行控制器操作方法：

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

控制台应用程序入口脚本通常叫  `yii`，位置你的应用程序根目录，包含的代码如下：

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

这个脚本是应用程序的一部分，所以你可以自由地调整它。如果你不想在发生错误时看到栈跟踪以提高性能，请将 `YII_DEBUG` 常量设置为 `false`。在 basic 和 advanced 应用程序模板中它是启用的，以便提供更友好的开发环境。

配置
-------------

从上面的代码中可以看出，控制台应用程序使用一个名为`console.php`的配置文件。在这个文件中，你应该指定如何配置需要的应用程序组件和属性。 

如果您的Web应用程序和控制台应用程序共享了很多配置，你可以考虑将共同部分移到一个单独的文件中，并将这个文件在这两个应用程序配置的包含，在 "advanced" 应用程序模板中就是这么做的。

有时候，你可能希望运行一个控制台命令，在入口脚本中指定一个应用程序配置。例如，在每个单独的测试套件中， 你想使用 `yii migrate` 命令升级你的测试数据库。要做到这一点，只需指定自定义应用程序配置，通过 `appconfig` 选项，如下所示，

```
yii <route> --appconfig=path/to/config.php ...
```


创建自己的控制台命令
----------------------------------

### 控制台的控制器和操作方法

定义一个控制台命令需要继承自 [[yii\console\Controller]] 类。在控制器类中，你定义一个或几个对应于此子命令的多种操作。在每一个操作方法中，你写的代码来实现该特定子命令的某些任务。

当运行一个命令时，你需要使用路由指定到对应的控制器的操作方法。例如，路由 `migrate/create` 指定对应的子命令 [[yii\console\controllers\MigrateController::actionCreate()|MigrateController::actionCreate()]] 操作方法。如果路由不包含操作方法ID，则默认操作方法将被执行。

### 选项

通过重写 [[yii\console\Controller::options()]] 方法，你可以指定一个控制台命令(controller/actionID)的可用的选项。该方法应该返回的控制器类的公共属性名称的列表。当运行一个命令时，你可以使用 `--OptionName=OptionValue` 语法指定一个选项的值。这将设置控制器类的 `OptionName` 属性的 `OptionValue`。到控制器类的`OptionName`属性。

如果一个选项的默认值是数组类型的，那么如果你在运行该命令设置此选项，请输入由逗号分割的字符串，它将转换成一个数组。

### 参数

除了选项，命令也可以接收参数。该参数将被传递到请求的操作方法的参数中。第一个参数对应于操作方法的第一参数，第二对应于第二，等等。如果没有足够的参数设置或相应的参数可能需要声明的默认值，如果他们没有默认值的，命令将报错并退出。

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

使用退出代码是控制台应用程序开发的最佳实践。如果一个命令返回 `0` 这意味着一切都OK。如果它是一个大于零的数，我们认为有一个错误，并且返回的数是可以被解释错误代码，以了解错误的详细信息。例如 `1` 一般可以代表一个未知的错误，则所有大于1的代码来指定特定的错误，诸如输入错误，丢失的文件等等。

为了使你的控制台命令返回一个退出代码，你只需在控制器操作方法中返回一个整数：

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
