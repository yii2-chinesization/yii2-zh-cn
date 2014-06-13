Yii2 核心代码风格指南
==============================

以下代码风格指南针对于开发 Yii 2.x 核心代码和官方扩展。
如果你想向核心代码 pull request，你应该考虑遵循本指南。
我们不强迫你在自己的应用中使用本代码风格，你可以自由选择你喜欢的。

你可以在这里获取 CodeSniffer 的配置文件：https://github.com/yiisoft/yii2-coding-standards

1. 概览
-----------

总体上我们采用 [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
兼容风格，所以所有符合
[PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
标准的代码也符合我们的代码风格。

- 文件 **必须** 使用 `<?php` 或 `<?=` 标签。
- 文件末尾应该空一行。
- 文件中的 PHP 代码 **必须** 使用无 BOM 的 UTF-8 字符编码。
- 代码 **必须** 使用 4个空格缩进，而不是 tab。
- 类名 **必须** 使用首字母大写的驼峰法 `StudlyCaps`。
- 类常量的声明 **必须** 全部大写并以下划线作分隔符。
- 方法的声明 **必须** 使用首字母小写的驼峰法 `camelCase`。
- 属性的声明 **必须** 使用首字母为小写的驼峰法 `camelCase`。
- 私有属性的声明 **必须** 由一个下划线开始。
- 始终使用 `elseif`，而不是 `else if`。

2. 文件
--------

### 2.1. PHP 标签

- PHP 代码 **必须** 使用 `<?php` 或 `<?=` 标签， `不可以` 使用包括 `<?` 在内的其它任何标签。
- 纯 PHP 代码的文件应该省略 `?>` 关闭标签。
- 每行代码末尾 `不可以` 使用空格作为结束。
- 任何包含 PHP 代码的文件扩展名都应该为 `.php`。

### 2.2. 字符编码

PHP 代码 **必须** 且只能使用 无 `BOM` 的 `UTF-8`编码。

3. 类名
--------------

类名的声明 **必须** 使用首字母为大写的驼峰法 `StudlyCaps` 声明， 例如 `Controller`， `Model`。

4. 类和接口
----------

下列说明中的“类”指的是所有“类”和“接口”。

- 类名的应该使用首字母为大写的驼峰法 `StudlyCaps` 。
- 左大括号始终应该写在类名的下一行。
- 每个类都应该有符合 PHPDoc 规范的文档块。
- 类中的所有代码都应该缩进一个 tab。
- 每个 PHP 文件中应该只包含一个类。
- 所有的类都应该有命名空间。
- 类名应该与文件名保持一致，类的命名空间应该与目录结构保持一致。

```php
/**
 * 文档
 */
class MyClass extends \yii\Object implements MyInterface
{
    // 代码
}
```

### 4.1. 常量

类常量的声明 **必须**全部大写并以下划线作分隔符。
例如：

```php
<?php
class Foo
{
    const VERSION = '1.0';
    const DATE_APPROVED = '2012-06-01';
}
```
### 4.2. 属性

- 声明一个公有属性应该使用关键字 `public` 显式的指明它。
- 公有和受保护类型的属性应该声明在类中的最上方，位于所有成员方法的前面。私有属性也应该声明在类中的最上方，但当它处理的数据只和类中很少一部分成员方法相关时，可以直接写在这些方法的前面。
- 一个类中属性声明的顺序从上到下应该为公有、受保护、私有。
- 从可读性角度考虑，属性的声明之间不该有空行，而属性和方法之间应该有2个空行。
- 私有属性的命名应该以一个下划线开始，后面是首字母为小写的驼峰法 `camelCase`，如 `$_totalCount`。
- 公有类成员和普通独立变量的命名应该用首字母为小写的驼峰法 `camelCase`，如 `$camelCase`。
- 使用描述性的命名，诸如 `$i` 、 `$k` 之类模糊不清的最好不要使用。


例如：

```php
<?php
class Foo
{
    public $publicProp;
    protected $protectedProp;
    private $_privateProp;
}
```

### 4.3. 方法

- 函数和方法的命名应该用首字母为小写的驼峰法 `camelCase`。
- 方法名本身应该是描述性的，用来表示这个方法的目的。
- 方法的声明应该始终使用 `public`、 `protected` 或 `private` 修饰其可见性，不可以使用 PHP 4 风格的 `var`。
- 包裹方法的左大括号应该位于方法声明的下一行。

~~~
/**
 * 文档
 */
class Foo
{
    /**
     * 文档
     */
    public function bar()
    {
        // 代码
        return $value;
    }
}
~~~

### 4.4 文档块

`@param`，`@var`,`@property` 和 `@return` **必须**要声明类型诸如 `boolean`，`integer`，`string`，`array` 或 `null`，你还可以使用类名诸如 `Model`、 `ActiveRecord` 等。数组成员为对象时使用 `ClassName[]`。

### 4.5 构造方法

- 应该使用 `__construct` 而不是 PHP 4 风格的构造方法。

## 5 PHP

### 5.1 类型

- 所有 PHP 内置类型和值都 **必须** 用小写。包括 `true`， `false`， `null` 和 `array`。

- 关联数组的使用应该遵循以下格式：

```php
$config = [
    'name'  => 'Yii',
    'options' => ['usePHP' => true],
];
```

- 更改现有变量的类型被看做是坏习惯，除非很有必要否则不要这么做。


```php
public function save(Transaction $transaction, $argument2 = 100)
{
    $transaction = new Connection; // bad
    $argument2 = 200; // good
}
```

### 5.2 字符串

- 如果字符串中不包含变量和单引号，使用时用单引号。

```php
$str = 'Like this.';
```

- 如果字符串中包含单引号，可以使用双引号去避免额外转义。

#### 变量替换

```php
$str1 = "Hello $username!";
$str2 = "Hello {$username}!";
```

不允许这样替换：

```php
$str3 = "Hello ${username}!";
```

#### 字符串连接

连接字符串时的操作符左右加空格：

```php
$name = 'Yii' . ' Framework';
```

- 如果字符串过长，使用以下格式：

```php
$sql = "SELECT *"
    . "FROM `post` "
    . "WHERE `id` = 121 ";
```

### 5.3 数组

使用 PHP 5.4 的短数组语法。

#### 数字索引

- `不可以` 使用负数作数组的索引。

- 使用如下格式声明数组：

```php
$arr = [3, 14, 15, 'Yii', 'Framework'];
```

- 如果太多数组元素在一行中：

```php
$arr = [
    3, 14, 15,
    92, 6, $test,
    'Yii', 'Framework',
];
```

#### 关联数组

关联数组的使用应该遵循以下格式：

```php
$config = [
    'name'  => 'Yii',
    'options' => ['usePHP' => true],
];
```

### 5.4 控制语句

- 条件控制语句的括号前后 **必须**有一个空格。
- 括号里的运算符前后都应该有一个空格。
- 左大括号应该位于同一行。
- 右大括号应该位于新的一行。
- 始终用大括号包裹单行语句。

```php
if ($event === null) {
    return new Event();
} elseif ($event instanceof CoolEvent) {
    return $event->instance();
} else {
    return null;
}

//单行语句没有用大括号包裹，这是不允许的：
if (!$model && null === $event)
    throw new Exception('test');
```

#### switch

- 使用以下格式 switch：

```php
switch ($this->phpType) {
    case 'string':
        $a = (string)$value;
        break;
    case 'integer':
    case 'int':
        $a = (integer)$value;
        break;
    case 'boolean':
        $a = (boolean)$value;
        break;
    default:
        $a = null;
}
```

### 5.5 函数调用

```php
doIt(2, 3);

doIt(['a' => 'b']);

doIt('a', [
    'a' => 'b',
    'c' => 'd',
]);
```

### 5.6 匿名函数声明

请注意 `function` 和 `use` 关键字与左括号之间的空格：

```php
// 好的
$n = 100;
$sum = array_reduce($numbers, function ($r, $x) use ($n) {
    $this->doMagic();
    $r += $x * $n;
    return $r;
});

// 糟糕的
$n = 100;
$mul = array_reduce($numbers, function($r, $x) use($n) {
    $this->doMagic();
    $r *= $x * $n;
    return $r;
});
```

文档
-------------

- 参考 [PHPdoc](http://phpdoc.org/) 语法。
- 不允许没有文档注释的代码出现。
- 所有类文件 **必须**包含一个位于文件最上方的文件级别的文档块，以及一个位于类上方的类级别的文档块。
- 如果一个方法没有返回值，则没有必要使用 `@return` 。
- Yii2 中所有继承自 `yii\base\Object` 的虚拟属性都 **必须** 在文档块中添加一个 `@property` 标签。标签的注释内容可以通过在 build 目录下运行 `./build php-doc` 从相应的 getter 和 setter 方法的 `@return` 和 `@param` 标签中自动生成。如果生成的内容与 `@return` 中不同，你可以向 getter 和 setter 方法添加 `@property` 标签去显式的指明内容。

```php
/**
 * 返回所有属性或单个属性的错误信息
 * @param string $attribute 属性名，使用 null 取回所有属性的错误信息。
 * @property array 一个所有属性错误信息的数组，没有错误则返回空数组。
 * 结果是一个二维数组，详细描述见 [[getErrors()]]。
 * @return array 所有属性或指定属性的错误信息，没有错误则返回空数组。
 * 请注意，当返回所有属性的错误信息时，结果是一个二维数组，格式是这样：
 * ...
 */
public function getErrors($attribute = null)
```

#### 文件级别的注释：

```php
<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
```

#### 类级别的注释：

```php
/**
 * Component 是一个提供了 *property*， *event* 和 *behavior* 的基类。
 *
 * @include @yii/doc/base-Component.md
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
```

#### 方法/函数注释：

```php
/**
 * 返回一个事件的附加事件列表
 * 你可以通过添加和删除处理去操作返回的 [[Vector]] 对象。
 * 例如：
 *
 * ~~~
 * $component->getEventHandlers($eventName)->insertAt(0, $eventHandler);
 * ~~~
 *
 * @param string $name 事件名称
 * @return Vector 该事件的附加事件处理列表
 * @throw Exception 如果事件未定义
 */
public function getEventHandlers($name)
{
    if (!isset($this->_e[$name])) {
        $this->_e[$name] = new Vector;
    }
    $this->ensureBehaviors();
    return $this->_e[$name];
}
```

#### Markdown

正如上面所示， PHPDoc 文档块中可以使用 markdown 语法。

这里有一些额外需要掌握的语法，用以在文档中对类、方法和属性进行链接：

- `[[canSetProperty]]` 将会创建一个链接，指向当前类中的 `canSetProperty` 方法或属性。
- `[[Component::canSetProperty]]` 将会创建一个链接，指向同一个命名空间下 `Component` 类中的 `canSetProperty` 方法或属性。
- `[[yii\base\Component::canSetProperty]]` 将会创建一个链接，指向 `yii\base` 命名空间下 `Component` 类中的 `canSetProperty` 方法或属性。
- `[[Component]]` 将会创建一个链接指向同一个命名空间下的 `Component` 类。这里写上命名空间也无妨。

用下述方法定义链接的文字：

```
... as displayed in the [[header|header cell]].
```

|前面的内容是上述的指向方法、属性或类的链接地址， 后面的内容为链接文字。


#### 单行注释

- 单行注释应该使用 `//` 开头而不是 `#`。
- 单行注释应该自成一行。

额外规则
----------------

### `=== []` . `empty()`

**必须** 尽可能使用 `empty()`。

### 多重返回点

当条件嵌套开始变得混乱应该尽早 `return`。如果方法很短则无关紧要。

### `self` vs. `static`

除了下述场景以外一律使用 `static`。

- 访问类常量必须通过 `self`， 例如 `self::MY_CONSTANT`。
- 访问私有静态属性必须通过 `self`， 例如 `self::$_events`。
- 允许递归调用 `self` 去而不是继承该类。

### “不要做某事”的值

设置一个组件不要做某事应该传的值为 `false`，不应该假设为 `null`， `''` 或 `[]`。

### 目录/命名空间命名

- 使用小写。
- 使用名字的复数形式表示对象（例如Validators）。
- 使用单数形式表示相关的功能/特性（例如Web）。