从 Yii 1.1 升级
======================

 Yii 2.0和1.1区别非常大，因为2.0完全重写了。因此，从1.1版本升级不是琐碎的小版本之间的升级。本章将总结这两个版本的主要差异。

请注意 Yii 2.0引入的很多新功能未涵盖在本章总结。强烈推荐你通篇阅读本权威指南以掌握那些功能。有可能你以前须自行开发的功能现在已经成为核心代码的一部分了。


命名空间
---------

Yii 2.0 最明显的变化是命名空间的使用。几乎所有核心类都有命名空间，如 `yii\web\Request` 。类名已不再需要"C"前缀。命名空间按照目录结构取名，如 `yii\web\Request` 对应的类文件是 Yii 框架目录的 `web/Request.php`。由于 Yii 的类加载器，不需要显式引入任何类文件就能直接使用任何核心类。


组件和对象
--------------------

Yii 2.0 将 Yii 1.1 的 `CComponent` 类分离为`对象`和`组件`两个类：[[yii\base\Object]] and [[yii\base\Component]]。
[[yii\base\Object|Object]] 对象类是轻量级的基类，可用 getters 和 setters 来定义[对象属性]。[[yii\base\Component|Component]] 组件类继承自 [[yii\base\Object|Object]] 对象类并支持[事件](basic-events.md)和[行为](basic-behaviors.md)。

若自定义类不需要事件或行为功能，只要继承自对象类即可，通常是代表基本数据结构的类使用。


对象配置
--------------------

[[yii\base\Object|Object]] 对象类提供了一个统一配置对象的方法。对象类的子类需要如下声明构造器（如需要），以使子类可被恰当配置：

```php
class MyClass extends \yii\base\Object
{
    public function __construct($param1, $param2, $config = [])
    {
        // ... 配置生效前的初始化

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();

        // ... 配置生效后的初始化
    }
}
```

以上代码中，构造函数的最后一个参数必须是键值对配置数组，用于构造函数运行结束时初始化属性。可以覆写[[yii\base\Object::init()|init()]]方法来完成配置生效后的初始化工作。

遵循该约定，就能如下使用配置数组来创建和配置新对象：

```php
$object = Yii::createObject([
    'class' => 'MyClass',
    'property1' => 'abc',
    'property2' => 'cde',
], [$param1, $param2]);
```

更多配置信息请查阅[对象配置](basic-configs.md)章节。


事件
------

Yii 2.0 定义事件的方法不再需要 `on` 开头，现在事件名不受任何限制。事件触发通过调用[[yii\base\Component::trigger()|trigger()]]方法实现：

```php
$event = new \yii\base\Event;
$component->trigger($eventName, $event);
```

附加处理器到事件使用[[yii\base\Component::on()|on()]]方法：

```php
$component->on($eventName, $handler);
// 分离处理器，使用:
// $component->off($eventName, $handler);
```

事件功能更加强悍了，更多细节请参考[事件](basic-events.md) 章节。


路径别名
----------

Yii 2.0 将路径别名的使用扩展到文件/目录路径和 URL。路径别名必须以 `@` 开头，以便区别于普通的文件/目录路径和 URL 。如，路径别名 `@yii`指向 Yii 框架的安装目录。 Yii 核心代码的绝大多数地方都支持路径别名，如[[yii\caching\FileCache::cachePath]]的赋值即可用路径别名又可用标准的目录路径。


路径别名和类的命名空间也密切相关。推荐为每个根命名空间定义一个路径别名，以便无须额外配置就能使用 Yii 的类自动加载器。例如，`@yii` 指向 Yii 安装目录，类似`yii\web\Request` 的类就能被 Yii 自动加载。如果使用 Zend 框架等第三方库，只要定义路径别名 `@Zend` 指向该框架的安装目录，Yii 就能自动加载这个库的任何类。

更多路径别名信息请参阅[路径别名](basic-aliases.md)章节。


视图
----

视图最显著的改动是视图内的 `$this` 不再指向当前控制器或小部件，而是指向*视图*对象，这是 2.0 引进的新概念。*视图*即[[yii\web\View]]类，表示 MVC 模式的视图部分。现在要在视图中访问控制器或小部件，需要使用 `$this->context` 。

要渲染任一视图内的局部视图，现在使用 `$this->render()` 方法，且必须显性**echo**它，因为 `render()` 方法返回的是渲染结果而不是直接显示内容。如：

```php
echo $this->render('_item', ['item' => $item]);
```

除了使用 PHP 作为主要的模板语言，Yii 2.0也装备了两种流行模板引擎的官方支持：Smarty 和 Twig 。Prado 模板引擎不再支持。要使用这些模板引擎，必须配置 `view` 应用组件，设置[[yii\base\View::$renderers|View::$renderers]]属性。详情请参阅[模板引擎](tutorial-template-engines.md)章节。


模型
------

Yii 2.0使用[[yii\base\Model]]作为模型基类，类似于1.1的 `CModel` 。`CFormModel` 弃用了，现在通过继承[[yii\base\Model]]来创建表单模型类。

