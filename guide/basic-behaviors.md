行为
=========

一个行为（也认为是*mixin*）可用于增强现在的有组件功能，而无需修改该组件中的代码。特别是，一个行为可“注入”到组件的公共方法和属性，使得组件自身可以直接访问。在组件中一个行为也能相应事件触发，从而拦截正常的执行代码。 与 [PHP's traits](http://www.php.net/traits) 不同, 行为可在运行时附加到类。

使用行为
---------------

无论是写代码还是通过配置，一个行为都能附加到任何继承自 [[yii\base\Component]] 的类。

### 通过`behaviors`方法附加行为

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
