Yii 的基本概念
=====================


Component and Object（组件和对象）
--------------------

Yii框架的类通常扩展自两个基类 [[yii\base\Object]] 和 [[yii\base\Component]] 中的一个。
这些类提供的有用特性都会自动被扩展自他们的子类所继承。

[[yii\base\Object|Object]] 类提供[配置与 *property* 功能](../api/base/Object.md)。
而[[yii\base\Component|Component]]类则扩展自[[yii\base\Object|Object]]类，并添加了
[事件处理](events.md) 和 [行为](behaviors.md)特性.

[[yii\base\Object|Object]]类通常用于表示基础数据结构
而[[yii\base\Component|Component]]类则是应用程序组件或实现其他更高逻辑的类。


对象的配置
--------------------

[[yii\base\Object|Object]]类引入了一个统一的配置对象的方式。
任何[[yii\base\Object|Object]]的子类（扩展类）应该覆盖它的构造函数（如果需要的话），
以使它可以被正确配置：

```php
class MyClass extends \yii\base\Object
{
    public function __construct($param1, $param2, $config = [])
    {
        // ... 在配置生效前的初始化

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();

        // ... 在配置生效后初始化
    }
}
```

在上面的例子中，构造函数的最后一个参数
必须是一个包含相应键值对的数组，来初始化构造函数最后面的属性。
在配置生效后，仍可以通过覆盖`init()`方法，来进行初始化工作
。

根据该约定，
你可以使用下面的配置数组(configuration array)来创建和配置一个新的对象：

```php
$object = Yii::createObject([
    'class' => 'MyClass',
    'property1' => 'abc',
    'property2' => 'cde',
], [$param1, $param2]);
```


路径别名（Path Aliases）
------------

Yii 2.0扩展了路径别名的用法，以同时应用于文件/目录的路径和 URL。
一个别名必须以`@`字符开始，以便区别于传统的文件/目录路径或URL。
举个栗子，别名`@yii`指的是Yii的安装目录，而`@web`表示的是当前运行app的base URL。
几乎所有的 Yii 核心代码内部都支持路径别名。例如，
`FileCache::cachePath`可以采取两种参数，路径别名或正常的目录路径都可以。

路径别名和类的命名空间也是密切相关的。
建议将每一个根命名空间定义为路径别名，这让你可以使用Yii的类文件自动加载器
而不需要进行任何配置。例如，由于 `@yii` 指向 Yii 的安装目录，像 `yii\web\Request` 这样的类便可以通过 Yii 自动导入
如果需要用到，如 Zend Framework 这样的第三方类库，
你可以定义一个`@Zend`路径别名，将它指向其安装目录，
这样Yii就能自动导入这个类库中的任何类。

我们的核心框架已经预定义了以下几个路径别名：

- `@yii` - 框架目录。
- `@app` - 当前运行的应用主体的路径（base path）。
- `@runtime` - runtime（运行环境）目录。
- `@vendor` - Composer 的 vendor 文件夹。
- `@webroot` - 当前运行应用的 web 根目录。
- `@web` - 当前运行应用的 base URL（根URL）。

自动加载（Autoloading）
-----------

所有的类、接口和traits（特质）都会在使用它们的时候自动加载。而不需要使用
`include` or `require`。那是因为 Composer 和 Yii 的扩展特性在起作用。

Autoloader (自动加载器)需要遵守 [PSR-4](https://github.com/php-fig/fig-standards/blob/master/proposed/psr-4-autoloader/psr-4-autoloader.md)标准。
这意味，一个命名空间、类、接口以及 trait (特质)的别名的定义，应该对应于其文件系统路径。
除了根命名空间，根命名空间是通过路径别名定义的。

例如，如果标准别名`@app`指向`/var/www/example.com/`，那么`\app\models\User`将从
`/var/www/example.com/app/models/User.php`载入。

要自定义别名，可以使用下面的代码来配置：

```php
Yii::setAlias('@shared', realpath('~/src/shared'));
```

要附加自动加载功能，可以使用标准的PHP`spl_autoload_register`注册。

助手类（Helper Classes）
--------------

助手类通常只包括（并使用）一些静态的方法，比如下面的用法：

```php
use \yii\helpers\Html;
echo Html::encode('Test > test');
```

这里是几个框架所提供的助手类：

- ArrayHelper
- Console
- FileHelper
- Html
- HtmlPurifier
- Image
- Inflector
- Json
- Markdown
- Security
- StringHelper
- Url
- VarDumper