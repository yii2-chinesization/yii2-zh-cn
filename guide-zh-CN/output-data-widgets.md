数据小部件
============
列表视图 （ListView）
--------



细节视图（DetailView）
----------

DetailView displays the detail of a single data [[yii\widgets\DetailView::$model|model]].
 
It is best used for displaying a model in a regular format (e.g. each model attribute is displayed as a row in a table).
The model can be either an instance of [[\yii\base\Model]] or an associative array.
 
DetailView uses the [[yii\widgets\DetailView::$attributes]] property to determines which model attributes should be displayed and how they
should be formatted.
 
A typical usage of DetailView is as follows:
 
```php
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'title',             // title attribute (in plain text)
        'description:html',  // description attribute in HTML
        [                    // the owner name of the model
            'label' => 'Owner',
            'value' => $model->owner->name,
        ],
    ],
]);
```

表格视图（GridView）
--------

数据表格或表格视图是 Yii 最强大的小部件之一。如需快速建立系统的管理后台部分，表格视图特别有用。表格视图从[数据源](data-providers.md)获取数据并渲染每行，每行的列展现数据表的表单数据。

数据表的一行代表单个数据项的数据，一列通常表示数据项的一个特性（有些列会对应特性或静态文本的复杂表达式）。

表格视图支持数据项的排序和分页。排序和分页能以 AJAX 模式或标准页面请求两种方式实现。使用表格视图类（GridView）的好处之一是用户禁止 JavaScript 时，排序和分页能自动降级到标准页面请求且功能还能符合期望值。

使用 GridView 的最少代码示例如下：

```php
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

$dataProvider = new ActiveDataProvider([
    'query' => Post::find(),
    'pagination' => [
        'pageSize' => 20,
    ],
]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
]);
```

以上代码首先建立一个数据供应器，然后使用 GridView 展现从数据供应器取出的每行数据的每个特性。被显示的表配备了排序和分页的功能。

### 网格列

Yii 网格由许多列组成。根据列类型和设置就能够不同地显示数据。

GridView 的列配置可定义如下：

```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        // 通过 $dataProvider 包括的数据定义了一个简单列
        // 模型列1 的数据将被使用
        'id',
        'username',
        // 更多复杂列
        [
            'class' => 'yii\grid\DataColumn', // 默认可省略
            'value' => function ($data) {
                return $data->name;
            },
        ],
    ],
]);
```

注意如果配置的列部分没有定义，Yii 将尝试显示所有可能的数据供应器的模型列。

### 列的类

网格列可通过使用不同列类（column class）来自定义：

```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn', // <-- 这里
            // 你可以定义其他属性在这里
        ],
```

另外除了使用我们将在下面回顾的 Yii 提供的列类（column classes），你可以创建自己的列类。

每个列类都继承自[[\yii\grid\Column]]，因此在配置网格列时可以设置一些共同选项。

- `header` 可以设置头信息
- `footer` 可以设置页脚信息
- `visible` 要显示的列
- `content` 可以传递有效 PHP 回调函数以返回数据行的数据，格式如下：

```php
function ($model, $key, $index, $grid) {
    return 'a string';
}
```

可以传递数组来指定不同容器的 HTML 选项：

- `headerOptions`
- `contentOptions`
- `footerOptions`
- `filterOptions`

#### 数据列（类）

数据列用于数据显示和排序，这是默认列类型，使用它的话可以省略类的指定。

TBD

#### 动作列（类）

动作列显示动作按钮如每行的更新或删除按钮。

```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\ActionColumn',
            // 可在此配置其他属性
        ],
```

可配置属性：

- `controller` 是处理动作的控制器 ID ，如果未设置，将使用当前活动控制器。
- `template` 用来组成动作列元素的模板，大括号内的内容将视作控制器的动作 ID （也称为动作列的 *按钮名*）。它们将被指定在[[yii\grid\ActionColumn::$buttons|buttons]]内相应的按钮渲染回调函数取代。如， `{view}` 将被回调函数 `buttons['view']` 的结果取代。如果未找到回调函数，将被空字符串取代。默认 `{view} {update} {delete}` 。
- `buttons` 是按钮渲染回调函数的数组，数组键是按钮名（没有大括号），而数组值是相应的按钮渲染回调函数。回调函数使用以下格式：

```php
function ($url, $model) {
    // 返回按钮 HTML 代码
}
```

