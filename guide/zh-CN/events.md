事件（Events）
======

事件是一种向某些地方的现有代码里"注入"自定义代码的方式。例如，
当用户添加一条评论时，一个评论对象可以触发一个“添加”事件。我们可以编写自定义的代码，并把它与事件绑定，
这样当事件被触发（比如，添加了评论），我们自定义的代码就会被执行

事件在提高组件灵活性和与框架或扩展的工作流程挂钩方面非常有用。

触发事件
-----------------

任何组件都可以通过 `trigger` 方法来触发事件：

```php
$this->trigger('myEvent');

// 或者

$event = new CreateUserEvent(); // 扩展自 yii\base\Event
$event->userName = 'Alexander';
$this->trigger('createUserEvent', $event);
```

事件名在定义它的类中应该是唯一的。同时，它也是*case-sensitive（大小写敏感）*的。
用类的常量来定义事件名会是一个不错的办法：

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

绑定事件处理器
------------------------

可以向一个事件绑定一个或多个名为 *event handlers（事件处理器）*的 PHP 回调函数。当事件被触发，
所有绑定它的事件处理器就会被自动引入。

这里有两个主要的绑定事件处理器的方法。一个是通过代码，一个是通过应用设置。

> 小贴士：要想获得框架或扩展中全部最新的事件列表，你可以搜索代码 `->trigger`。

### 通过代码绑定

你可以通过一个组件对象的 `on` 方法来分配它的事件触发器，这个方法的第一个参数是它要绑定的事件的名字；
第二个参数是当事件发生时，需要回调的handler（处理器）：

```php
$component->on($eventName, $handler);
```

这个处理器必须是一个合法的PHP 回调类型（译者： [仅供参考](http://www.php.net/manual/zh/language.types.callable.php)）。它可以是：

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

还可以通过第三个参数来提供其他的外部数据：

```php
$component->on($eventName, function ($event) {
	// 这些外部数据可以这样来访问 $event->data
}, $extraData);
```

### 通过配置来绑定

通过引用程序的配置文件绑定处理器也是可行的：

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

移除事件处理器
-----------------------

相对应的 `off` 方法用于移除一个事件处理器：

```php
$component->off($eventName);
```

Yii 支持将多个处理程序关联的同一事件的能力。如以上所述，使用 `off` 时
每一个处理器都会被移除。这样，若你需要只移除一个处理器，你需要给`off` 方法提供第二个参数，就像这样：

```php
$component->off($eventName, $handler);
```

同样，`$handler` 在 `off` 方法中的形式，应该与它在 `on` 方法中一样。

全局事件
-------------

你可以用 ”global“ events 给应用的所有组件上一个全局事件，而不用每一个组件都来一遍。触发一个全局事件，需要用应用的实例对象，
而不是某一个组件的：

```php
Yii::$app->trigger($eventName);
```

这样绑定处理器：

```php
Yii::$app->on($eventName, $handler);
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