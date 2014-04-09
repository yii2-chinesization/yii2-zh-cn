日志记录（Logging）
=======

Yii 提供了一个灵活可扩展的日志功能，可以基于不同的日志严格级别和分类来处理。
你可以通过设立不同的标准来过滤分拣这些信息，并把他们存进不同的的文件，邮件或者调试器，等等。

基础
--------------

最基本的日志记录就像调用一个方法一样简单：

```php
\Yii::info('你好，我是一条日志消息，么么哒！');
```

### 消息分类（Message category）

可以给一个消息附加一个消息分类的信息，从而使得这些消息可以被过滤，或者分别用不同的方式处理。
消息分类是日志记录方法的第二个参数，它默认为 `application`
。

### 严格级别（Severity levels）

有多种严格级别和相应方法可供选择：

- [[Yii::trace]] 主要是用于开发目的，用以标明某些代码的运作流程。注意：它只在开发模式下才起效，
也就是 `YII_DEBUG` 是 `true` 的时候。
- [[Yii::error]] 用以记录那些不可恢复的错误。
- [[Yii::warning]] 在错误发生后，运行仍可继续执行时记录。
- [[Yii::info]] 用以在重要事件执行时保存记录，比如管理员的登陆。

日志目的地（Log targets）
-----------

当一个日志记录方法被调用时，消息被传递到了 [[yii\log\Logger]] （日志记录器）组件，也可以这样访问 `Yii::$app->log`。
Logger 在内存中积攒消息，并在累积足够多的消息时，或 request （访问请求）结束后，再把他们一起存入不同的日志“目的地”
，比如文件或邮件。

你可以在应用配置中设置这些目的地，比如这样：

```php
[
	'components' => [
		'log' => [
			'targets' => [
				'file' => [
					'class' => 'yii\log\FileTarget',
					'levels' => ['trace', 'info'],
					'categories' => ['yii\*'],
				],
				'email' => [
					'class' => 'yii\log\EmailTarget',
					'levels' => ['error', 'warning'],
					'message' => [
						'to' => ['admin@example.com', 'developer@example.com'],
						'subject' => '来自 example.com 的新日志消息',
					],
				],
			],
		],
	],
]
```

在上面的配置中，我们定义了两个目的地：[[yii\log\FileTarget|file]] 和 [[yii\log\EmailTarget|email]]。
In both cases we are filtering messages handles by these targets by severity. In case of file target we're
additionally filter by category. `yii\*` means all categories starting with `yii\`.

Each log target can have a name and can be referenced via the [[yii\log\Logger::targets|targets]] property as follows:

```php
Yii::$app->log->targets['file']->enabled = false;
```

When the application ends or [[yii\log\Logger::flushInterval|flushInterval]] is reached, Logger will call
[[yii\log\Logger::flush()|flush()]] to send logged messages to different log targets, such as file, email, web.


性能分析（Profiling）
---------

Performance profiling is a special type of message logging that can be used to measure the time needed for the
specified code blocks to execute and find out what the performance bottleneck is.

To use it we need to identify which code blocks need to be profiled. Then we mark the beginning and the end of each code
block by inserting the following methods:

```php
\Yii::beginProfile('myBenchmark');
...code block being profiled...
\Yii::endProfile('myBenchmark');
```

where `myBenchmark` uniquely identifies the code block.

Note, code blocks need to be nested properly such as

```php
\Yii::beginProfile('block1');
	// some code to be profiled
	\Yii::beginProfile('block2');
		// some other code to be profiled
	\Yii::endProfile('block2');
\Yii::endProfile('block1');
```

Profiling results [可以在 debugger 中显示出来](module-debug.md)。
