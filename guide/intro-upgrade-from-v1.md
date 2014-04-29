从 Yii 1.1 升级到 Yii 2.0
======================

在本章中，我们罗列了 Yii 2 相比 Yii 1.1 的主要变化。希望该列表能让你更容易地接受从 Yii 1.1 升级到 Yii 2.0 时代里所发生的改变，并基于你对 Yii 1 已有的了解来快速掌握 Yii 2。


命名空间
---------

Yii 2.0 最大的变化是命名空间的使用。几乎所有核心类都使用命名空间，如 `yii\web\Request`。Yii 1.1 时代中常见的 **C** 类名前缀则不再使用。命名空间遵循目录结构取名，如 `yii\web\Request` 代表的类文件是 Yii 框架文件夹内的 `web/Request.php`。这样由于 Yii 的类自动加载器的存在，我们就不需要显式引入任何类文件就能直接使用相应的核心类。


组件和对象
--------------------

Yii 2.0 把 Yii 1.1 的 `CComponent` 类分离为`对象`和`组件`两个类：[[yii\base\Object]] and [[yii\base\Component]]。
[[yii\base\Object|Object]] 对象类是轻量级的基本类，允许通过 getters 和 setters 来定义类属性。[[yii\base\Component|Component]] 组件类继承自 [[yii\base\Object|Object]] 对象类并支持事件特性和行为特性。

若自定义类不需要事件或行为特性，继承自对象类即可，通常用于那些代表基本数据结构的类。

对象和组件的更多细节请参看[Yii 的基本概念](basics.md)章节。


配置对象
--------------------

[[yii\base\Object|Object]] 对象类提供了一个统一地配置对象的方法。对象类的子类需要使用如下格式的构造器（如需要），以使对象可以被恰当地配置：

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

上面构造器的最后一个参数必须是一个配置数组，是用于构造器运行结束时初始化属性的键值对。
可以通过覆写[[yii\base\Object::init()|init()]]方法在配置生效后再运行初始化。
遵循该约定，可以如下使用配置数组创建和配置新对象：

```php
$object = Yii::createObject([
    'class' => 'MyClass',
    'property1' => 'abc',
    'property2' => 'cde',
], [$param1, $param2]);
```

更多配置信息请查阅[基础概念](basics.md).


事件
------

Yii 2.0 定义事件不再需要使用 `on` 开头的方法名称来定义事件，可以使用任何事件名。Yii 2 使用 `on` 方法来附加处理器到事件上：

```php
$component->on($eventName, $handler);
// 分离处理器，使用:
// $component->off($eventName, $handler);
```

附加处理器后，可以将参数关联到处理器上，这样处理器就可以访问这些事件参数了：
```php
$component->on($eventName, $handler, $params);
```

这样改变后，Yii 2 可以使用全局事件了。以下是应用实例的事件触发器和附加处理器的简单例子：

```php
Yii::$app->on($eventName, $handler);
....
// 以下代码将触发事件，并调用事件处理器。
Yii::$app->trigger($eventName);
```

如需要处理某个类的所有实例而不是单个对象，可以如下方式附加处理器：

```php
Event::on(ActiveRecord::className(), ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    Yii::trace(get_class($event->sender) . ' is inserted.');
});
```

以上代码定义了一个处理器，将被所有 AR 对象的 `EVENT_AFTER_INSERT` 事件触发调用。
更多细节参考 [Event handling section](events.md)。


路径别名
----------

Yii 2.0 将路径别名的使用扩展到文件/目录路径和 URL 路径。路径别名必须以 `@` 开头，以便区别于文件/目录路径和 URL 路径。如， `@yii`路径别名代表 Yii 框架安装目录。路径别名在 Yii 的核心代码的绝大多数地方都支持。如`FileCache::cachePath` 即可以用于路径别名也可以作为正常的目录路径。


路径别名和类命名空间关系密切。推荐为每个根命名空间定义一个路径别名，即可无须配置就能使用 Yii 的类加载器了。如， `@yii` 指向 Yii 安装目录，那么 Yii 就自动加载了类似`yii\web\Request` 的类。如需使用 Zend 框架第三方库，定义 `@Zend` 路径别名指向该框架安装目录，Yii 就能够自动加载这个库的任何类。

更多路径别名信息请参看 [Basic concepts section](basics.md)。


视图
----

Yii 2.0 提供了[[yii\web\View|View]]类来代表 MVC 模式中的视图部分。通过视图应用组件可以全局配置。用 `$this`可以在任何视图文件中访问到该"view"视图组件。比较 Yii 1.1,这个变化是最大的：
**视图文件中的`$this` 不再指向控制器或小部件对象了。**而是指向用于渲染该视图文件的视图对象。现在需要通过`$this->context` 来访问控制器或小部件对象。

