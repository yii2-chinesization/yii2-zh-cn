待翻译新增内容 Active Record（活动记录）
=============

Active Record（活动记录，以下简称AR）实现了[Active Record 的设计模式](http://zh.wikipedia.org/wiki/Active_Record)。
使用 AR 的前提是单个对象关联到数据表的特定行。对象属性映射到数据表的对应列。引用一个 AR 属性等同于连接数据表获取相应列数据。
一条活动记录（AR对象）对应数据表的一行，AR对象的属性则映射该行的相应列。

例如，`Customer` AR类关联 `tbl_customer` 表，意味着该类的 `name` 属性自动映射到 `tbl_customer` 表的 `name` 列。
有了活动记录的帮忙，假设变量 $customer 代表 `Customer` 类对象，可使用 `$customer->name` 获取关联数据行的 `name` 列。上例中，AR提供了一种面向对象的方式来获取数据库存储的数据。但 AR 的功能不止于此。

用 AR 而不是原生的 SQL 语句去执行数据库查询，可以调用直观方法来实现相同目标。如，调用 [[yii\db\ActiveRecord::save()|save()]] 方法将执行插入或更新轮询，将在该 AR 类关联的数据表新建或更新一行数据：

```php
$customer = new Customer();
$customer->name = 'Qiang';
$customer->save();  // 一行新数据插入 tbl_customer 表
```


定义 AR 类并关联数据表
------------------------------

定义 AR 类可以通过继承 [[yii\db\ActiveRecord]] 来实现，
实现 `tableName` 方法：

```php
use yii\db\ActiveRecord;

class Customer extends ActiveRecord
{
	/**
	 * @return string 返回该AR类关联的数据表名
	 */
	public static function tableName()
	{
		return 'tbl_customer';
	}
}
```

`tableName` 方法只须返回与该类相关联的数据表名。

可以用以下两种方法之一获取该类实例：

* 使用 `new` 创建对象，将插入新的数据行。
* 使用一个方法来提取数据库现有记录 （或记录集）

连接数据库
----------------------

AR 的实现依赖 [[yii\db\Connection|DB connection]] 执行基础的 DB 操作。
默认情况下，ActiveRecord假定存在一个名为`db` 的应用组件，提供数据库连接信息（满足[[yii\db\Connection]] 所需）和配置信息。
该组件通常在应用配置文件中配置：

```php
return [
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;dbname=testdb',
			'username' => 'demo',
			'password' => 'demo',
		],
	],
];
```

请阅读 [Database basics](database-basics.md) 章节，来了解关于如何配置和使用数据库连接的更多信息。

查询数据
---------------------------

有两个从数据库查询数据的 AR 方法：

 - [[yii\db\ActiveRecord::find()]]
 - [[yii\db\ActiveRecord::findBySql()]]

以上两个方法都会返回 [[yii\db\ActiveQuery]] 实例，该类继承自 [[yii\db\Query]]，同样支持灵活且强大的 DB 轮询方法。
以下例子提供了一些可选择的方法：

```php
// 取回所有状态为 *active*（激活的）的客户并以他们的 ID 排序：
$customers = Customer::find()
	->where(['status' => Customer::STATUS_ACTIVE])
	->orderBy('id')
	->all();

// 返回ID为1的客户：
$customer = Customer::find(1);

// 以上代码等价于下面的代码：
$customer = Customer::find()
	->where(['id' => 1])
	->one();

// 用原生SQL语句检索客户：
$sql = 'SELECT * FROM tbl_customer';
$customers = Customer::findBySql($sql)->all();

// 取回活跃客户（状态为 *active*）的数量：
$count = Customer::find()
	->where(['status' => Customer::STATUS_ACTIVE])
	->count();

// 以数组而不是对象形式取回客户信息：
$customers = Customer::find()
	->asArray()
	->all();
// $customers的每个元素都是键值对数组

// 以客户ID索引结果集：
$customers = Customer::find()->indexBy('id')->all();
// $customers 数组以 ID 为索引
```

> 注意：在上面的代码中，`Customer::STATUS_ACTIVE` 是一个在 `Customer` 类里定义的常量。
  相较于直接依赖于写死的字符串或数字，引用一个常量是一种更好的编程习惯。


AR 也支持批量查询，如：

```php
// 一次提取 10 个客户信息
foreach (Customer::find()->batch(10) as $customers) {
	// $customers 是 10 个或更少的客户对象的数组
}
// 一次提取 10 个客户并一个一个地遍历处理
foreach (Customer::find()->each(10) as $customer) {
	// $customer 是一个 ”Customer“ 对象
}
// 贪婪加载模式的批处理查询
foreach (Customer::find()->with('orders')->each() as $customer) {
}
```

如 [Query Builder](query-builder.md) 所述，
当请求大量数据时，批量查询非常有用，它会把你的内存占用限制在一定程度内。


访问列数据
---------------------

AR的属性映射对应数据行的列数据对象。
特性表现和常规的对象公开属性一样，特性名和对应的列名一样且区分大小写。

使用以下语法读取列的值：

```php
// "id" 和 "mail" 是 $customer AR对象关联的数据表列名
$id = $customer->id;
$email = $customer->email;
```

要改变列值，只要给关联属性赋新值并保存对象即可：

```php
$customer->email = 'jane@example.com';
$customer->save();
```

操作数据
-----------------------------

AR 提供以下方法插入、更新和删除数据库数据：

- [[yii\db\ActiveRecord::save()|save()]]
- [[yii\db\ActiveRecord::insert()|insert()]]
- [[yii\db\ActiveRecord::update()|update()]]
- [[yii\db\ActiveRecord::delete()|delete()]]
- [[yii\db\ActiveRecord::updateCounters()|updateCounters()]]
- [[yii\db\ActiveRecord::updateAll()|updateAll()]]
- [[yii\db\ActiveRecord::updateAllCounters()|updateAllCounters()]]
- [[yii\db\ActiveRecord::deleteAll()|deleteAll()]]

请注意 [[yii\db\ActiveRecord::updateAll()|updateAll()]]， [[yii\db\ActiveRecord::updateAllCounters()|updateAllCounters()]] 和 [[yii\db\ActiveRecord::deleteAll()|deleteAll()]] 都是静态方法，用整个整个数据表。其他方法只对 AR 对象关联的数据行有效。

```php
// 插入新客户的记录
$customer = new Customer();
$customer->name = 'James';
$customer->email = 'james@example.com';
$customer->save();  // 等同于 $customer->insert();

// 更新现有客户记录
$customer = Customer::find($id);
$customer->email = 'james@example.com';
$customer->save();  // 等同于 $customer->update();

// 删除已有客户记录
$customer = Customer::find($id);
$customer->delete();

// 所有客户的age字段加1：
Customer::updateAllCounters(['age' => 1]);
```

> 补充信息：执行 `save()` 方法前， AR 先通过 `ActiveRecord::isNewRecord` 方法分析 AR 对象是否为 PHP 端初始化出来的，并以此来判断是需要 `INSERT`（插入）还是 `UPDATE`（更新）数据，
  然后哉 save() 方法保存数据时，就会执行相应的 SQL 语句。

如果想引用数据库 schema 里定义的默认值，则可以使用 `loadDefaultValues()` 方法：

```php
$customer = new Customer();
$customer->loadDefaultValues();
$customer->name = 'Alexander';
$customer->save();
```


数据输入和有效性验证
-------------------------

AR 继承了 [[yii\base\Model]] 的数据有效性验证和数据输入能力。有效性验证的方法会在数据保存时被调用。
数据的有效性验证会在 `save()` 方法执行时自动完成，如果验证失败，数据保存操作将取消。

更多细节请参看本指南的 [Model](model.md) 部分。

查询关联的数据
------------------------

使用 AR 方法也可以查询数据表的关联数据（如，选出表A的数据可以拉出表B的关联数据）。
有了 AR，返回的关联数据连接就像连接关联主表的 AR 对象的属性一样。

建立关联关系后，通过 `$customer->orders` 可以获取
一个 `Order` 对象的数组，该数组代表当前客户对象的订单集。

定义关联关系使用一个可以返回[[yii\db\ActiveQuery]]对象的 getter 方法，
[[yii\db\ActiveQuery]]对象有关联上下文的相关信息，因此可以只查询关联数据。

```php
class Customer extends \yii\db\ActiveRecord
{
	public function getOrders()
	{
		// 客户和订单通过 Order.customer_id -> id 关联的一对多关系
		return $this->hasMany(Order::className(), ['customer_id' => 'id']);
	}
}

class Order extends \yii\db\ActiveRecord
{
	// 订单和客户通过 Customer.id -> customer_id 关联的一对一关系
	public function getCustomer()
	{
		return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
	}
}
```

以上使用了 [[yii\db\ActiveRecord::hasMany()]] 和 [[yii\db\ActiveRecord::hasOne()]] 方法。
以上两例分别是关联数据多对一关系和一对一关系的建模范例。
如，一个客户有很多订单，一个订单只归属一个客户。
两个方法都有两个参数并返回[[yii\db\ActiveQuery]]对象。

 - `$class`：关联模型类名，它必须是一个完全合格的类名。
 - `$link`: 两个表的关联列，应为键值对数组的形式。
   数组的键是 `$class` 关联表的列名，
   而数组值是关联类 $class 的列名。
   基于表外键定义关联关系是最佳实践。

建立关联关系后，获取关联数据和获取组件属性一样简单，
执行以下相应getter方法即可：

```php
//取得客户的订单
$customer = Customer::find(1);
$orders = $customer->orders; // $orders 是 Order 对象数组
```

以上代码实际执行了以下两条 SQL 语句：

```sql
SELECT * FROM tbl_customer WHERE id=1;
SELECT * FROM tbl_order WHERE customer_id=1;
```

> 提示:再次用表达式 `$customer->orders`将不会执行第二次 SQL 查询，
SQL 查询只在该表达式第一次使用时执行。
数据库访问只返回缓存在内部前一次取回的结果集，如果你想查询新的
关联数据，先要注销现有结果集：`unset($customer->orders);`。

有时候需要在关联查询中传递参数，如不需要返回客户全部订单，
只需要返回购买金额超过设定值的大订单，
通过以下 getter 方法声明一个关联数据 `bigOrders` ：

```php
class Customer extends \yii\db\ActiveRecord
{
	public function getBigOrders($threshold = 100)
	{
		return $this->hasMany(Order::className(), ['customer_id' => 'id'])
			->where('subtotal > :threshold', [':threshold' => $threshold])
			->orderBy('id');
	}
}
```

`hasMany()` 返回[[yii\db\ActiveQuery]]对象，该对象允许你通过
[[yii\db\ActiveQuery]]方法定制查询。

如上声明后，执行`$customer->bigOrders` 就返回
总额大于100的订单。使用以下代码更改设定值：

```php
$orders = $customer->getBigOrders(200)->all();
```

>注意：关联查询返回的是 [[yii\db\ActiveQuery]] 的实例，如果像特性（如类属性）那样连接关联数据，
返回的结果是关联查询的结果，即[[yii\db\ActiveRecord]]的实例，
或者是数组，或者是 null ，取决于关联关系的多样性。如，`$customer->getOrders()` 返回
`ActiveQuery` 实例，而 `$customer->orders` 返回`Order` 对象数组
（如果查询结果为空则返回空数组）。


中间表关联
--------------------------

有时，两个表通过中间表关联，定义这样的关联关系，
可以通过调用 [[yii\db\ActiveQuery::via()|via()]] 方法或[[yii\db\ActiveQuery::viaTable()|viaTable()]] 方法
来定制[[yii\db\ActiveQuery]]对象。

`tbl_order`表和`tbl_item`表通过中间表`tbl_order_item`关联，
可以在 `Order` 类声明 `items` 关联关系取代中间表：

```php
class Order extends \yii\db\ActiveRecord
{
	public function getItems()
	{
		return $this->hasMany(Item::className(), ['id' => 'item_id'])
			->viaTable('tbl_order_item', ['order_id' => 'id']);
	}
}
```

两个方法是相似的，除了
via() 方法的第一个参数是使用 AR 类中定义的关联名。
以上方法取代了中间表，等价于：

```php
class Order extends \yii\db\ActiveRecord
{
	public function getOrderItems()
	{
		return $this->hasMany(OrderItem::className(), ['order_id' => 'id']);
	}

	public function getItems()
	{
		return $this->hasMany(Item::className(), ['id' => 'item_id'])
			->via('orderItems');
	}
}
```

[pivot table]: http://en.wikipedia.org/wiki/Pivot_table "Pivot table（数据透视表，英文，维基百科）"


延迟加载和即时加载（又称惰性加载与贪婪加载）
----------------------

如前所述，当你第一次连接关联对象时， AR 将执行一个数据库查询
来检索请求数据并填充到关联对象的相应属性。
如果再次连接相同的关联对象，不再执行任何查询语句，这种数据库查询的执行方法称为“延迟加载”。如：

```php
// 被执行的 SQL 语句: SELECT * FROM tbl_customer WHERE id=1
$customer = Customer::find(1);
// 被执行的 SQL 语句: SELECT * FROM tbl_order WHERE customer_id=1
$orders = $customer->orders;
// 没有 SQL 语句被执行
$orders2 = $customer->orders; //取回上次查询的缓存数据
```

延迟加载非常实用，但是，在以下场景中使用延迟加载会遭遇性能问题：

```php
// 被执行的 SQL 语句: SELECT * FROM tbl_customer LIMIT 100
$customers = Customer::find()->limit(100)->all();

foreach ($customers as $customer) {
	// 100条 SQL 语句将被执行: SELECT * FROM tbl_order WHERE customer_id=...
	$orders = $customer->orders;
	// ...处理 $orders...
}
```

假设数据库查出的客户超过100个，以上代码将执行多少条 SQL 语句？
101 条！第一条 SQL 查询语句取回100个客户，然后，
每个客户要执行一条 SQL 查询语句以取回该客户的所有订单。

为解决以上性能问题，可以通过调用 [[yii\db\ActiveQuery::with()]] 方法使用*即时加载*解决。

```php
// SQL 语句被执行: SELECT * FROM tbl_customer LIMIT 100;
//               SELECT * FROM tbl_orders WHERE customer_id IN (1,2,...)
$customers = Customer::find()->limit(100)
	->with('orders')->all();

foreach ($customers as $customer) {
	// 没有 SQL 语句被执行
	$orders = $customer->orders;
	// ...处理 $orders...
}
```

如你所见，同样的任务只需要两个 SQL 语句。

> 须知:通常，即时加载 N 个关联关系而通过 `via()` 或者 `viaTable()` 定义了 M 个关联关系，
将有 1+M+N 条 SQL 查询语句被执行：一个查询取回主表行数，
一个查询给每一个 (M) 中间表，一个查询给每个 (N) 关联表。

> 注意:当用即时加载定制 `select()` 时，确保连接
到关联模型的列都被包括了，否则，关联模型不会载入。如：

```php
$orders = Order::find()->select(['id', 'amount'])->with('customer')->all();
// $orders[0]->customer 总是空的，使用以下代码解决这个问题：
$orders = Order::find()->select(['id', 'amount', 'customer_id'])->with('customer')->all();
```

有时候，你想自由的自定义关联查询，
延迟加载和即时加载都可以实现，如：

```php
$customer = Customer::find(1);
// 惰性加载：SELECT * FROM tbl_order WHERE customer_id=1 AND subtotal>100
$orders = $customer->getOrders()->where('subtotal>100')->all();

// 贪婪加载：SELECT * FROM tbl_customer LIMIT 100
//                SELECT * FROM tbl_order WHERE customer_id IN (1,2,...) AND subtotal>100
$customers = Customer::find()->limit(100)->with([
	'orders' => function($query) {
		$query->andWhere('subtotal>100');
	},
])->all();
```


逆关系
-----------------

关联关系通常成对定义，如， `Customer` 可以有个名为 `orders` 关联项，
而 `Order` 也有个名为`customer` 的关联项：

```php
class Customer extends ActiveRecord
{
	....
	public function getOrders()
	{
		return $this->hasMany(Order::className(), ['customer_id' => 'id']);
	}
}

class Order extends ActiveRecord
{
	....
	public function getCustomer()
	{
		return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
	}
}
```

如果我们执行以下查询，可以发现订单的 `customer` 和 
找到这些订单的客户对象并不是同一个。连接 `customer->orders` 将触发一条 SQL 语句
而连接一个订单的 `customer` 将触发另一条 SQL 语句。

```php
// SELECT * FROM tbl_customer WHERE id=1
$customer = Customer::find(1);
// echoes "not equal"
// SELECT * FROM tbl_order WHERE customer_id=1
// SELECT * FROM tbl_customer WHERE id=1
if ($customer->orders[0]->customer === $customer) {
	echo 'equal';
} else {
	echo 'not equal';
}
```

为避免多余执行的后一条语句，我们可以为 `customer`或  `orders` 关联关系定义相反的关联关系，
通过调用 [[yii\db\ActiveQuery::inverseOf()|inverseOf()]] 方法可以实现。

```php
class Customer extends ActiveRecord
{
	....
	public function getOrders()
	{
		return $this->hasMany(Order::className(), ['customer_id' => 'id'])->inverseOf('customer');
	}
}
```

现在我们同样执行上面的查询，我们将得到：

```php
// SELECT * FROM tbl_customer WHERE id=1
$customer = Customer::find(1);
// 输出相同
// SELECT * FROM tbl_order WHERE customer_id=1
if ($customer->orders[0]->customer === $customer) {
	echo 'equal';
} else {
	echo 'not equal';
}
```

以上我们展示了如何在延迟加载中使用相对关联关系，
相对关系也可以用在即时加载中：

```php
// SELECT * FROM tbl_customer
// SELECT * FROM tbl_order WHERE customer_id IN (1, 2, ...)
$customers = Customer::find()->with('orders')->all();
// 输出相同
if ($customers[0]->orders[0]->customer === $customers[0]) {
	echo 'equal';
} else {
	echo 'not equal';
}
```

> 注意:相对关系不能在包含中间表的关联关系中定义。
> 即是，如果你的关系是通过 [[yii\db\ActiveQuery::via()|via()]] 或 [[yii\db\ActiveQuery::viaTable()|viaTable()]] 方法定义的，
> 就不能调用 [[yii\db\ActiveQuery::inverseOf()]] 方法了。


 JOIN 类型关联查询
----------------------

使用关系数据库时，普遍要做的是连接多个表并明确地运用各种 JOIN 查询。
JOIN SQL语句的查询条件和参数，使用 [[yii\db\ActiveQuery::joinWith()]]
可以重用已定义关系并调用
而不是使用 [[yii\db\ActiveQuery::join()]] 来实现目标。

```php
// 查找所有订单并以客户 ID 和订单 ID 排序，并贪婪加载 "customer" 表
$orders = Order::find()->joinWith('customer')->orderBy('tbl_customer.id, tbl_order.id')->all();
// 查找包括书籍的所有订单，并以 `INNER JOIN` 的连接方式即时加载 "books" 表
$orders = Order::find()->innerJoinWith('books')->all();
```

以上，方法 [[yii\db\ActiveQuery::innerJoinWith()|innerJoinWith()]] 是访问 `INNER JOIN` 类型的  [[yii\db\ActiveQuery::joinWith()|joinWith()]] 的快捷方式
。


可以连接一个或多个关联关系，可以自由使用查询条件到关联查询，
也可以嵌套连接关联查询。如：

```php
// 连接多重关系
// 找出24小时内注册客户包含书籍的订单
$orders = Order::find()->innerJoinWith([
	'books',
	'customer' => function ($query) {
		$query->where('tbl_customer.created_at > ' . (time() - 24 * 3600));
	}
])->all();
// 连接嵌套关系：连接 books 表及其 author 列
$orders = Order::find()->joinWith('books.author')->all();
```

代码背后， Yii 先执行一条 JOIN SQL 语句把满足 JOIN SQL 语句查询条件的主要模型查出，
然后为每个关系执行一条查询语句，
bing填充相应的关联记录。

[[yii\db\ActiveQuery::joinWith()|joinWith()]] 和  [[yii\db\ActiveQuery::with()|with()]] 的区别是
前者连接主模型类和关联模型类的数据表来检索主模型，
而后者只查询和检索主模型类。
检索主模型

由于这个区别，你可以应用只针对一条 JOIN SQL 语句起效的查询条件。
如，通过关联模型的查询条件过滤主模型，如前例，
可以使用关联表的列来挑选主模型数据，

当使用 [[yii\db\ActiveQuery::joinWith()|joinWith()]] 方法时可以响应没有歧义的列名。
上例可以使用 `tbl_item.id` 和 `tbl_order.id` 来消除 `id` 列指代的歧义。 
因为订单表和项目表都包括 `id` 列。

当连接关联关系时，关联关系默认使用即时加载。你可以
通过传参数 `$eagerLoading` 来决定在指定关联查询中是否使用即时加载。

默认 [[yii\db\ActiveQuery::joinWith()|joinWith()]] 使用左连接来连接关联表。
你也可以传 `$joinType` 参数来定制连接类型。
你也可以使用 [[yii\db\ActiveQuery::innerJoinWith()|innerJoinWith()]]。

以下是 `INNER JOIN` 的简短例子：

```php
// 查找包括书籍的所有订单，但 "books" 表不使用即时加载
$orders = Order::find()->innerJoinWith('books', false)->all();
// 等价于：
$orders = Order::find()->joinWith('books', false, 'INNER JOIN')->all();
```

有时连接两个表时，需要在关联查询的 ON 部分指定额外条件。
这可以通过调用 [[yii\db\ActiveQuery::onCondition()]] 方法实现：

```php
class User extends ActiveRecord
{
	public function getBooks()
	{
		return $this->hasMany(Item::className(), ['owner_id' => 'id'])->onCondition(['category_id' => 1]);
	}
}
```

在上面， [[yii\db\ActiveRecord::hasMany()|hasMany()]] 方法回传了一个 [[yii\db\ActiveQuery]] 对象，
当你用 [[yii\db\ActiveQuery::joinWith()|joinWith()]] 执行一条查询时，取决于正被调用的是哪个 [[yii\db\ActiveQuery::onCondition()|onCondition()]]，
返回 `category_id` 为 1 的 items 

当你用 [[yii\db\ActiveQuery::joinWith()|joinWith()]] 进行一次查询时，“on-condition”条件会被放置在相应查询语句的 ON 部分。
如：

```php
// SELECT tbl_user.* FROM tbl_user LEFT JOIN tbl_item ON tbl_item.owner_id=tbl_user.id AND category_id=1
// SELECT * FROM tbl_item WHERE owner_id IN (...) AND category_id=1
$users = User::find()->joinWith('books')->all();
```

注意：如果通过 [[yii\db\ActiveQuery::with()]] 进行贪婪加载或使用惰性加载的话，则 on 条件会被放置在对应 SQL语句的 `WHERE` 部分。
因为，此时此处并没有发生 JOIN 查询。比如：

```php
// SELECT * FROM tbl_user WHERE id=10
$user = User::find(10);
// SELECT * FROM tbl_item WHERE owner_id=10 AND category_id=1
$books = $user->books;
```


关联表操作
--------------------------

ActiveRecord 提供下列两个方法来建立或移除
两个 ActiveRecord 对象之间的关系。

- [[yii\db\ActiveRecord::link()|link()]]
- [[yii\db\ActiveRecord::unlink()|unlink()]]

如，给定一个客户和一个新订单，我们可以使用以下代码
把订单和客户关联起来：

```php
$customer = Customer::find(1);
$order = new Order();
$order->subtotal = 100;
$customer->link('orders', $order);
```

上面调用的 [[yii\db\ActiveRecord::link()|link()]] 会设置 order 的 `customer_id` 为主键
$customer 的值，然后调用 [[yii\db\ActiveRecord::save()|save()]] 方法保存订单到数据库。


ActiveRecord 对象的生命周期
-------------------------------------

ActiveRecord 的对象在不同情况下被使用时，有不同的生命周期。
AR 或其子类行为可以在其生命周期通过
覆写方法和事件处理机制来注入定制代码。

以 AR 实例为例，生命周期如下：

1.构造函数
2. [[yii\db\ActiveRecord::init()|init()]] 初始化方法将触发一个 [[yii\db\ActiveRecord::EVENT_INIT|EVENT_INIT]] 事件。

通过 [[yii\db\ActiveRecord::find()|find()]] 方法得到的 AR 实例的生命周期：

1.构造函数
2. [[yii\db\ActiveRecord::init()|init()]] 初始化方法将触发一个 [[yii\db\ActiveRecord::EVENT_INIT|EVENT_INIT]] 事件。
3.  [[yii\db\ActiveRecord::afterFind()|afterFind()]] 将触发[[yii\db\ActiveRecord::EVENT_AFTER_FIND|EVENT_AFTER_FIND]]事件

当调用[[yii\db\ActiveRecord::save()|save()]] 方法插入或更新一条 AR 记录时，将经历以下生命周期：

1. [[yii\db\ActiveRecord::beforeValidate()|beforeValidate()]] 会触发[[yii\db\ActiveRecord::EVENT_BEFORE_VALIDATE|EVENT_BEFORE_VALIDATE]]事件
2. [[yii\db\ActiveRecord::afterValidate()|afterValidate()]] 会触发 [[yii\db\ActiveRecord::EVENT_AFTER_VALIDATE|EVENT_AFTER_VALIDATE]] 事件
3. [[yii\db\ActiveRecord::beforeSave()|beforeSave()]] 触发 [[yii\db\ActiveRecord::EVENT_BEFORE_INSERT|EVENT_BEFORE_INSERT]] 事件或[[yii\db\ActiveRecord::EVENT_BEFORE_UPDATE|EVENT_BEFORE_UPDATE]] 事件
4.执行数据插入或更新
5. [[yii\db\ActiveRecord::afterSave()|afterSave()]]: 触发 [[yii\db\ActiveRecord::EVENT_AFTER_INSERT|EVENT_AFTER_INSERT]] 或 [[yii\db\ActiveRecord::EVENT_AFTER_UPDATE|EVENT_AFTER_UPDATE]] 事件

当调用 [[yii\db\ActiveRecord::delete()|delete()]] 方法删除 AR 数据时所经历的生命周期如下：

1. [[yii\db\ActiveRecord::beforeDelete()|beforeDelete()]]:触发[[yii\db\ActiveRecord::EVENT_BEFORE_DELETE|EVENT_BEFORE_DELETE]] 事件
2.执行数据删除
3. [[yii\db\ActiveRecord::afterDelete()|afterDelete()]]: 触发[[yii\db\ActiveRecord::EVENT_AFTER_DELETE|EVENT_AFTER_DELETE]] 事件


作用域
------

当调用[[yii\db\ActiveRecord::find()|find()]]或[[yii\db\ActiveRecord::findBySql()|findBySql()]]方法，
将返回[[yii\db\ActiveQuery|ActiveQuery]] 实例。
你也可以调用其他方法，如 [[yii\db\ActiveQuery::where()|where()]], [[yii\db\ActiveQuery::orderBy()|orderBy()]],
以更细化查询条件。

有可能需要不同地方多次调用同一个查询方法集合，这种情况，
可以考虑定义一个所谓的作用域（*scopes*），作用域本质上也是一个方法，定义在一个自定的查询类中，这个类
调用了一系列的查询方法来修正查询对象，使用作用域方法如同调用一个普通查询方法一样。

定义一个作用域方法需要两个步骤，首先为模型创建一个自定的查询类并在此类定义必须的作用域方法。
如，为 `Comment` 模型创建 `CommentQuery` 类，
 定义`active()`作用域方法如下：

```php
namespace app\models;

use yii\db\ActiveQuery;

class CommentQuery extends ActiveQuery
{
	public function active($state = true)
	{
		$this->andWhere(['active' => $state]);
		return $this;
	}
}
```

重点是：

1. 类必须继承自 `yii\db\ActiveQuery`或其子类。
2.方法必须是公开的并返回 `$this` 以便方法链成立。可以接收参数。
3.确认 [[yii\db\ActiveQuery]] 方法对修改查询条件非常有用。

其次，覆写 [[yii\db\ActiveRecord::createQuery()]] 方法以便可以使用自定的查询类而不是默认的 [[yii\db\ActiveQuery|ActiveQuery]] 类。
以下是示例：

```php
namespace app\models;

use yii\db\ActiveRecord;

class Comment extends ActiveRecord
{
	public static function createQuery($config = [])
	{
		$config['modelClass'] = get_called_class();
		return new CommentQuery($config);
	}
}
```

就这样。现在你可以使用自定的作用域方法了：

```php
$comments = Comment::find()->active()->all();
$inactiveComments = Comment::find()->active(false)->all();
```

当定义关联关系时也可以使用作用域，如：

```php
class Post extends \yii\db\ActiveRecord
{
	public function getActiveComments()
	{
		return $this->hasMany(Comment::className(), ['post_id' => 'id'])->active();

	}
}
```

或当执行关联查询时使用作用域传输：

```php
$posts = Post::find()->with([
	'comments' => function($q) {
		$q->active();
	}
])->all();
```


### 让 IDE 更好地支持

为了让现代 IDE 自动完成更智能，你需要为一些模型和查询方法覆写返回类型，
如下：

```php
/**
 * @method \app\models\CommentQuery|static|null find($q = null) static
 * @method \app\models\CommentQuery findBySql($sql, $params = []) static
 */
class Comment extends ActiveRecord
{
	// ...
}
```

```php
/**
 * @method \app\models\Comment|array|null one($db = null)
 * @method \app\models\Comment[]|array all($db = null)
 */
class CommentQuery extends ActiveQuery
{
	// ...
}
```

### 默认作用域

 如果你以前曾用过 Yii 1.1,你已经了解一个缺省作用域的概念。缺省作用域就是对所有的数据库查询生效的作用域。
 你可以通过覆写 [[yii\db\ActiveRecord::createQuery()]] 方法来自定义缺省作用域，如

```php
public static function createQuery($config = [])
{
	$config['modelClass'] = get_called_class();
	return (new ActiveQuery($config))->where(['deleted' => false]);
}
```

注意现在你的所有查询都不能使用[[yii\db\ActiveQuery::where()|where()]]方法，
只能使用 [[yii\db\ActiveQuery::where()|where()]] 和 [[yii\db\ActiveQuery::orWhere()|orWhere()]] 方法，
以避免覆写了缺省条件。


事务处理
------------------------

当一些 DB 操作是相关的且被同时执行

TODO: FIXME: WIP, TBD, https://github.com/yiisoft/yii2/issues/226

,
[[yii\db\ActiveRecord::afterSave()|afterSave()]], [[yii\db\ActiveRecord::beforeDelete()|beforeDelete()]] 和 [[yii\db\ActiveRecord::afterDelete()|afterDelete()]] 生命周期方法. 
开发者的解决方案是通过数据库事务包覆写 [[yii\db\ActiveRecord::save()|save()]] 方法
甚至在控制器功能方法中使用事务，这个解决方式严格来说不是最佳实践
(违背了 “小控制器大模型”的基本规则）。

以下就是这些方式（**不要** 使用，除非你确定你真的需要这么做）。模型：

```php
class Feature extends \yii\db\ActiveRecord
{
	// ...

	public function getProduct()
	{
		return $this->hasOne(Product::className(), ['product_id' => 'id']);
	}
}

class Product extends \yii\db\ActiveRecord
{
	// ...

	public function getFeatures()
	{
		return $this->hasMany(Feature::className(), ['id' => 'product_id']);
	}
}
```

重写 [[yii\db\ActiveRecord::save()|save()]] 方法：

```php

class ProductController extends \yii\web\Controller
{
	public function actionCreate()
	{
		// FIXME: TODO: WIP, TBD
	}
}
```

控制器层面使用事务处理

```php
class ProductController extends \yii\web\Controller
{
	public function actionCreate()
	{
		// FIXME: TODO: WIP, TBD
	}
}
```

代替以上弱相关的方法，可以使用原子级场景和操作特性。

```php
class Feature extends \yii\db\ActiveRecord
{
	// ...

	public function getProduct()
	{
		return $this->hasOne(Product::className(), ['product_id' => 'id']);
	}

	public function scenarios()
	{
		return [
			'userCreates' => [
				'attributes' => ['name', 'value'],
				'atomic' => [self::OP_INSERT],
			],
		];
	}
}

class Product extends \yii\db\ActiveRecord
{
	// ...

	public function getFeatures()
	{
		return $this->hasMany(Feature::className(), ['id' => 'product_id']);
	}

	public function scenarios()
	{
		return [
			'userCreates' => [
				'attributes' => ['title', 'price'],
				'atomic' => [self::OP_INSERT],
			],
		];
	}

	public function afterValidate()
	{
		parent::afterValidate();
		// FIXME: TODO: WIP, TBD
	}

	public function afterSave($insert)
	{
		parent::afterSave($insert);
		if ($this->getScenario() === 'userCreates') {
			// FIXME: TODO: WIP, TBD
		}
	}
}
```

控制器非常简洁：

```php
class ProductController extends \yii\web\Controller
{
	public function actionCreate()
	{
		// FIXME: TODO: WIP, TBD
	}
}
```

Optimistic Locks
----------------

TODO

被污染属性
----------------

TODO

另见
--------

- [Model](model.md)
- [[yii\db\ActiveRecord]]
