查询生成器和查询
=======================

Yii 提供了基本的数据访问层，描述在[数据库基础](database-basics.md)部分。数据库访问层提供了数据库交互的底层方式，虽然一些情况很有用，但写原生的 SQL 语句容易出错、令人生厌。另一个可选的方案是使用查询生成器。查询生成器以面向对象的方式生成待执行的查询语句。

查询生成器的典型用法如下：

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


如你所见，[[yii\db\Query]]似乎是需要处理的主角。但背后`Query` 实际只负责表示各种查询信息。真正生成查询
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

You may specify multiple tables using a comma-separated string or an array.
Table names can contain schema prefixes (e.g. `'public.user'`) and/or table aliases (e.g. `'user u'`).
The method will automatically quote the table names unless it contains some parenthesis
(which means the table is given as a sub-query or DB expression). For example,


```php
$query->select('u.*, p.*')->from(['user u', 'post p']);
```

When the tables are specified as an array, you may also use the array keys as the table aliases
(if a table does not need alias, do not use a string key). For example,

```php
$query->select('u.*, p.*')->from(['u' => 'user u', 'p' => 'post']);
```

You may specify a sub-query using a `Query` object. In this case, the corresponding array key will be used
as the alias for the sub-query.

```php
$subQuery = (new Query())->select('id')->from('user')->where('status=1');
$query->select('*')->from(['u' => $subQuery]);
```


### `WHERE`

Usually data is selected based upon certain criteria. Query Builder has some useful methods to specify these, the most powerful of which being `where`. It can be used in multiple ways.

The simplest way to apply a condition is to use a string:

```php
$query->where('status=:status', [':status' => $status]);
```

When using strings, make sure you're binding the query parameters, not creating a query by string concatenation. The above approach is safe to use, the following is not:

```php
$query->where("status=$status"); // Dangerous!
```

Instead of binding the status value immediately, you can do so using `params` or `addParams`:

```php
$query->where('status=:status');
$query->addParams([':status' => $status]);
```

Multiple conditions can simultaneously be set in `where` using the *hash format*:

```php
$query->where([
    'status' => 10,
    'type' => 2,
    'id' => [4, 8, 15, 16, 23, 42],
]);
```

That code will generate the following SQL:

```sql
WHERE (`status` = 10) AND (`type` = 2) AND (`id` IN (4, 8, 15, 16, 23, 42))
```

NULL is a special value in databases, and is handled smartly by the Query Builder. This code:

```php
$query->where(['status' => null]);
```

results in this WHERE clause:

```sql
WHERE (`status` IS NULL)
```

Another way to use the method is the operand format which is `[operator, operand1, operand2, ...]`.

Operator can be one of the following:

- `and`: the operands should be concatenated together using `AND`. For example,
  `['and', 'id=1', 'id=2']` will generate `id=1 AND id=2`. If an operand is an array,
  it will be converted into a string using the rules described here. For example,
  `['and', 'type=1', ['or', 'id=1', 'id=2']]` will generate `type=1 AND (id=1 OR id=2)`.
  The method will NOT do any quoting or escaping.
- `or`: similar to the `and` operator except that the operands are concatenated using `OR`.
- `between`: operand 1 should be the column name, and operand 2 and 3 should be the
   starting and ending values of the range that the column is in.
   For example, `['between', 'id', 1, 10]` will generate `id BETWEEN 1 AND 10`.
- `not between`: similar to `between` except the `BETWEEN` is replaced with `NOT BETWEEN`
  in the generated condition.
- `in`: operand 1 should be a column or DB expression, and operand 2 be an array representing
  the range of the values that the column or DB expression should be in. For example,
  `['in', 'id', [1, 2, 3]]` will generate `id IN (1, 2, 3)`.
  The method will properly quote the column name and escape values in the range.
- `not in`: similar to the `in` operator except that `IN` is replaced with `NOT IN` in the generated condition.
- `like`: operand 1 should be a column or DB expression, and operand 2 be a string or an array representing
  the values that the column or DB expression should be like.
  For example, `['like', 'name', 'tester']` will generate `name LIKE '%tester%'`.
  When the value range is given as an array, multiple `LIKE` predicates will be generated and concatenated
  using `AND`. For example, `['like', 'name', ['test', 'sample']]` will generate
  `name LIKE '%test%' AND name LIKE '%sample%'`.
  You may also provide an optional third operand to specify how to escape special characters in the values.
  The operand should be an array of mappings from the special characters to their
  escaped counterparts. If this operand is not provided, a default escape mapping will be used.
  You may use `false` or an empty array to indicate the values are already escaped and no escape
  should be applied. Note that when using an escape mapping (or the third operand is not provided),
  the values will be automatically enclosed within a pair of percentage characters.
- `or like`: similar to the `like` operator except that `OR` is used to concatenate the `LIKE`
  predicates when operand 2 is an array.
- `not like`: similar to the `like` operator except that `LIKE` is replaced with `NOT LIKE`
  in the generated condition.
- `or not like`: similar to the `not like` operator except that `OR` is used to concatenate
  the `NOT LIKE` predicates.
- `exists`: requires one operand which must be an instance of [[yii\db\Query]] representing the sub-query.
  It will build a `EXISTS (sub-query)` expression.
- `not exists`: similar to the `exists` operator and builds a `NOT EXISTS (sub-query)` expression.

