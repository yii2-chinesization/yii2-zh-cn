
Yii的基本概念
=====================


组件和对象
--------------------

Yii框架的类通常扩展自两个基类[[yii\base\Object]] 和 [[yii\base\Component]]中的一个。
这些类提供的那些不错的特性都会自动添加到扩展自他们的子类。

[[yii\base\Object|Object]]类提供[配置和属性特性](../api/base/Object.md)。
而[[yii\base\Component|Component]]类则扩展自[[yii\base\Object|Object]]，并添加了
[事件处理](events.md) 和 [行为](behaviors.md)特性。

[[yii\base\Object|Object]]通常用于表示基本数据结构的类，而
[[yii\base\Component|Component]]则用于应用程序组件及实现其他更高逻辑的类。


对象的配置
--------------------

[[yii\base\Object|Object]]类引入配置对象的统一方式。任何
[[yii\base\Object|Object]]的子类应该覆盖它的构造函数（如果需要的话），以使它
它可以被正确配置：

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

在上面的例子中，构造函数的最后一个参数必须使用
包含键名 - 值相对应的数组来初始化构造函数最后面的属性。
在配置生效后，就可以覆盖`init()`方法进行初始化工作


根据该约定，你可以使用下面的配置数组(configuration array)来创建和配置一个新的对象：


```php
$object = Yii::createObject([
    'class' => 'MyClass',
    'property1' => 'abc',
    'property2' => 'cde',
], $param1, $param2);
```


路径别名
------------

Yii 2.0扩展了路径别名的用法，以用于文件/目录路径和URL。一个别名
必须以`@`字符开始，以便区别于文件/目录路径和URL。
例如，别名`@`指的是Yii的安装目录，而`@web`表示的是当前运行页面的URL。
几乎所有的Yii核心代码内部都支持路径别名。例如，
`FileCache::cachePath`可以采取两种路径别名和一个正常的目录路径。

路径别名也和类的命名空间密切相关的。建议将
每一个根命名空间定义为路径别名，这让你可以使用Yii的自动加载类而
不需要进行任何配置。例如，由于`@yii`指向Yii的安装目录，
像`yii\web\Request`这样的类便可以通过Yii自动导入。如果需要用第三方类库
(如Zend Framework)，你可以定义一个`@Zend`路径别名，它指向其安装
目录，这样Yii就能自动导入这个类库中的任何类。


自动加载
-----------

所有的类、接口和性状都会在使用它们的时候自动加载，而不需要使用
`include` or `require`。那是因为Composer和Yii的扩展特性在起作用。

自动加载(Autoloader) 需要遵守 [PSR-4](https://github.com/php-fig/fig-standards/blob/master/proposed/psr-4-autoloader/psr-4-autoloader.md)标准。
这意味，一个命名空间、类、接口以及特质的别名的定义，应该对应于除了根命名空间的文件路径。


例如，如果标准别名`@app`指向`/var/www/example.com/`，那么`\app\models\User`将从
`/var/www/example.com/app/models/User.php`载入。.

要自定义别名，可以使用下面的代码来配置：

```php
Yii::setAlias('@shared', realpath('~/src/shared'));
```

要附加自动加载功能，可以使用标准的PHP`spl_autoload_register`注册。

Helper类
--------------

Helper类通常包含静态方法，只能用下面的方法使用：

```php
use \yii\helpers\Html;
echo Html::encode('Test > test');
```

以下是框架提供的几个类：

- ArrayHelper
- Console
- FileHelper
- Html
- HtmlPurifier
- Inflector
- Json
- Markdown
- Security
- StringHelper
- VarDumper