For partial views, the [[yii\web\View|View]] class now includes a `render()` function. This creates another significant change in the usage of views compared to 1.1:
 **`$this->render(...)` does not output the processed content; you must echo it yourself.**
 
 ```php
 echo $this->render('_item', ['item' => $item]);
 ```

通过"view"视图组件可以访问视图对象，因此可以在代码的任何地方，而不是必须在控制器或小部件内渲染视图文件：

```php
$content = Yii::$app->view->renderFile($viewFile, $params);
// 也可以显式创建新的视图实例来渲染：
// $view = new View();
// $view->renderFile($viewFile, $params);
```

Yii 2.0 也不再存在 `CClientScript` 。[[yii\web\View|View]]类通过显著的改进已经取代了它的角色。更多细节请参看"assets" 部分。

尽管 Yii 2.0 仍然使用 PHP 作为主要的模板语言，但也提供了两个官方扩展来支持以下两个流行的模板引擎：Smarty 和 Twig。Prado 模板引擎不再支持。要使用这些模板引擎，需要使用 `tpl` 或 `twig` 作为视图文件的扩展名。也可以配置[[yii\web\View::$renderers|View::$renderers]]属性来使用其他模板引擎，更多细节请参看[使用模板引擎](template.md)部分。

视图类更多细节请参看[视图部分](view.md)。


模型
------

Yii 2.0 的模型关联了一个由[[yii\base\Model::formName()|formName()]]方法返回的表单名，主要用于 HTML 表单收集用户输入的模型数据。在 Yii 1.1 ，表单名通常硬编码为模型类名。

