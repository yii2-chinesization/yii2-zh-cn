事件
======

事件允许在特定执行点“注入”自定义代码到现存代码中。可以附加自定义代码到事件，当这个事件被触发时，这些代码将自动执行。例如，邮件程序对象成功发出消息时可触发 `messageSent` 事件。如想保持追踪成功发送的消息，可以附加这个追踪代码到 `messageSent` 事件。

Yii 引入了名为[[yii\base\Component]]的基类以支持事件。如果一个类需要触发事件就应该继承[[yii\base\Component]]或其子类。


触发事件
----------

事件通过调用[[yii\base\Component::trigger()]]方法触发，此方法须传递*事件名*和描述参数可选的事件对象到事件处理器。如：

```php
namespace app\components;

use yii\base\Component;
use yii\base\Event;

class Foo extends Component
{
    const EVENT_HELLO = 'hello';

    public function bar()
    {
        $this->trigger(self::EVENT_HELLO);
    }
}
```

以上代码当调用 `bar()` ，它将触发名为 `hello` 的事件。

> 提示：推荐使用类常量来表示事件名。上例中，常量 `EVENT_HELLO` 用来表示 `hello` 。这有两个好处。第一，它可以防止拼写错误并支持 IDE 的自动完成。第二，只要简单检查常量声明就能了解一个类支持哪些事件。

有时想要在触发事件时同时传递一些额外信息到事件处理器。例如，邮件程序要传递消息信息到 `messageSent` 事件的处理器以便处理器了解哪些消息被发送了。为此，可以提供一个事件对象作为[[yii\base\Component::trigger()]]方法的第二个参数。这个事件对象必须是[[yii\base\Event]]类或其子类的实例。如：

```php
namespace app\components;

use yii\base\Component;
use yii\base\Event;

class MessageEvent extends Event
{
    public $message;
}

class Mailer extends Component
{
    const EVENT_MESSAGE_SENT = 'messageSent';

    public function send($message)
    {
        // ...sending $message...

        $event = new MessageEvent;
        $event->message = $message;
        $this->trigger(self::EVENT_MESSAGE_SENT, $event);
    }
}
```

当[[yii\base\Component::trigger()]]方法被调用时，它将调用附加到命名事件（named event）的事件处理器。


事件处理器（Event Handlers 又称为事件处理句柄）
--------------

事件处理器是一个[PHP 回调函数](http://www.php.net/manual/en/language.types.callable.php)，当它所附加到的事件被触发时它就会执行。可以使用以下回调函数之一：

- 字符串形式指定的 PHP 全局函数，如 `trim()` ；
- 对象名和方法名数组形式指定的对象方法，如 `[$object, $method]` ；
- 类名和方法名数组形式指定的静态类方法，如 `[$class, $method]` ；
- 匿名函数，如 `function ($event) { ... }` 。

事件处理器的格式是：

```php
function ($event) {
    // $event 是 yii\base\Event 或其子类的对象
}
```

通过 `$event` 参数，事件处理器就获得了以下有关事件的信息：

- [[yii\base\Event::name|event name]]
- [[yii\base\Event::sender|event sender]]：哪个对象的 `trigger()` 方法被调用
- [[yii\base\Event::data|custom data]]：当附加事件处理器（简单地说）时提供的数据


附加事件处理器
----------------

调用[[yii\base\Component::on()]]方法来附加处理器到事件上。如：

```php
$foo = new Foo;

// 处理器是全局函数
$foo->on(Foo::EVENT_HELLO, 'function_name');

// 处理器是对象方法
$foo->on(Foo::EVENT_HELLO, [$object, 'methodName']);

// 处理器是静态类方法
$foo->on(Foo::EVENT_HELLO, ['app\components\Bar', 'methodName']);

// 处理器是匿名函数
$foo->on(Foo::EVENT_HELLO, function ($event) {
    //事件处理逻辑
});
```

附加事件处理器时可以提供额外数据作为[[yii\base\Component::on()]]方法的第三个参数。数据在事件被触发和处理器被调用时能被处理器使用。如：

```php
// 当事件被触发时以下代码显示 "abc"
// 因为 $event->data 包括被传递到 "on" 方法的数据
$foo->on(Foo::EVENT_HELLO, function ($event) {
    echo $event->data;
}, 'abc');
```

可以附加一个或多个处理器到一个事件。当事件被触发，已附加的处理器将按附加次序依次调用。如果某个处理器必须停止其后的处理器调用，它可以设置 `$event` 参数的[yii\base\Event::handled]]属性为真，如下：

```php
$foo->on(Foo::EVENT_HELLO, function ($event) {
    $event->handled = true;
});
```




可以向一个事件绑定一个或多个名为 *event handlers（事件处理器）*的 PHP 回调函数。当事件被触发，
所有绑定它的事件处理器就会被自动引入。

