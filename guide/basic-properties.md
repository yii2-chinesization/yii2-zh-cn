属性
=========

PHP 中，类的成员变量亦称为*属性*。它们是类定义的一部分并用来表现类实例的状态。实践中，你经常想要在属性被读取或修改时做些特殊处理。例如，当字符串被赋值给`label` 属性时，你想要去掉字符串前后空格，可以使用以下代码完成这个任务：

```php
$object->label = trim($label);
```

以上代码的缺点是无论何时何地修改 `label` 属性都必须调用`trim()` 函数。如果将来对 `label` 属性提出新要求，如首字母转换成大写字母，你就必须修改所有这些地方——这是你最希望规避的实践。

要解决这个问题，Yii 引进了名为[[yii\base\Object]]的基类来支持基于 *getter* 和 *setter* 类方法的属性定义。如果一个类需要这样的支持就应该继承[[yii\base\Object]]或其子类。

> 资讯：Yii 框架的核心类几乎都继承自[[yii\base\Object]]或其子类。即无论何时在核心类见到 getter 或 setter 方法，都可以像属性一样使用它。

getter 方法是方法名以`get` 开头的方法，而 setter 方法名以 `set` 开头。方法名中 `get` 或 `set` 前缀后的部分定义了属性名。如， getter 方法 `getLabel()` 和 setter 方法 `setLabel()` 定义了一个名为 `label` 的属性，如下所示：

```php
namespace app\components;

use yii\base\Object;

class Foo extend Object
{
    private $_label;

    public function getLabel()
    {
        return $this->_label;
    }

    public function setLabel($value)
    {
        $this->_label = trim($value);
    }
}
```

 getters/setters 定义的属性能像类成员变量那样使用。两者主要的区别是这种属性被读取时，对应的 getter 方法将被调用；而当属性被赋值时，对应的 setter 方法就调用。如：

```php
// 等同于 $label = $object->getLabel();
$label = $object->label;

// 等价于 $object->setLabel('abc');
$object->label = 'abc';
```

只有 getter 方法没有 setter 方法定义的属性是*只读属性*。尝试赋值给这样的属性将导致[[yii\base\InvalidCallException|InvalidCallException]]无效调用异常。类似的，只有 setter 方法而没有 getter 方法定义的属性是*只写属性*，尝试读取这种属性也会触发异常。但只写属性不常见。

基于 getter 和 setter 定义属性有很多专门的规则或限制：

* 这类属性取名是*不区分大小写的*。如， `$object->label` 和 `$object->Label` 是同一个属性。因为 PHP 方法名是不区分大小写的。
* 如果此类属性名和类成员变量相同，后者将有优先权。例如，假设以上 `Foo` 类有个 `label` 成员变量，然后给 `$object->label = 'abc'` 赋值，将赋给成员变量而不是 setter `setLabel()` 方法。
* 这类属性不支持可见性（访问限制）。定义属性的 getter 和 setter 方法是公有、受保护还是私有的对属性的可见性没有区别。
* 这类属性只能被*非静态* getter 和 setter 方法定义，静态方法不计。

回到一开始提到的问题，取代处处要调用 `trim()` 函数，我们只在 setter `setLabel()` 方法内调用一次。如果 label 首字母变成大写的新要求来了，我们只需要修改`setLabel()` 方法，而无须接触任何其它代码。
