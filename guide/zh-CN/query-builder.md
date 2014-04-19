SQL查询生成器和查询
=======================

Yii 提供了基本的数据访问层，描述在[数据库基础](database-basics.md)部分。数据库访问层提供了数据库交互的底层方式，虽然一些情况很有用，但写原生的 SQL 语句容易出错、令人生厌。另一个可选的方案是使用查询生成器。查询生成器以面向对象的方式生成待执行的查询语句。

SQL查询生成器的典型用法如下：

```php
$rows = (new \yii\db\Query())
    ->select('id, name')
    ->from('user')
    ->limit(10)
    ->all();

// 等价于以下代码：

$query = (new \yii\db\Query())
    ->select('id, name')
    ->from('user')
    ->limit(10);

// 创建命令，可以通过 $command->sql 来查看真正的 SQL 语句。
$command = $query->createCommand();

// 执行命令：
$rows = $command->queryAll();
```

查询方法
-------------


如你所见，[[yii\db\Query]]似乎是需要处理的主角。但背后`Query` 实际只负责表示各种查询信息。真正生成查询SQL
的逻辑由[[yii\db\QueryBuilder]]调用 `createCommand()` 方法实现，而查询执行由[[yii\db\Command]]完成。

为方便起见，[[yii\db\Query]]提供了一系列常用查询方法来生成查询、执行查询和返回查询结果。如，

- [[yii\db\Query::all()|all()]]: 生成和执行查询并返回数组形式的所有查询结果。
- [[yii\db\Query::one()|one()]]: 返回结果集的第一行。
- [[yii\db\Query::column()|column()]]: 返回结果集的第一列。
- [[yii\db\Query::scalar()|scalar()]]: 返回结果集第一行的第一列
- [[yii\db\Query::exists()|exists()]]: 返回指明查询结果是否存在的值。
- [[yii\db\Query::count()|count()]]: 返回 `COUNT` 查询的结果。其他相似的方法包括 `sum()`, `average()`, `max()`, `min()`, 这些方法支持所谓数据的聚集查询。


生成查询语句
--------------

以下将介绍如何生成各种 SQL 语句从句。为了简单起见，使用 `$query` 代表[[yii\db\Query]]对象。

### `SELECT`

为形成基本的 `SELECT` 查询语句，需要指定从哪个表选择什么列：

```php
$query->select('id, name')
    ->from('user');
```

Select 选项可指定为如上逗号分隔的字符串，或指定为数组。数组在形成动态 select 查询语句特别有用：

```php
$query->select(['id', 'name'])
    ->from('user');
```

> 信息：如果 `SELECT` 从句包括 SQL 表达式，应该总是使用数组格式。
> 因为 SQL 表达式 如 `CONCAT(first_name, last_name) AS full_name` 可能包括逗号。
> 如果把该表达式和其他 columns 列排在一个字符串，该表达式将被逗号分离成你不希望看到的好几个部分。

指定列可以包括表前缀或列别名，如 `user.id`, `user.id AS user_id` 。如使用数组指定列，也可以使用数组的键来指明列别名，如 `['user_id' => 'user.id', 'user_name' => 'user.name']`。

要选择不同的行，可以调用 `distinct()` ：

```php
$query->select('user_id')->distinct()->from('post');
```

### `FROM`

要指定从哪个表选择数据，调用 `from()`：

```php
$query->select('*')->from('user');
```

可以使用逗号分隔的字符串或数组指定多个表。表名可以包括模式前缀（如 `'public.user'`）和表别名（如 `'user u'`）。from()方法会自动引用表名，除非表名包括圆括号（说明所给的表是子查询或 DB 表达式）。如：

```php
$query->select('u.*, p.*')->from(['user u', 'post p']);
```

当表以数组形式指明，可以使用数组键作为表别名（如果表不需要别名，不要使用字符串形式的键）。如：

```php
$query->select('u.*, p.*')->from(['u' => 'user u', 'p' => 'post']);
```

使用 `Query` 对象指定子查询。这种情况，相应的数组键将用作子查询的别名：

```php
$subQuery = (new Query())->select('id')->from('user')->where('status=1');
$query->select('*')->from(['u' => $subQuery]);
```


### `WHERE`

通常数据基于一定的条件来筛选。查询生成器有一些有用的方法来指定这些条件（标准），其中最强大的是 `where` 方法。这个方法可以多种方式使用。

应用条件最简单的方式是使用字符串：

```php
$query->where('status=:status', [':status' => $status]);
```

使用字符串须确保是绑定查询参数而不是用字符串们来创建查询。以上方法的使用是安全的，而以下则不安全：

```php
$query->where("status=$status"); // 危险！
```