Yii 2.0引进了名为[[yii\base\Model::scenarios()|scenarios()]]的新方法来声明支持的场景、属性赋值在哪个场景下必须验证及是否视为安全赋值。如：

```php
public function scenarios()
{
    return [
        'backend' => ['email', 'role'],
        'frontend' => ['email', '!name'],
    ];
}
```

以上代码声明了两个场景：`backend` 和 `frontend` 。对于 `backend` 场景，`email` 和 `role` 属性值都必须是安全的且能批量赋值；对于 `frontend` 场景，`email` 能批量赋值而 `role` 不能，且 `email` 和 `role` 都必须验证。

[[yii\base\Model::rules()|rules()]]方法仍用于声明验证规则。注意由于引进了[[yii\base\Model::scenarios()|scenarios()]]，现在已经没有 `unsafe` 验证器。

大多数情况下，如果[[yii\base\Model::rules()|rules()]]方法完整指定场景就不必覆写[[yii\base\Model::scenarios()|scenarios()]]，也不必声明 `unsafe` 属性值。

更多模型相关细节请参考[模型](basic-models.md)章节。


控制器
-----------

Yii 2.0使用[[yii\web\Controller]]作为控制器基类，类似于1.1的 `CWebController` 。而[[yii\base\Action]]是动作类的基类。

当在控制器动作写代码时，最明显的变化是返回要渲染的内容而不是输出（echo）它。如：

```php
public function actionView($id)
{
    $model = \app\models\Post::findOne($id);
    if ($model) {
        return $this->render('view', ['model' => $model]);
    } else {
        throw new \yii\web\NotFoundHttpException;
    }
}
```

更多控制器相关内容请参考[控制器](structure-controllers.md)章节。


小部件
-------

Yii 2.0 使用[[yii\base\Widget]]作为小部件基类，类似于1.1的 `CWidget` 。

为了得到更好的 IDE 支持，Yii 2.0引进了使用小部件的新语法，就是静态方法[[yii\base\Widget::begin()|begin()]], [[yii\base\Widget::end()|end()]] 和 [[yii\base\Widget::widget()|widget()]]，用法如下：

```php
use yii\widgets\Menu;
use yii\widgets\ActiveForm;

// 注意必须** "echo" **结果以显示内容
echo Menu::widget(['items' => $items]);

// 传递一个数组初始化对象的属性
$form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => ['inputOptions' => ['class' => 'input-xlarge']],
]);
... 表单输入域在这里 ...
ActiveForm::end();
```

更多细节请参阅 [小部件](structure-widgets.md)章节。


主题
------

Yii 2.0 主题的工作原理完全不同于 Yii 1.1。现在主题基于路径映射机制来映射源视图文件路径到主题化视图文件路径。如，一个主题的路径图是`['/web/views' => '/web/themes/basic']`，则视图文件`/web/views/site/index.php` 的主题化版本就是`/web/themes/basic/site/index.php`。因此，现在主题可应用于任何视图文件，即使是在当前控制器或小部件的外部渲染的视图。

因此也不再使用 `CThemeManager` ，而是用 `theme` 作为 "view" 应用组件的一个可配置属性。

更多细节请参考[主题](tutorial-theming.md)章节。


控制台应用
--------------------

控制台应用现在可像 Web 应用一样由控制器组成，控制台的控制器继承自[[yii\console\Controller]]，类似于1.1的 `CConsoleCommand` 。

运行控制台命令使用`yii <route>` ，其中`<route>` 代表控制器路由 (如 `sitemap/index`)。其他匿名参数传递到对应的控制器动作方法，而有名参数根据[[yii\console\Controller::options()]]的声明来解析。

Yii 2.0 支持从注释自动生成命令帮助信息。

更多细节请参阅[控制台命令](tutorial-console.md)章节。


国际化
----

Yii 2.0 移除了原来的日期格式器和数字格式器，而以PECL intl PHP 模块取代。

消息翻译现在由"i18n" 应用组件执行。该组件管理一系列消息源，允许使用基于消息类别的不同消息源。

更多细节请参阅[国际化](tutorial-i18n.md)章节。


动作过滤器
--------------

动作过滤器现在通过行为（behavior）来实现。定义新的过滤器需继承[[yii\base\ActionFilter]]类。使用过滤器需要附加过滤器类为控制器的行为。如，使用过滤器[[yii\filters\AccessControl]]，要在控制器编写以下代码：

```php
public function behaviors()
{
    return [
        'access' => [
            'class' => 'yii\filters\AccessControl',
            'rules' => [
                ['allow' => true, 'actions' => ['admin'], 'roles' => ['@']],
            ],
        ],
    ];
}
```

更多细节请参考[过滤](runtime-filtering.md)章节。


资源
------

Yii 2.0 引入了一个新的概念，称为 *资源包* 。类似于1.1的脚本包概念。

