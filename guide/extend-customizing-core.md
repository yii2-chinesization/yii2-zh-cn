辅助(Helper)类
==============

Yii提供许多辅助类来帮助简化一些常见的编程任务，如字符串、数组操作及HTML的代码生成等等。这些辅助类存在于 `yii\helpers` 命名空间中，并且都是静态类（即它们只包含静态属性和方法，所以不应该被实例化）。


可以直接通过调用它的静态方法使用一个辅助类：

```php
use yii\helpers\ArrayHelper;

$c = ArrayHelper::merge($a, $b);
```

扩展辅助类
------------------------

为了使辅助类更容易扩展，Yii将它们分成两个类：一个基类（如 `BaseArrayHelper`）和一个实体类(concrete class)（如`ArrayHelper`）。要使用一个辅助类时，你应该只使用实体的版本，而不是使用基类。

如果你想自定义一个辅助类，请执行以下步骤（以`ArrayHelper`为例）：

1. 和Yii提供的实体类一样，给你的类起个类名，包括命名空间：`yii\helpers\ArrayHelper`
2. 从基类中扩展类：`class ArrayHelper extends \yii\helpers\BaseArrayHelper`
3. 在你的类中，根据需要覆盖任何方法或属性，也可以添加新的方法或属性
4. 在引导脚本中添加如下码，以让你的应用使用你的辅助类：

```php
Yii::$classMap['yii\helpers\ArrayHelper'] = 'path/to/ArrayHelper.php';
```

上面的步骤4指定了Yii的自动加载类去使用你定制的辅助类，而不是使用Yii原本配置的默认版本。

> 提示：您可以通过配置  `Yii::$classMap` 来指定自己定制的类取代Yii的任何核心类，辅助类只是一个方面而已。