取代直接绑定状态值可以使用 `params` 或 `addParams` 来完成：

```php
$query->where('status=:status');
$query->addParams([':status' => $status]);
```

在 `where` 同时设置多个条件可以使用 *哈希格式*:


```php
$query->where([
    'status' => 10,
    'type' => 2,
    'id' => [4, 8, 15, 16, 23, 42],
]);
```

以上代码将生成下面的 SQL 语句：

```sql
WHERE (`status` = 10) AND (`type` = 2) AND (`id` IN (4, 8, 15, 16, 23, 42))
```

在数据库中 NULL 是个特殊值，也可以用查询生成器漂亮的处理。代码如下：

```php
$query->where(['status' => null]);
```

以上 WHERE 从句的结果是：

```sql
WHERE (`status` IS NULL)
```

另一个使用此方法的方式是 `[操作符, 操作数1, 操作数2, ...]` 这样的操作格式。

操作符可以是以下之一：

- `and`: 操作数用 `AND` 连结。例如，`['and', 'id=1', 'id=2']` 将生成 `id=1 AND id=2`。如果操作数是数组，将使用以下规则转换为字符串。如，`['and', 'type=1', ['or', 'id=1', 'id=2']]` 将生成 `type=1 AND (id=1 OR id=2)`。方法将 *不做* 任何转义或引用。
- `or`: 和 `and` 操作符相似，除了操作数用 `OR` 连结。
- `between`: 操作数1是列名，操作数2和3是列所在范围的初值和末值。如， `['between', 'id', 1, 10]` 将生成 `id BETWEEN 1 AND 10` 。
- `not between`:  和 `between` 类似，除了在生成的条件用 `NOT BETWEEN` 替换 `BETWEEN` 。
- `in`: 操作数 1 应是一个列或 DB 表达式，操作符 2 应是代表列或 DB 表达式所在取值范围的数组。如， `['in', 'id', [1, 2, 3]]` 将生成 `id IN (1, 2, 3)`。where() 方法将会引用恰当的列名并转义范围里的值。
- `not in`: 和 `in` 操作符类似，除了在生成的条件中用 `NOT IN` 替换 `IN` 。
- `like`: 操作数 1 应是一个列或 DB 表达式，而操作数 2 是字符串或数组，表示要 like 的列值或 DB 表达式。如， `['like', 'name', 'tester']` 将生成 `name LIKE '%tester%'`。当取值范围以数组形式给定，多个 `LIKE` 判断从句将生成并用 `AND` 连结。例如， `['like', 'name', ['test', 'sample']]` 将生成 `name LIKE '%test%' AND name LIKE '%sample%'`。也可以提供可选的第三个操作数来指定在值里如何转义特定字符。该操作数应是映射特定字符到其相对的转义字符的数组。如果该操作数没有提供，将使用默认的转义映射表。要使用 `false` 或空数组来表明值已经转义，没有需要转义的字符。注意当使用默认转义映射表（或第三个操作数未提供），值将被自动以半角字符来转义。
- `or like`: 和 `like` 操作符类似，除了当操作符 2 是数组时用 `OR` 连结 `LIKE` 判断从句。
- `not like`: 和 `like` 操作符类似，除了在生成的条件中用 `NOT LIKE` 取代 `LIKE` 。
- `or not like`: 和 `not like` 操作符类似，除了  `OR` 用来连结 `NOT LIKE` 判断从句.
- `exists`: 要求一个操作数必须是[[yii\db\Query]]实例来表示子查询。该操作符将建立 `EXISTS (sub-query)` 表达式。
- `not exists`: 和 `exists` 操作符类似，也会创建一个 `NOT EXISTS (sub-query)` 表达式。

如要动态建立条件的以上各部分，用 `andWhere()` 或 `orWhere()` 是非常方便的：

```php
$status = 10;
$search = 'yii';

$query->where(['status' => $status]);
if (!empty($search)) {
    $query->andWhere(['like', 'title', $search]);
}
```

在这里 `$search` 不能为空并生成以下 SQL 语句：

```sql
WHERE (`status` = 10) AND (`title` LIKE '%yii%')
```

#### 建立过滤条件

基于用户输入建立过滤条件，通常希望用忽略过滤器中的 “空输入” 进行特别地处理。如， HTML 表单有用户名和电子邮箱输入项，当用户只输入用户名时，我们将尝试只建立匹配用户名的查询，使用 `filterWhere()` 来实现这个目标：