一个资源包是一个目录下的资源文件集合（如 JavaScript 文件、CSS 文件、图片文件等）。每一个资源包被表示为一个类，该类继承自[[yii\web\AssetBundle]]。用[[yii\web\AssetBundle::register()]]方法注册一个资源包后，就使它的资源可被 Web 访问，注册了资源包的页面会自动包含和引用资源包内的 JS 和 CSS 文件。

更多细节请参阅[资源管理](output-assets.md) 章节。


助手类
--------------

Yii 2.0 引进了许多普遍用到的静态助手类，如

* [[yii\helpers\Html]]
* [[yii\helpers\ArrayHelper]]
* [[yii\helpers\StringHelper]]
* [[yii\helpers\FileHelper]]
* [[yii\helpers\Json]]
* [[yii\helpers\Security]]


表单
----------

Yii 2.0 引进了 *域（field）*概念使用[[yii\widgets\ActiveForm]]来建立表单。一个域是一个由标签、输入框、错误消息和提示文字组成的容器，被表示为[[yii\widgets\ActiveField|ActiveField]]对象。

使用域建立的表单比以前更干净：

```php
<?php $form = yii\widgets\ActiveForm::begin(); ?>
    <?= $form->field($model, 'username') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Login') ?>
    </div>
<?php yii\widgets\ActiveForm::end(); ?>
```


查询生成器
-------------

Yii 1.1中，查询语句构建分散在多个类中，包括`CDbCommand`,`CDbCriteria`, 和 `CDbCommandBuilder`。Yii 2.0 用[[yii\db\Query|Query]]对象表示一个数据库查询，这个对象可以在幕后通过[[yii\db\QueryBuilder|QueryBuilder]]的帮助生成 SQL 语句。例如：

```php
$query = new \yii\db\Query();
$query->select('id, name')
      ->from('user')
      ->limit(10);

$command = $query->createCommand();
$sql = $command->sql;
$rows = $command->queryAll();
```

这些查询语句生成方法最好和[活动记录](db-active-record.md)一起使用。

更多细节请参阅[章节](db-query-builder.md)。


活动记录
------------

Yii 2.0 给[活动记录](db-active-record.md)引入了很多变化。最突出的两点是：查询语句的生成和关联查询的处理。

1.1的 `CDbCriteria` 类替换为[[yii\db\ActiveQuery]]，它继承自[[yii\db\Query]]，因此也继承了所有查询语句生成方法。调用[[yii\db\ActiveRecord::find()]]方法来开始生成查询，如：

```php
// 检索所有 *活动的* 客户和订单，以 ID 排序：
$customers = Customer::find()
    ->where(['status' => $active])
    ->orderBy('id')
    ->all();
```

声明关联关系只需简单定义一个 getter 方法来返回[[yii\db\ActiveQuery|ActiveQuery]]对象。getter 方法定义的属性名(即 getOrders() 中的 orders )表示关联关系名。如，以下代码声明了一个 `orders` 关系（1.1中必须在固定地方 `relations()`)声明关系）：

```php
class Customer extends \yii\db\ActiveRecord
{
    public function getOrders()
    {
        return $this->hasMany('Order', ['customer_id' => 'id']);
    }
}
```

使用 `$customer->orders` 来访问指定客户的订单。也可以使用以下代码来执行自定义查询条件的动态关联查询：

```php
$orders = $customer->getOrders()->andWhere('status=1')->all();
```

Yii 2.0 的预先加载关系和 1.1 是不同的。尤其是 JOIN 查询，1.1的 JOIN 查询创建来同时取出主表和关联表的记录，而 2.0也执行两条 SQL 语句，但不使用 JOIN ，而是：第一条语句取回主表记录，然后第二条语句用主表记录的主键过滤后再取出关联记录。

当构建查询来返回大量记录时，也可使用[[yii\db\ActiveQuery::asArray()|asArray()]]方法链来取代返回[[yii\db\ActiveRecord|ActiveRecord]]对象，以致返回查询结果是数组，这能明显降低大量记录所必须的 CPU 时间和内存。如：

```php
$customers = Customer::find()->asArray()->all();
```

活动记录有更多其他的改动和增强，请参考[活动记录](db-active-record.md)章节获取更多细节。



用户和身份接口
-----------------

1.1的`CWebUser` 类现在被[[yii\web\User]]取代，也不再 `CUserIdentity` 类。相反，使用更直观的[[yii\web\IdentityInterface]] 实现。高级 App 提供了使用示例。


URL 管理
--------------

URL 管理和1.1相似，主要的改进是现在支持可选参数了。如，有以下声明好的规则，URL 管理将会同时匹配 `post/popular` 和 `post/1/popular` 。而1.1必须使用两个规则来达到这个目标。

```php
[
    'pattern' => 'post/<page:\d+>/<tag>',
    'route' => 'post/index',
    'defaults' => ['page' => 1],
]
```

更多细节请参考 [Url 管理器](url.md). 【原文未改】？[URL 解析和生成](runtime-url-handling.md)


Yii 1.1和2.x 共同使用
------------------------------

如有遗留 Yii 1.1代码并想要把它和 Yii 2.0共同使用，请参考[Yii 1.1和2.0 共同使用](extend-using-v1-v2.md)章节。