以上代码中的 `$url` 是为创建按钮的列类的 URL ， `$model` 是被渲染的当前行的模型对象。

- `urlCreator` 是使用指定模型信息建立按钮 URL 的回调函数。回调签名应该和[[yii\grid\ActionColumn::createUrl()]]相同。如果该属性未设置，按钮 URL 将使用[[yii\grid\ActionColumn::createUrl()]]创建。

#### 复选框列（类）

复选框列显示复选框的一列。

要添加复选框列到[[yii\grid\GridView]]，如下添加它到[[yii\grid\GridView::$columns|columns]]配置：

```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        // ...
        [
            'class' => 'yii\grid\CheckboxColumn',
            // 在此配置其他属性
        ],
    ],
```

用户可以点击复选框来选择网格的行。被选中的行可调用以下 JavaScript 代码获取：

```javascript
var keys = $('#grid').yiiGridView('getSelectedRows');
// keys 是键名关联到选中行的数组
```

#### 有序列（类）

有序列渲染行的序号以 `1` 开始逐个排序。

用法如下，很简单：

```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'], // <-- 这里
```

数据排序
------------

- https://github.com/yiisoft/yii2/issues/1576

数据筛选
--------------

要筛选数据，表格视图需要一个[模型](model.md)从过滤的表单取得输入数据，并调整 dataprovider 的查询语句到期望的搜索条件。使用[active records](active-record.md)的惯例是建立一个搜索模型类继承活动记录类。然后用这个类定义搜索的验证规则和提供 `search()` 方法来返回 data provider 。

要给 `Post` 模型添加搜索能力，可以创建 `PostSearch` ，如下所示：

```php
<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PostSearch extends Post
{
    public function rules()
    {
        // 只有在 rules() 的字段才能被搜索
        return [
            [['id'], 'integer'],
            [['title', 'creation_date'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass 父类实现的scenarios()
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Post::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // 加载搜索表单数据并验证
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // 通过添加过滤器来调整查询语句
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'title', $this->name])
              ->andFilterWhere(['like', 'creation_date', $this->creation_date]);

        return $dataProvider;
    }
}

```

你可以在控制器使用这个方法来为表格视图获取 dataProvider ：

```php
$searchModel = new PostSearch();
$dataProvider = $searchModel->search($_GET);

return $this->render('myview', [
	'dataProvider' => $dataProvider,
	'searchModel' => $searchModel,
]);
```

然后在视图将 `$dataProvider` 和 `$searchModel` 赋值给表格视图：

```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
]);
```


和关联模型一起使用
----------------------------

在表格视图显示活动记录将会遇到何处显示关联列值的情况，如显示 post 的作者名而不是 `id` 。当 `Post` 模型有名为 `author` 的关联关系且 author 模型有 `name` 特性时，可以通过定义列中的特性名如 `author.name` 完成。表格视图将显示作者名但默认排序和筛选未启用。你可以调整将在本章最后一节介绍的 `PostSearch` 模型来添加该功能。

要在关联列排序，必须连接关联表并添加排序规则到 data provider 的 Sort 组件：

```php
$query = Post::find();
$dataProvider = new ActiveDataProvider([
    'query' => $query,
]);

// 连接关联 `author` 表作为 `users` 表的一个关系
// 并设置表别名为 `author`
$query->joinWith(['author' => function($query) { $query->from(['author' => 'users']); }]);
// 使关联列的排序生效
$dataProvider->sort->attributes['author.name'] = [
    'asc' => ['author.name' => SORT_ASC],
    'desc' => ['author.name' => SORT_DESC],
];

// ...
```

筛选也需要像上面那样调用 joinWith 。也可以定义可搜索特性和规则的列如下：

```php
public function attributes()
{
    // 添加关联字段到可搜索特性
    return array_merge(parent::attributes(), ['author.name']);
}

public function rules()
{
    return [
        [['id'], 'integer'],
        [['title', 'creation_date', 'author.name'], 'safe'],
    ];
}
```

然后在 `search()` 方法只须以 `$query->andFilterWhere(['LIKE', 'author.name', $this->getAttribute('author.name')]);`添加另一个过滤条件。

> 须知：更多有关 `joinWith` 和后台执行查询的相关信息请参考
> [活动记录的预先加载和延迟加载](active-record.md#lazy-and-eager-loading).