```php
// $username and $email 来自用户输入
$query->filterWhere([
    'username' => $username,
    'email' => $email,
]);
```

 `filterWhere()` 方法和 `where()` 非常相似。 `filterWhere()` 最大的区别是从提供的条件中移除空值。所以，如果 `$email` 为空，得到的查询是 `...WHERE username=:username`，如果 `$username` 和 `$email` 都为空，查询语句将没有 `WHERE` 部分。

如果值是 null、空字符串、空格组成的字符串或空数组，那么值就是 *空* 。
也可以使用 `andFilterWhere()` 和 `orFilterWhere()` 附加更多的过滤条件。

### `ORDER BY`

对结果排序使用 `orderBy` 和 `addOrderBy` ：

```php
$query->orderBy([
    'id' => SORT_ASC,
    'name' => SORT_DESC,
]);
```

以上代码将升序排列 `id` 列然后降序排列 `name` 列。

```

### `GROUP BY` 和 `HAVING`

添加 `GROUP BY` 到生成的 SQL ，可以使用以下代码：


```php
$query->groupBy('id, status');
```

使用 `groupBy` 后添加其他字段：

```php
$query->addGroupBy(['created_at', 'updated_at']);
```

使用 `having` 方法和其 `andHaving` 及 `orHaving` 来添加 `HAVING` 条件。这些方法的参数类似于 `where` 方法群的参数：

```php
$query->having(['status' => $status]);
```

### `LIMIT` 和 `OFFSET`

要限制查询结果只取10行，可以使用 `limit` ：

```php
$query->limit(10);
```

要跳过前100行使用：

```php
$query->offset(100);
```

### `JOIN`

 `JOIN` 从句可使用恰当的 join 方法在查询生成器生成：

- `innerJoin()`
- `leftJoin()`
- `rightJoin()`

左连接在一条查询中从两个相关表筛选数据：

```php
$query->select(['user.name AS author', 'post.title as title'])
    ->from('user')
    ->leftJoin('post', 'post.user_id = user.id');
```

以上代码， `leftJoin()` 方法的第一个参数指定连接的表，第二个参数定义连接条件。

如果你的数据库应用支持其他连接类型，可以用统一 `join` 方法使用它们：

```php
$query->join('FULL OUTER JOIN', 'post', 'post.user_id = user.id');
```

第一个参数是要执行的连接类型，第二个是要连接的表，第三个是条件。

像 `FROM` 一样，可以连接子查询。要这样做，指定子查询为包括至少一个元素的数组即可。数组值必须是 `Query` 对象，代表子查询，而数组键是子查询的别名。例如：

```php
$query->leftJoin(['u' => $subQuery], 'u.id=author_id');
```


### `UNION`

SQL 的`UNION` 添加一个查询的结果到另一个查询结果。返回的列必须匹配两个查询。Yii 里要建立 `UNION` ，先形成两个查询对象，然后使用 `union` 方法：

```php
$query = new Query();
$query->select("id, 'post' as type, name")->from('post')->limit(10);

$anotherQuery = new Query();
$anotherQuery->select('id, 'user' as type, name')->from('user')->limit(10);

$query->union($anotherQuery);
```


批（量）查询
-----------

处理大数据量的时候，类似[[yii\db\Query::all()]]这样的方法并不太合适，因为这些方法要求加载所有数据到内存。为保持低内存要求， Yii 提供了所谓的批查询支持。批查询利用数据指针分批取数据。

批查询这样使用：

```php
use yii\db\Query;

$query = (new Query())
    ->from('user')
    ->orderBy('id');

foreach ($query->batch() as $users) {
    // $users 是用户表的100行以内的数组
}

// 或者你希望逐行遍历（iterate，有时翻译为迭代）
foreach ($query->each() as $user) {
    // $user 表示用户表的一行数据
}
```

[[yii\db\Query::batch()]] 和 [[yii\db\Query::each()]]方法返回[[yii\db\BatchQueryResult]]对象，该对象实现了 `Iterator` （迭代器）接口，因此可以用于 `foreach` 结构。在第一个遍历过程，建立了一条 SQL 查询到数据库，然后数据在这个迭代中被批量取回。一批数量缺省是100行，即每批取回100行数据。可以通过传递第一个参数到 `batch()` 和 `each()` 方法改变一批取回的数量。

对比[[yii\db\Query::all()]]，批查询一次只加载100行数据到内存。如果你处理完数据马上丢弃，批查询可以帮助保持内存在限定范围内使用。

如需指定查询结果用[[yii\db\Query::indexBy()]]方法以某列来索引，批查询仍将保持本来的索引。例如：

```php
use yii\db\Query;

$query = (new Query())
    ->from('user')
    ->indexBy('username');

foreach ($query->batch() as $users) {
    // $users 以 "username" 列为索引
}

foreach ($query->each() as $username => $user) {
}
```
