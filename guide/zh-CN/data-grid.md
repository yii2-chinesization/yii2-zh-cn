数据网格
=========

数据网格或网格视图是 Yii 最强大的小部件之一。如需快速建立系统的管理后台部分，数据网格特别有用。数据网格从[数据源](data-providers.md)获取数据并渲染每行，每行的列展现数据表的表单数据。

数据表的一行代表单个数据项的数据，一列通常表示数据项的一个特性（有些列会对应特性或静态文本的复杂表达式）。

网格视图支持数据项的排序和分页。排序和分页能以 AJAX 模式或标准页面请求两种方式实现。使用网格视图类（GridView）的好处之一是用户禁止 JavaScript 时，排序和分页能自动降级到标准页面请求且功能还能符合期望值。

使用 GridView 的最少代码示例如下：

```php
use yii\data\GridView;
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

网格列
------------

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

网格列可通过使用不同列的类来自定义：

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

#### 数据列

Data column is for displaying and sorting data. It is default column type so specifying class could be omitted when
using it.
数据列用于数据显示和排序，默认列类型

TBD

#### Action column

Action column displays action buttons such as update or delete for each row.

```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\ActionColumn',
            // you may configure additional properties here
        ],
```

Available properties you can configure are:

- `controller` is the ID of the controller that should handle the actions. If not set, it will use the currently active
  controller.
- `template` the template used for composing each cell in the action column. Tokens enclosed within curly brackets are
  treated as controller action IDs (also called *button names* in the context of action column). They will be replaced
  by the corresponding button rendering callbacks specified in [[yii\grid\ActionColumn::$buttons|buttons]]. For example, the token `{view}` will be
  replaced by the result of the callback `buttons['view']`. If a callback cannot be found, the token will be replaced
  with an empty string. Default is `{view} {update} {delete}`.
- `buttons` is an array of button rendering callbacks. The array keys are the button names (without curly brackets),
  and the values are the corresponding button rendering callbacks. The callbacks should use the following signature:

```php
function ($url, $model) {
    // return the button HTML code
}
```

In the code above `$url` is the URL that the column creates for the button, and `$model` is the model object being
rendered for the current row.

- `urlCreator` is a callback that creates a button URL using the specified model information. The signature of
  the callback should be the same as that of [[yii\grid\ActionColumn\createUrl()]]. If this property is not set,
  button URLs will be created using [[yii\grid\ActionColumn\createUrl()]].

#### Checkbox column

CheckboxColumn displays a column of checkboxes.
 
To add a CheckboxColumn to the [[yii\grid\GridView]], add it to the [[yii\grid\GridView::$columns|columns]] configuration as follows:
 
```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        // ...
        [
            'class' => 'yii\grid\CheckboxColumn',
            // you may configure additional properties here
        ],
    ],
```

Users may click on the checkboxes to select rows of the grid. The selected rows may be obtained by calling the following
JavaScript code:

```javascript
var keys = $('#grid').yiiGridView('getSelectedRows');
// keys is an array consisting of the keys associated with the selected rows
```

#### Serial column

Serial column renders row numbers starting with `1` and going forward.

Usage is as simple as the following:

```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'], // <-- here
```

Sorting data
------------

- https://github.com/yiisoft/yii2/issues/1576

Filtering data
--------------

For filtering data the GridView needs a [model](model.md) that takes the input from the filtering
form and adjusts the query of the dataprovider to respect the search criteria.
A common practice when using [active records](active-record.md) is to create a search Model class
that extends from the active record class. This class then defines the validation rules for the search
and provides a `search()` method that will return the data provider.

To add search capability for the `Post` model we can create `PostSearch` like in the following example:

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
        // only fields in rules() are searchable
        return [
            [['id'], 'integer'],
            [['title', 'creation_date'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Post::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // load the seach form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'title', $this->name])
              ->andFilterWhere(['like', 'creation_date', $this->creation_date]);

        return $dataProvider;
    }
}

```

You can use this function in the controller to get the dataProvider for the GridView:

```php
$searchModel = new PostSearch();
$dataProvider = $searchModel->search($_GET);

return $this->render('myview', [
	'dataProvider' => $dataProvider,
	'searchModel' => $searchModel,
]);
```

And in the view you then assign the `$dataProvider` and `$searchModel` to the GridView:

```php
echo GridView::widget([
    'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
]);
```


Working with model relations
----------------------------

When displaying active records in a GridView you might encounter the case where you display values of related
columns such as the post's author's name instead of just his `id`.
You do this by defining the attribute name in columns as `author.name` when the `Post` model
has a relation named `author` and the author model has an attribute `name`.
The GridView will then display the name of the author but sorting and filtering are not enabled by default.
You have to adjust the `PostSearch` model that has been introduced in the last section to add this functionallity.

To enable sorting on a related column you have to join the related table and add the sorting rule
to the Sort component of the data provider:

```php
$query = Post::find();
$dataProvider = new ActiveDataProvider([
    'query' => $query,
]);

// join with relation `author` that is a relation to the table `users`
// and set the table alias to be `author`
$query->joinWith(['author' => function($query) { $query->from(['author' => 'users']); }]);
// enable sorting for the related column
$dataProvider->sort->attributes['author.name'] = [
    'asc' => ['author.name' => SORT_ASC],
    'desc' => ['author.name' => SORT_DESC],
];

// ...
```

Filtering also needs the joinWith call as above. You also need to define the searchable column in attributes and rules like this:

```php
public function attributes()
{
    // add related fields to searchable attributes
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

In `search()` you then just add another filter condition with `$query->andFilterWhere(['LIKE', 'author.name', $this->getAttribute('author.name')]);`.

> Info: For more information on `joinWith` and the queries performed in the background, check the
> [active record docs on eager and lazy loading](active-record.md#lazy-and-eager-loading).