新方法[[yii\base\Model::load()|load()] 和 [[yii\base\Model::loadMultiple()|Model::loadMultiple()]]被用于简化从用户输入到模型的数据填充。如：

```php
$model = new Post();
if ($model->load($_POST)) {...}
// 等价于:
if (isset($_POST['Post'])) {
    $model->attributes = $_POST['Post'];
}

$model->save();

$postTags = [];
$tagsCount = count($_POST['PostTag']);
while ($tagsCount-- > 0) {
    $postTags[] = new PostTag(['post_id' => $model->id]);
}
Model::loadMultiple($postTags, $_POST);
```

Yii 2.0 提供了新方法[[yii\base\Model::scenarios()|scenarios()]] 来声明特性在不同场景所需的验证。子类可以覆写[[yii\base\Model::scenarios()|scenarios()]] 方法以返回场景清单（数组）和相应的特性，特性在[[yii\base\Model::validate()|validate()]]调用时将被验证。如：

```php
public function scenarios()
{
    return [
        'backend' => ['email', 'role'],
        'frontend' => ['email', '!name'],
    ];
}
```

该方法也决定了哪些特性是安全的，哪些不是。特别是在给定场景中，如果特性出现在[[yii\base\Model::scenarios()|scenarios()]]相应的特性清单上，且名字没有 `!`前缀，就认为是 *安全的* 。

因为以上改进，Yii 2.0 不再需要 "unsafe" 验证器。

如果你的模型只有一个场景（非常普遍），不需要覆写[[yii\base\Model::scenarios()|scenarios()]]，这时和 1.1 的运行是一样的。

更多 Yii 2.0 模型请参考本指南的 [模型](model.md)部分。


控制器
-----------

现在[[yii\base\Controller::render()|render()]] 和 [[yii\base\Controller::renderPartial()|renderPartial()]] 方法返回的是渲染结果而不是直接发送出去，必须 *显式* `echo` ： `echo $this->render(...);` 。

更多 Yii 2.0 控制器相关内容请参考本指南的 [控制器](controller.md)部分。


小部件
-------

Yii 2.0 使用小部件更直接了，可以使用[[yii\base\Widget|Widget]]类的[[yii\base\Widget::begin()|begin()]],[[yii\base\Widget::end()|end()]]和[[yii\base\Widget::widget()|widget()]]方法。如：

```php
// 注意必须  "echo" 结果才能显示
echo \yii\widgets\Menu::widget(['items' => $items]);

// 传递数组以初始化对象属性
$form = \yii\widgets\ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => ['inputOptions' => ['class' => 'input-xlarge']],
]);
... 表单输入在此 ...
\yii\widgets\ActiveForm::end();
```

原先在 Yii 1.1,必须通过 `CBaseController`的 `beginWidget()`,`endWidget()` 和 `widget()` 方法来输入小部件类名字符串，而 Yii 2.0 的上述方法对 IDE 更友好（IDE支持更好）。
更多内容请参见 [视图小部件](view.md#widgets) 部分。


主题
------

Yii 2.0 的主题运行机制完全不同于 Yii 1.1。现在主题基于路径映射表来 "翻译" 原始视图到主题视图。如，一个主题的路径映射表是`['/web/views' => '/web/themes/basic']`，则视图文件`/web/views/site/index.php` 的主题版本就是`/web/themes/basic/site/index.php`。

因此，主题现在可用于任何视图文件，即便该视图是通过外部的控制器或小部件对象渲染的。

不再需要 `CThemeManager` ，相反 `theme` 成为了 "view" 组件可配置的属性。

更多主题相关请参考[主题化部分](theming.md)。


控制台应用
--------------------

控制台应用现在可像 Web 应用一样由控制器组成，事实上两者的控制器均继承自同一个父类。

控制台控制器像 1.1 中的 `CConsoleCommand` 一样，由一个或多个部分组成。通过`yii <route>` 命令来执行控制台命令，`<route>` 表示控制器路径 (如 `sitemap/index`)。匿名命令行参数作为参数传到相应的控制器动作方法，而命名命令行参数则视为可选项，在 `options($id)`声明。

Yii 2.0 支持从注释自动生成命令帮助信息。

更多有关控制台应用的内容请参看[控制台](console.md)。


国际化
----

Yii 2.0 移除了原来的日期格式和数字格式方法，而以PECL intl PHP 模块取代。

消息翻译仍被支持，但现在由"i18n" 应用组件管理。该组件管理一系列消息源，也允许使用基于消息类别的不同消息源。更多资讯请参见[I18N](i18n.md)类文档。


动作过滤器
--------------

动作过滤器现在通过行为来实现。定义新的过滤器需继承[[yii\base\ActionFilter]]类。使用过滤器需要把过滤器类当做行为附加到控制器上。如，使用过滤器[[yii\filters\AccessControl]]，需编写以下代码：

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

更多动作过滤器的相关内容请参考 [控制器动作过滤器部分](controller.md#action-filters)。


资源
------

Yii 2.0 引入了一个新的概念： *资源包* 。类似于 Yii 1.1 中的脚本包（ `CClientScript`管理的）,但支持更好。

一个资源包是同目录资源文件集合（如 JS 文件、CSS 文件、图片文件等）。每个资源包代表一个继承自[[yii\web\AssetBundle]]的类。通过[[yii\web\AssetBundle::register()]]注册一个资源包，就能够使该资源包通过 Web 访问，而当前页会自动引用资源包内的 JS 和 CSS 文件。

更多内容请参见[资源管理器](assets.md)。


静态助手类
--------------

Yii 2.0 提供了许多通用的静态助手类，如
[[yii\helpers\Html|Html]],
[[yii\helpers\ArrayHelper|ArrayHelper]],
[[yii\helpers\StringHelper|StringHelper]].
[[yii\helpers\FileHelper|FileHelper]],
[[yii\helpers\Json|Json]],
[[yii\helpers\Security|Security]],
这些类设计得易于扩展。注意由于引用固定类名，静态类通常难以扩展。但 Yii 2.0 提供了类映射表(通过 [[Yii::$classMap]])来克服了这一困难。



活动表单
----------

Yii 2.0 提供了 *字符段* （ *field*）概念来使用[[yii\widgets\ActiveForm]]建立表单。一个字符段是一个容器，由标签、输入区、错误信息和提示文字组成，代表[[yii\widgets\ActiveField|ActiveField]]对象。

使用字符段可以更清晰的建立表单：

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

Yii 1.1中的查询生成分散在多个类中，包括`CDbCommand`,`CDbCriteria`, 和 `CDbCommandBuilder`。Yii 2.0 使用[[yii\db\Query|Query]]代表一个数据库查询，使用[[yii\db\QueryBuilder|QueryBuilder]]从 Query 对象生成 SQL 语句。举例：

```php
$query = new \yii\db\Query();
$query->select('id, name')
      ->from('user')
      ->limit(10);

$command = $query->createCommand();
$sql = $command->sql;
$rows = $command->queryAll();
```

这些查询生成方法最好和[[yii\db\ActiveRecord|ActiveRecord]]一起使用，下节将介绍。


活动记录
------------

Yii 2.0 的[[yii\db\ActiveRecord|ActiveRecord]] 有了显著的变化。最重要的一点是活动记录的关联查询。Yii 1.1 中，必须在 `relations()` 方法中声明关联关系，而 Yii 2.0,通过 getter 方法返回的[[yii\db\ActiveQuery|ActiveQuery]]对象已经完成了这一步。如，以下方法声明了一个 "orders" 关系：

```php
class Customer extends \yii\db\ActiveRecord
{
    public function getOrders()
    {
        return $this->hasMany('Order', ['customer_id' => 'id']);
    }
}
```

使用 `$customer->orders` 访问客户的订单。也可以使用`$customer->getOrders()->andWhere('status=1')->all()` 实现加查询条件的实时关联查询。

Yii 2.0 预先加载关联记录的方式和 1.1 很不同。尤其是 JOIN 查询，1.1的 JOIN 查询用于一起取出主表和关联表记录。而 2.0不使用 JOIN ，而是执行两条 SQL 语句：第一条语句取出主表记录，第二条根据主表记录的主键过滤后取出关联表相关记录。

Yii 2.0 执行查询不再使用 `model()` 方法，用的是[[yii\db\ActiveRecord::find()|find()]]方法：

```php
// 检索所有状态为活动的客户并以 ID 排序：
$customers = Customer::find()
    ->where(['status' => $active])
    ->orderBy('id')
    ->all();
// 返回主键为 1 的客户
$customer = Customer::findOne(1);
```


 [[yii\db\ActiveRecord::find()|find()]] 方法返回了 [[yii\db\Query]]子类[[yii\db\ActiveQuery|ActiveQuery]]的实例。因此可以使用[[yii\db\Query]]里的所有查询方法。

如需返回结果是数组而不是 AR 对象，调用[[yii\db\ActiveQuery::asArray()|ActiveQuery::asArray()]]即可。当返回大量记录时返回数组更高效、更有用。

```php
$customers = Customer::find()->asArray()->all();
```

Yii 2.0的活动记录默认只保存有变化的特性。1.1中当调用 `save()`方法时，无论数据是否改变，所有特性都会被存入数据库，除非显式提供待保存的特性列表。
Scopes are now defined in a custom [[yii\db\ActiveQuery|ActiveQuery]] class instead of model directly.
范围现在定义在[[yii\db\ActiveQuery|ActiveQuery]]类而不是直接定义在模型。
更多细节请参考 [活动记录](active-record.md)。


自引用的表名和列名
------------------------------------

Yii 2.0 支持数据表名和列名的自动替换。以两个大括号包围的名字 （如`{{tablename}}` ）视为表名，以两个中括号包围的名字（如`[[fieldname]]` ）视为列名。它们将根据使用的数据库引擎来替换：

```php
$command = $connection->createCommand('SELECT [[id]] FROM {{posts}}');
echo $command->sql;  // MySQL: SELECT `id` FROM `posts`
```

这个特点在开发支持不同数据库的应用时特别有用。


用户和身份接口
--------------------------

The `CWebUser` class in 1.1 is now replaced by [[yii\web\User]], and there is no more
`CUserIdentity` class. Instead, you should implement the [[yii\web\IdentityInterface]] which
is much more straightforward to implement. The advanced application template provides such an example.
1.1中的`CWebUser` 类现在被[[yii\web\User]]取代，也不再有`CUserIdentity` 类。相反，使用更直观的[[yii\web\IdentityInterface]] 实现。高级应用样板有使用示例。


URL 管理
--------------

URL 管理和 1.1的相似，主要的改进是现在支持可选参数。如，有以下声明了的规则，URL 管理将会同时匹配 `post/popular` 和 `post/1/popular` 两个。1.1需要使用两个规则来达到这个目标。

```php
[
    'pattern' => 'post/<page:\d+>/<tag>',
    'route' => 'post/index',
    'defaults' => ['page' => 1],
]
```

更多细节请参考 [Url 管理器](url.md).


响应
--------

TBD

扩展
----------

Yii 1.1 extensions are not compatible with 2.0 so you have to port or rewrite these. In order to get more info about
extensions in 2.0 [referer to corresponding guide section](extensions.md).
Yii 1.1 的扩展不适用于 2.0，需要矫正或重写。Yii 2.0扩展的更多信息请参考[扩展编写参考](extensions.md).

用Composer集成
-------------------------

Yii完全支持 Composer 这个著名的 PHP 包管理器，Composer 解决依赖关系，通过简单控制台命令升级代码保持最新，管理第三方库的自动加载而无须理会第三方库使用哪种自动加载方式。

更多细节请参考本指南的[composer包管理器](composer.md) 和 [安装](installation.md) 部分。

混合使用Yii 1.1 和 2.x
------------------------------

该主题请参考[Yii 和第三方系统的集合运用](using-3rd-party-libraries.md)