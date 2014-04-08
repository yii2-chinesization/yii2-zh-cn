行为
=========

行为(behavior)（也称为 *mixin*）可以无须修改代码就可增强已有组件的功能。尤其是行为可以 “注入” 它的公开方法和属性到组件，并通过组件直接访问。行为也能响应触发的组件事件，由此拦截正常的代码执行。不同于[PHP's traits](http://www.php.net/traits)，行为可以运行时实时附加到类上。


使用行为
---------------

行为可以通过代码或应用配置附加到任何继承自[[yii\base\Component]]的类上。

### 通过 `behaviors` 方法附加行为

为附加行为到类可以执行组件的 `behaviors` 方法。如， Yii 内置了[[yii\behaviors\TimestampBehavior]]行为在保存[[yii\db\ActiveRecord|Active Record]]模型时自动升级时间戳字段：

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

以上代码中， `timestamp` 名用来指向组件使用的行为。如 `$user->timestamp` 取得已附加的时间戳行为实例。对应的数组是用来创建[[yii\behaviors\TimestampBehavior|TimestampBehavior]]对象的配置。

除了响应插入和更新活动记录， `TimestampBehavior` 还提供了 `touch()` 方法以分配当前时间戳到指定特性。如前所述，通过组件可直接访问该方法，如下所示：

```php
$user->touch('login_time');
```

如果不需要访问行为对象，或行为不需要定制，当指定行为时也可以使用以下简单格式：

```php
use yii\behaviors\TimestampBehavior;

class User extends ActiveRecord
{
    // ...

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            // 或以下格式，要访问行为对象的话
            // 'timestamp' => TimestampBehavior::className(),
        ];
    }
}
```

### 动态附加行为

另一个附加行为到组件的方法是 `attachBehavior` ，如下所示：

```php
$component = new MyComponent();
$component->attachBehavior();
```

### 从配置附加行为

当配置组件时使用配置数组也可以附加行为到组件。语法如下：

```php
return [
    // ...
    'components' => [
        'myComponent' => [
            // 'as 行为名'
            'as tree' => [
                'class' => 'Tree',
                'root' => 0,
            ],
        ],
    ],
];
```

以上配置 `as tree` 表示附加一个名为 `tree` 的行为，数组将传递到[[\Yii::createObject()]]创建行为对象。


创建你自己的行为
---------------------------

创建你自己的行为，需要定义一个继承自[[yii\base\Behavior]]的类：

```php
namespace app\components;

use yii\base\Behavior;

class MyBehavior extends Behavior
{
}
```

要像[[yii\behaviors\TimestampBehavior]]那样可定制，请添加公开的属性：

```php
namespace app\components;

use yii\base\Behavior;

class MyBehavior extends Behavior
{
    public $attr;
}
```

现在，当行为被使用，就可以设置特性到希望应用该行为的地方：

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

行为当特定事件发生时会正常地执行，以下实现了`events` 方法来分派事件处理器：

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