If you are building parts of condition dynamically it's very convenient to use `andWhere()` and `orWhere()`:

```php
$status = 10;
$search = 'yii';

$query->where(['status' => $status]);
if (!empty($search)) {
    $query->andWhere(['like', 'title', $search]);
}
```

In case `$search` isn't empty the following SQL will be generated:

```sql
WHERE (`status` = 10) AND (`title` LIKE '%yii%')
```

#### Building Filter Conditions

When building filter conditions based on user inputs, you usually want to specially handle "empty inputs"
by ignoring them in the filters. For example, you have an HTML form that takes username and email inputs.
If the user only enters something in the username input, you may want to build a query that only tries to
match the entered username. You may use the `filterWhere()` method achieve this goal:

```php
// $username and $email are from user inputs
$query->filterWhere([
    'username' => $username,
    'email' => $email,
]);
```

The `filterWhere()` method is very similar to `where()`. The main difference is that `filterWhere()`
will remove empty values from the provided condition. So if `$email` is "empty", the resulting query
will be `...WHERE username=:username`; and if both `$username` and `$email` are "empty", the query
will have no `WHERE` part.

A value is *empty* if it is null, an empty string, a string consisting of whitespaces, or an empty array.

You may also use `andFilterWhere()` and `orFilterWhere()` to append more filter conditions.


### `ORDER BY`

For ordering results `orderBy` and `addOrderBy` could be used:

```php
$query->orderBy([
    'id' => SORT_ASC,
    'name' => SORT_DESC,
]);
```

Here we are ordering by `id` ascending and then by `name` descending.

```

### `GROUP BY` and `HAVING`

In order to add `GROUP BY` to generated SQL you can use the following:

```php
$query->groupBy('id, status');
```

If you want to add another field after using `groupBy`:

```php
$query->addGroupBy(['created_at', 'updated_at']);
```

To add a `HAVING` condition the corresponding `having` method and its `andHaving` and `orHaving` can be used. Parameters
for these are similar to the ones for `where` methods group:

```php
$query->having(['status' => $status]);
```

### `LIMIT` and `OFFSET`

To limit result to 10 rows `limit` can be used:

```php
$query->limit(10);
```

To skip 100 fist rows use:

```php
$query->offset(100);
```

### `JOIN`

The `JOIN` clauses are generated in the Query Builder by using the applicable join method:

- `innerJoin()`
- `leftJoin()`
- `rightJoin()`

This left join selects data from two related tables in one query:

```php
$query->select(['user.name AS author', 'post.title as title'])
    ->from('user')
    ->leftJoin('post', 'post.user_id = user.id');
```

In the code, the `leftJoin()` method's first parameter
specifies the table to join to. The second parameter defines the join condition.

If your database application supports other join types, you can use those via the  generic `join` method:

```php
$query->join('FULL OUTER JOIN', 'post', 'post.user_id = user.id');
```

The first argument is the join type to perform. The second is the table to join to, and the third is the condition.

Like `FROM`, you may also join with sub-queries. To do so, specify the sub-query as an array
which must contain one element. The array value must be a `Query` object representing the sub-query,
while the array key is the alias for the sub-query. For example,

```php
$query->leftJoin(['u' => $subQuery], 'u.id=author_id');
```


### `UNION`

`UNION` in SQL adds results of one query to results of another query. Columns returned by both queries should match.
In Yii in order to build it you can first form two query objects and then use `union` method:

```php
$query = new Query();
$query->select("id, 'post' as type, name")->from('post')->limit(10);

$anotherQuery = new Query();
$anotherQuery->select('id, 'user' as type, name')->from('user')->limit(10);

$query->union($anotherQuery);
```


Batch Query
-----------

When working with large amount of data, methods such as [[yii\db\Query::all()]] are not suitable
because they require loading all data into the memory. To keep the memory requirement low, Yii
provides the so-called batch query support. A batch query makes uses of data cursor and fetches
data in batches.

Batch query can be used like the following:

```php
use yii\db\Query;

$query = (new Query())
    ->from('user')
    ->orderBy('id');

foreach ($query->batch() as $users) {
    // $users is an array of 100 or fewer rows from the user table
}

// or if you want to iterate the row one by one
foreach ($query->each() as $user) {
    // $user represents one row of data from the user table
}
```

The method [[yii\db\Query::batch()]] and [[yii\db\Query::each()]] return an [[yii\db\BatchQueryResult]] object
which implements the `Iterator` interface and thus can be used in the `foreach` construct.
During the first iteration, a SQL query is made to the database. Data are since then fetched in batches
in the iterations. By default, the batch size is 100, meaning 100 rows of data are being fetched in each batch.
You can change the batch size by passing the first parameter to the `batch()` or `each()` method.

Compared to the [[yii\db\Query::all()]], the batch query only loads 100 rows of data at a time into the memory.
If you process the data and then discard it right away, the batch query can help keep the memory usage under a limit.

If you specify the query result to be indexed by some column via [[yii\db\Query::indexBy()]], the batch query
will still keep the proper index. For example,

```php
use yii\db\Query;

$query = (new Query())
    ->from('user')
    ->indexBy('username');

foreach ($query->batch() as $users) {
    // $users is indexed by the "username" column
}

foreach ($query->each() as $username => $user) {
}
```
