事件（Events）
======

Yii 以“事件”的形式，在程序运行的特定时间点上向已有的代码中“注入”部分自定义的代码。比如，一个评论对象，
可以在用户在某篇博文下添加一条评论时，就触发一个“add”事件。

事件因以下两种理由特别有用：其一，它们可以让你的组件变得更加灵活。
其二，你可以把你自己的代码挂载到框架或正在使用的扩展程序的常规流程里去。

绑定事件处理器
------------------------

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