这里有两个主要的绑定事件处理器的方法。一个是通过行内代码，一个是通过应用设置。

> 小技巧：要想获得框架或扩展中当下全部的事件列表，你可以在框架代码中搜索 `->trigger`。

### 通过代码绑定

你可以通过一个组件对象的 `on` 方法来在你的代码中指定它的事件触发器，这个方法的第一个参数是它要绑定的事件的名字；
第二个参数是当事件发生时，需要回调的 handler（处理器对象，例如，一个函数）：

```php
$component->on($eventName, $handler);
```

这个处理器必须是一个合法的 PHP 回调类型（Callback，译者： [仅供参考](http://www.php.net/manual/zh/language.types.callable.php)）。
它可以是一下几个之一：

- 一个全局函数的名字。
- 一个包含模型名称和方法名的数组。
- 一个包含一个对象和方法名的数组。
- 一个[匿名函数](http://www.php.net/manual/zh/functions.anonymous.php).

```php
// 全局函数：
$component->on($eventName, 'functionName');

// 模型与方法的名字：
$component->on($eventName, ['Modelname', 'functionName']);

// 对象与方法名：
$component->on($eventName, [$obj, 'functionName']);

// 匿名函数：
$component->on($eventName, function ($event) {
	// 使用 $event。
});
```

在匿名函数的示例中所示，这样，事件处理函数必须被定义，这样它才能作为一个参数被引入。
它会是一个 [[yii\base\Event]] 对象

为了向处理器中传入外部数据，你可以在 `on` 方法的第三个参数中提供这些数据。
这之后，就可以用 `$event->data` 的方式，在处理器内部访问到它们：

```php
$component->on($eventName, function ($event) {
	// 这些外部数据可以这样来访问 $event->data
}, $extraData);
```

### 通过配置来绑定

你还可以在你的应用配置文件中绑定事件处理器。
为此，你需要在你需要绑定事件处理器的组件的配置文本里添加一个元素。语法是 `"on <event>" => handler`：

```php
return [
	// ...
	'components' => [
		'db' => [
			// ...
			'on afterOpen' => function ($event) {
				// 与数据库建立连接后立刻搞一些事情
			}
		],
	],
];
```

当使用这种方法绑定事件触发器是，触发器必须是一个匿名函数。

触发事件
-----------------

大多数的事件将会在正常工作流程里被触发。比如，`beforeSave` 事件就会在 Active Record 模型保存前被触发。

但你仍可以通过 `trigger` 方法，手动触发一个事件，在组件上调用事件处理器：

```php
$this->trigger('myEvent');

// 或者

$event = new CreateUserEvent(); // 扩展自 yii\base\Event
$event->userName = '犀利哥';
$this->trigger('createUserEvent', $event);
```

事件名在定义它的类中应该是唯一的。同时，它也是 *case-sensitive（区分大小写）*的。
用类的常量定义事件名也不失为一个不错的办法：

```php
class Mailer extends Component
{
	const EVENT_SEND_EMAIL = 'sendEmail';

	public function send()
	{
		// ...
		$this->trigger(self::EVENT_SEND_EMAIL);
	}
}
```

移除事件处理器
-----------------------

相对应的 `off` 方法用于移除一个事件处理器：

```php
$component->off($eventName);
```

Yii 支持将多个处理器关联到同一事件上。在此情况下，使用 `off` 时，
每一个处理器都会被移除。这样，若你需要只移除一个处理器，你需要给`off` 方法提供第二个参数，就像这样：

```php
$component->off($eventName, $handler);
```

同样，`$handler` 在 `off` 方法中的形式，应该与它在 `on` 方法中被注册时的一样。

> 小技巧：若你之后可能需要移除它，那你最好别用匿名函数的形式注册它。

全局事件
-------------

你可以用 “global（全局）” 事件给应用的所有组件上一个全局事件，替换掉各个组件各自的。全局事件可以发生在任意组件类型上。
为了给一个全局事件绑定处理器，需要在应用实例上直接调用 `on` 方法：

```php
Yii::$app->on($eventName, $handler);
```

全局事件在整个应用实例而不是某个具体的组件上被触发：

```php
Yii::$app->trigger($eventName);
```

类事件
------------

也可以给一个类的所有实例对象一起绑定事件处理器，而不仅仅是某一个对象哦！
要做到这一点，只需通过静态的 `Event::on` 方法即可：

```php
Event::on(ActiveRecord::className(), ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
	Yii::trace(get_class($event->sender) . ' is inserted.');
});
```

上面的代码所定义的这个处理器，它可以在任意 Active Record 对象的 `EVENT_AFTER_INSERT` 事件发生时，被触发。