缓存
=======

缓存是提升Web应用性能的简便有效的方式。通过将相对静态的数据存储到缓存并在收到请求时取回缓存，应用程序便节省了生成这些数据所需的时间。缓存是提升应用程序性能的最佳方式之一，在任何大型网站上几乎是强制使用的。

基础概念
-------------

在 Yii 中使用缓存主要包括配置并访问一个应用组件。 下面的应用配置设定了一个使用两个 [memcached](http://memcached.org/) 缓存服务器的缓存组件。请注意，如果你正在使用basic的示例应用程序，这个配置应该操作在位于`@app/ web.php`的别名文件。

```php
'components' => [
	'cache' => [
		'class' => '\yii\caching\MemCache',
		'servers' => [
			[
				'host' => 'server1',
				'port' => 11211,
				'weight' => 100,
			],
			[
				'host' => 'server2',
				'port' => 11211,
				'weight' => 50,
			],
		],
	],
],
```

当应用程序运行中, 通过调用 `Yii::$app->cache` 能访问缓存组件。

Yii 提供了不同的缓存组件，可以将缓存数据存储到不同的媒介中。下面是一个可用缓存组件的列表：

* [[yii\caching\ApcCache]]: 使用 PHP [APC](http://php.net/manual/en/book.apc.php) 扩展. 在一个集中式胖应用程序中，这个选项被认为是处理缓存最快的 (例如，一个服务器，没有专门的负载均衡器等）。

* [[yii\caching\DbCache]]: 使用数据库表存储缓存数据。默认情况下，它将创建并使用在 runtime 目录下的一个
  [SQLite3](http://sqlite.org/) 数据库。你也可以通过设置其 db 属性指定一个给它使用的数据库。
  
* [[yii\caching\DummyCache]]: 目前 dummy 缓存并不实现缓存功能。此组件的目的是用于简化那些需要检查缓存可用性的代码。 例如，在开发阶段或者服务器尚未支持实际的缓存功能，我们可以使用此缓存组件。当启用了实际的缓存支持后，我们可以切换到使用相应的缓存组件。在这两种情况中，我们可以使用同样的代码 `Yii::$app->cache->get($key)` 获取数据片段而不需要担心 `Yii::$app->cache` 可能会是 `null`。

* [[yii\caching\FileCache]]: 使用文件存储缓存数据。这个特别适合用于存储大块数据（例如页面）

* [[yii\caching\MemCache]]: 使用 PHP [memcache](http://php.net/manual/en/book.memcache.php)
  和 [memcached](http://php.net/manual/en/book.memcached.php) 扩展。
在一个布式应用程序中，这个选项被认为是处理缓存最快的（例如，多个服务器，负载平衡器等）。

* [[yii\redis\Cache]]: 实现了基于 [Redis](http://redis.io/) 的键-值存储缓存组件（redis版本必须是2.6.12或更高）。

* [[yii\caching\WinCache]]: 使用 PHP [WinCache](http://iis.net/downloads/microsoft/wincache-extension)
  ([see also](http://php.net/manual/en/book.wincache.php)) 扩展。

* [[yii\caching\XCache]]: 使用 PHP [XCache](http://xcache.lighttpd.net/) 扩展。

* [[yii\caching\ZendDataCache]]: 使用
  [Zend Data Cache](http://files.zend.com/help/Zend-Server-6/zend-server.htm#data_cache_component.htm)
  作为低层缓存媒介。

提示: 由于所有的这些缓存组件均继承自同样的基类 [[yii\caching\Cache]]，因此无需改变使用缓存的那些代码就可以切换到使用另一种缓存方式。

缓存可以用于不同的级别。最低级别中，我们使用缓存存储单个数据片段，例如变量，我们将此称为 数据缓存（data caching）。下一个级别中，我们在缓存中存储一个由视图脚本的一部分生成的页面片段。 而在最高级别中，我们将整个页面存储在缓存中并在需要时取回。

在接下来的几个小节中，我们会详细讲解如何在这些级别中使用缓存。

注意: 按照定义，缓存是一个不稳定的存储媒介。即使没有超时，它也并不确保缓存数据一定存在。 因此，不要将缓存作为持久存储器使用。（例如，不要使用缓存存储 Session 数据）。

数据缓存
------------

数据缓存即存储一些 PHP 变量到缓存中，以后再从缓存中取出来。出于此目的，缓存组件的基类 [[yii\caching\Cache]] 提供了两个最常用的方法： [[yii\caching\Cache::set()|set()]] 和 [[yii\caching\Cache::get()|get()]]。注意，仅有序列化变量和对象能缓存成功。

要在缓存中存储一个变量 `$value` ，我们选择一个唯一 `$key` 并调用 [[yii\caching\Cache::set()|set()]] 存储它：

```php
Yii::$app->cache->set($key, $value);
```

缓存的数据将一直留在缓存中，除非它由于某些缓存策略（例如缓存空间已满，旧的数据被删除）而被清除。 要改变这种行为，我们可以在调用 [[yii\caching\Cache::set()|set()]] 的同时提供一个过期参数，这样在设定的时间段之后，缓存数据将被清除：

```php
// $value 在缓存中最多保留45秒
Yii::$app->cache->set($key, $value, 45);
```

稍后当我们需要访问此变量时（在同一个或不同的 Web 请求中），就可以通过 key 调用 [[yii\caching\Cache::get()|get()]] 从缓存中将其取回。 如果返回的是 `false`，表示此值在缓存中不可用，我们应该重新生成它：

```php
public function getCachedData()
{
	$key = /* 在这生成唯一key */;
	$value = Yii::$app->cache->get($key);
	if ($value === false) {
		$value = /* 因为在缓存中没有找到value，重新生成它，并且将它保存在缓存中以备后用 */;
		Yii::$app->cache->set($key, $value);
	}
	return $value;
}
```

这是一般使用数据缓存常见的模式。

当要存入缓存的变量选择 key 时，要确保此 key 对应用中所有其他存入缓存的变量是唯一的。 而在不同的应用之间，这个 key **不**需要是唯一的。缓存组件具有足够的智慧区分不同应用中的 key。

一些缓存存储器，例如 MemCache, APC, 支持以批量模式获取多个缓存值。这可以减少获取缓存数据时带来的开销。 Yii 提供了一个名为 [[yii\caching\Cache::mget()|mget()]] 的方法。它可以利用此功能。如果底层缓存存储器不支持此功能，[[yii\caching\Cache::mget()|mget()]] 依然可以模拟实现它。

要从缓存中清除一个缓存值，调用 [[yii\caching\Cache::delete()|delete()]]; 要清除缓存中的所有数据，调用 [[yii\caching\Cache::flush()|flush()]]。 当调用 [[yii\caching\Cache::flush()|flush()]] 时一定要小心，因为它会同时清除其他应用中的缓存。

提示: 由于 [[yii\caching\Cache]] 实现了 `ArrayAccess`，缓存组件也可以像一个数组一样使用。下面是几个例子：

```php
$cache = Yii::$app->cache;
$cache['var1'] = $value1;  // 相当于: $cache->set('var1', $value1);
$value2 = $cache['var2'];  // 相当于: $value2 = $cache->get('var2');
```

### 缓存依赖

除了过期设置，缓存数据也可能会因为依赖条件发生变化而失效。例如，如果我们缓存了某些文件的内容，而这些文件发生了改变，我们就应该让缓存的数据失效， 并从文件中读取最新内容而不是从缓存中读取。

我们将一个依赖关系表现为一个 [[yii\caching\Dependency]] 或其子类的实例。 当调用 [[yii\caching\Cache::set()|set()]]. 时，我们连同要缓存的数据将其一同传入。

```php
use yii\caching\FileDependency;

// 此值将在30秒后失效
// 也可能因依赖的文件发生了变化而更快失效
Yii::$app->cache->set($id, $value, 30, new FileDependency(['fileName' => 'example.txt']));
```

现在如果我们通过调用 `get()` 从缓存中获取 $value ，依赖关系将被检查，如果发生改变，我们将会得到一个 false 值，表示数据需要被重新生成。

如下是可用的缓存依赖的简要说明：

- [[yii\caching\FileDependency]]: 如果文件的最后修改时间发生改变，则依赖改变。
- [[yii\caching\GroupDependency]]: 用一个组名标记一个缓存数据。你可以调用 [[yii\caching\GroupDependency::invalidate()]] 一次清除所有相同组名的缓存。
- [[yii\caching\DbDependency]]: 如果指定 SQL 语句的查询结果发生改变，则依赖改变。
- [[yii\caching\ChainedDependency]]: 如果链中的任何依赖发生改变，则此依赖改变。
- [[yii\caching\ExpressionDependency]]: 如果指定的 PHP 表达式的结果发生改变，则依赖改变。

### 查询缓存

为数据库查询结果进行缓存，你可以调用  [[yii\db\Connection::beginCache()]] 和 [[yii\db\Connection::endCache()]] 来包它：

```php
$connection->beginCache(60); // cache all query results for 60 seconds.
// your db query code here...
$connection->endCache();
```


片段缓存
----------------

TBD: http://www.yiiframework.com/doc/guide/1.1/en/caching.fragment

### 缓存选项

TBD: http://www.yiiframework.com/doc/guide/1.1/en/caching.fragment#caching-options

### 缓存嵌套

TBD: http://www.yiiframework.com/doc/guide/1.1/en/caching.fragment#nested-caching

动态内容
---------------

TBD: http://www.yiiframework.com/doc/guide/1.1/en/caching.dynamic

页面缓存
------------

TBD: http://www.yiiframework.com/doc/guide/1.1/en/caching.page

### 输出缓存

TBD: http://www.yiiframework.com/doc/guide/1.1/en/caching.page#output-caching

### HTTP缓存

TBD: http://www.yiiframework.com/doc/guide/1.1/en/caching.page#http-caching
