行为
=========

行为是[[yii\base\Behavior]]或其子类的实例。行为，又称为[mixins](http://en.wikipedia.org/wiki/Mixin)，可以无须改变类继承关系即可增强一个已有的[[yii\base\Component|component]]组件类功能。当行为附加到组件后，它将“注入”它的方法和属性到组件，然后可以访问这些方法和属性，就像它们是组件类内部定义似的。此外，行为通过组件能响应被触发的[事件](basic-events.md)，从而自定义或调整组件正常执行的代码。


使用行为
---------------

要使用行为必须先把它附加到[[yii\base\Component|component]]类或其子类。下节将叙述怎样附加行为。

一旦行为附加到组件，就可以直接使用它。

通过附加了行为的组件可以如下方式访问行为的*公共*成员变量或行为的 getter 和 setter 方法定义的[属性](basic-properties.md)：

```php
// "prop1" 是定义在行为类的属性
echo $component->prop1;
$component->prop1 = $value;
```

类似地也可以调用行为的*公共*方法：

```php
// bar() 是定义在行为类的公共方法
$component->bar();
```

如你所见，尽管 `$component` 未定义 `prop1` 和 `bar()` ，它们用起来也像组件自己定义的一样。

如果两个行为都定义了一样的属性或方法，并且它们都附加到同一个组件，那么先附加上的行为在属性或方法被访问时就有优先权。

当行为附加到组件时可以取名关联行为。这种情况可以使用这个名称来访问行为对象，如下所示：

```php
$behavior = $component->getBehavior('myBehavior');
```

也能获取附加到这个组件的所有行为：

```php
$behaviors = $component->getBehaviors();
```


附加行为
----------

可以静态或动态地附加行为到组件[[yii\base\Component|component]]。前者在实践中更常见。

要静态附加行为，覆写行为要附加到的组件类的[[yii\base\Component::behaviors()|behaviors()]]方法即可。如：

```php
namespace app\models;

use yii\db\ActiveRecord;
use app\components\MyBehavior;

class User extends ActiveRecord
{
    public function behaviors()
    {
        return [
            // 匿名行为，只有行为类名
            MyBehavior::className(),

            // 命名行为，只有行为类名
            'myBehavior2' => MyBehavior::className(),

            // 匿名行为，配置数组
            [
                'class' => MyBehavior::className(),
                'prop1' => 'value1',
                'prop2' => 'value2',
            ],

            // 命名行为，配置数组
            'myBehavior4' => [
                'class' => MyBehavior::className(),
                'prop1' => 'value1',
                'prop2' => 'value2',
            ]
        ];
    }
}
```

[[yii\base\Component::behaviors()|behaviors()]]方法返回行为[配置](basic-configs.md)列表。


为了附加一个行为到一个类，你需要实现组件的 `behaviors` 方法。作为一个例子，Yii提供了 [[yii\behaviors\TimestampBehavior]]  行为，于用当一个  [[yii\db\ActiveRecord|Active Record]] 模型保存时自动更新时间戳：

```php
use yii\behaviors\TimestampBehavior;

class User extends ActiveRecord
{
	// ...

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
					ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
				],
			],
		];
	}
}
```

该组件可通过上面的`timestamp`引用行为。例如， `$user->timestamp`获得附加的时间戳行为实例。相应的数组是用来创建 [[yii\behaviors\TimestampBehavior|TimestampBehavior]] 对象的配置。

除了响应ActiveRecord的插入和更新事件，`TimestampBehavior` 还提供了一个方法 `touch()`，可分配指定当前时间戳的属性。正如之前所说，你可以直接使用组件访问此方法，如下所示：

```php
$user->touch('login_time');
```

如果你不需要访问行为对象，或行为不需要定制配置，也可以使用下面简化格式来指定行为。

```php
use yii\behaviors\TimestampBehavior;

class User extends ActiveRecord
{
	// ...

	public function behaviors()
	{
		return [
			TimestampBehavior::className(),
			// or the following if you want to access the behavior object
			// 'timestamp' => TimestampBehavior::className(),
		];
	}
}
```

### 动态附加行为

另一种附加行为到一个组件是调用`attachBehavior`方法，如下所示：

```php
$component = new MyComponent();
$component->attachBehavior();
```

### 从配置附加行为

当使用配置数组时可附加行为到组件。语法如下所示：

```php
return [
	// ...
	'components' => [
		'myComponent' => [
			// ...
			'as tree' => [
				'class' => 'Tree',
				'root' => 0,
			],
		],
	],
];
```

在上面的配置中 `as tree` 表示附加一个行为名为 `tree`，配置数组将被传递给 [[\Yii::createObject()]] 创建行为对象。

创建你自己的行为
---------------------------

要创建自己的行为，你必须定义一个继承自 [[yii\base\Behavior]] 的类。

```php
namespace app\components;

use yii\base\Behavior;

class MyBehavior extends Behavior
{
}
```

为了可定制，类似于 [[yii\behaviors\TimestampBehavior]]，需要添加公共属性：

```php
namespace app\components;

use yii\base\Behavior;

class MyBehavior extends Behavior
{
	public $attr;
}
```

现在，当使用这个行为时，你可以设置使用行为属性：

```php
namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord
{
	// ...

	public function behaviors()
	{
		return [
			'mybehavior' => [
				'class' => 'app\components\MyBehavior',
				'attr' => 'member_type'
			],
		];
	}
}
```

行为正常写入特定事件发生时采取动作。下面我们实现 `events` 方法指定事件处理：

```php
namespace app\components;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class MyBehavior extends Behavior
{
	public $attr;

	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
			ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
		];
	}

	public function beforeInsert() {
		$model = $this->owner;
		// Use $model->$attr
	}

	public function beforeUpdate() {
		$model = $this->owner;
		// Use $model->$attr
	}
}
```
