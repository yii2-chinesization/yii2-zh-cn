视图
====

视图组件是 MVC 的重要部分。视图作为应用界面，履行其向终端用户表现数据的职责，如显示表单等。

基础
------

Yii 默认使用 PHP 作为视图模板来生成内容和元素。web 应用视图通常包括一些 HTML 和 PHP `echo`, `foreach`, `if` 等基础结构的联合体。视图中使用复杂的 PHP 代码被认为是不良实践。当复杂逻辑和功能是必须的，这些代码应移动到控制器或小部件。

视图通常被控制器动作用[[yii\base\Controller::render()|render()]]方法调用：

```php
public function actionIndex()
{
    return $this->render('index', ['username' => 'samdark']);
}
```

[[yii\base\Controller::render()|render()]]方法的第一个参数是拟显示的视图名。在控制器背景下，Yii
将在 `views/site/` 目录下寻找该控制器的视图文件，其中 `site` 是控制器 ID 。更多有关视图名如何分解
的细节请参考[[yii\base\Controller::render()]]方法。

[[yii\base\Controller::render()|render()]]的第二个参数是键值对数组，控制器通过该数组将数据传递给视图，数组键为视图变量名，数组值在视图中通过引用相应的数组键变量名可获取使用。

上述动作 actionIndex 的视图是`views/site/index.php` ，在视图中可以如此使用：

```php
<p>Hello, <?= $username ?>!</p>
```
render()第二个参数的数组键'username' 在视图文件中作为变量名 $username 使用，引用输出的结果是第二个参数的数组值 'samdark'。

任何数据类型都可以传递给视图，包括数组和对象。

除了上述的[[yii\web\Controller::render()|render()]]方法，[[yii\web\Controller]]类还提供了一些其他的渲染方法，以下是这些方法的摘要：

* [[yii\web\Controller::render()|render()]]：渲染视图并应用布局到渲染结果，最常用于整个页面的渲染。
* [[yii\web\Controller::renderPartial()|renderPartial()]]：渲染无须布局的视图，常用于渲染页面片段。
* [[yii\web\Controller::renderAjax()|renderAjax()]]：渲染无须布局的视图并注入已注册的 JS/CSS 脚本文件。通常用于渲染响应 AJAX 请求的 HTML 输出。
* [[yii\web\Controller::renderFile()|renderFile()]]：渲染视图文件，和 [[yii\web\Controller::renderPartial()|renderPartial()]]类似，除了该方法使用视图文件路径而不是视图文件名做参数。


小部件
-------

小部件用于视图，是独立自足的积木块，一种结合复杂逻辑、显示和功能到简单组件的方法。一个小部件：

* 可能包括 PHP 高级编程
* 通常是可配置的
* 通常提供要显示的数据
* 在视图内返还要显示的 HTML

Yii 捆绑了大量的小部件，如[活动表单](form.md)，面包屑，菜单和[bootstrap 框架的封装小部件](bootstrap-widgets.md)。另外，Yii 扩展提供更多小部件，如[jQueryUI](http://www.jqueryui.com)的官方小部件。

要使用小部件，视图文件须如下操作：

```php
// 注意必须 "echo" 结果才能显示
echo \yii\widgets\Menu::widget(['items' => $items]);

// 传递数组以初始化对象属性
$form = \yii\widgets\ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => ['inputOptions' => ['class' => 'input-xlarge']],
]);
... 表单输入数据在此 ...
\yii\widgets\ActiveForm::end();
```

上述第一例，[[yii\base\Widget::widget()|widget()]]方法用来调用一个只需要输出内容的小部件。第二例中，[[yii\base\Widget::begin()|begin()]] 和 [[yii\base\Widget::end()|end()]]用于包含内容的小部件，小部件在被调用的两个方法之间输出其内容。以表单为例，输出的是 `<form>` 标签和一些设置的属性。


安全
--------

主要的安全原则之一是始终转义输出。违反该原则将导致脚本执行，更可能导致被称为 XSS 的跨站点脚本攻击，以致管理员密码泄露，使用户可以自动执行动作等。

Yii 提供了很好的工具以帮助你转义输出。最基本的转义要求是文本不带任何标记。可以如下这样处理：

```php
<?php
use yii\helpers\Html;
?>

<div class="username">
    <?= Html::encode($user->name) ?>
</div>
```

如果需要渲染的 HTML 变得复杂，可以分配转义任务给优秀的[HTMLPurifier](http://htmlpurifier.org/)库，这个库在 Yii 中包装成一个助手类[[yii\helpers\HtmlPurifier]]：

```php
<?php
use yii\helpers\HtmlPurifier;
?>

<div class="post">
    <?= HtmlPurifier::process($post->text) ?>
</div>
```

注意虽然 HTMLPurifier 在输出安全上非常优秀，但它不是非常快速，所以可考虑[缓存结果](caching.md)。

任选其一的两种模板语言
------------------------------

官方扩展的模板引擎有[Smarty](http://www.smarty.net/) 和 [Twig](http://twig.sensiolabs.org/)。了解更多内容请参考本指南的[使用模板引擎](template.md)部分。

模板中使用视图对象
------------------------------

[[yii\web\View]]组件的实例在视图模板中可用，以`$this` 变量表示。模板中使用视图对象可以完成许多有用的事情，如设置页面标题和元标签（meta tags），注册脚本和访问环境（控制器或小部件）。

### 设置页面标题

通常在视图模板设置页面标题。既然可以使用`$this` 访问视图对象，设置标题变得非常简单：

```php
$this->title = 'My page title';
```

### 添加元标签

添加元标签（meta tags）如编码、描述、关键词用视图对象也是非常简单的：

```php
$this->registerMetaTag(['encoding' => 'utf-8']);
```

第一个参数是 `<meta>` 标签选项名和值的映射。以上代码将生成：

```html
<meta encoding="utf-8">
```

有时一个类型只允许存在一条标签，这种情况需要指定第二个参数：

```html
$this->registerMetaTag(['name' => 'description', 'content' => 'This is my cool website made with Yii!'], 'meta-description');
$this->registerMetaTag(['name' => 'description', 'content' => 'This website is about funny raccoons.'], 'meta-description');
```

如果有第二个参数相同的多个调用（该例是 `meta-description` ），后者将覆盖前者，只有一条标签被渲染：

```html
<meta name="description" content="This website is about funny raccoons.">
```

### 注册链接标签

`<link>` 标签在许多情况都非常有用，如自定义网站图标、指向 RSS 订阅和分派 OpenID 到另一个服务器。 Yii 的视图对象有一个方法可以完成这些目标：

```php
$this->registerLinkTag([
    'title' => 'Lives News for Yii Framework',
    'rel' => 'alternate',
    'type' => 'application/rss+xml',
    'href' => 'http://www.yiiframework.com/rss.xml/',
]);
```

以上代码将得到以下结果：

```html
<link title="Lives News for Yii Framework" rel="alternate" type="application/rss+xml" href="http://www.yiiframework.com/rss.xml/" />
```

和 meta 标签一样可以指定另一个参数来确保一个类型只有一个链接被注册。

### 注册 CSS

用[[yii\web\View::registerCss()|registerCss()]] 或 [[yii\web\View::registerCssFile()|registerCssFile()]]来注册 CSS。前者注册 CSS 代码块，而后者注册了一个外部的 CSS 文件。如：

```php
$this->registerCss("body { background: #f00; }");
```

以上代码运行结果是添加下面代码到页面的 head 部分：

```html
<style>
body { background: #f00; }
</style>
```

要指定样式标签的其他属性，可以传递键值对数组到第三个参数。如需确保只有一个样式标签，用第四个参数，方法如 meta 标签描述的一样。

```php
$this->registerCssFile("http://example.com/css/themes/black-and-white.css", [BootstrapAsset::className()], ['media' => 'print'], 'css-print-theme');
```

以上代码将添加一条 CSS 文件链接到页面的 head 部分。

* 第一个参数指定要注册的 CSS 文件。
* 第二个参数指定该 CSS 文件基于[[yii\bootstrap\BootstrapAsset|BootstrapAsset]]，意味着该 CSS 文件将添加在[[yii\bootstrap\BootstrapAsset|BootstrapAsset]]的 CSS 文件后面。不指定这个依赖关系，这个 CSS 文件和[[yii\bootstrap\BootstrapAsset|BootstrapAsset]] CSS 文件的相对位置就是未定义的。
* 第三个参数指定`<link>` 标签有哪些属性。
* 最后一个参数指定识别该 CSS 文件的 ID 。如没提供，将使用 CSS 文件的 URL 替代。

强烈推荐使用[资源包](assets.md)来注册外部 CSS 文件，而不是使用[[yii\web\View::registerCssFile()|registerCssFile()]]。资源包允许你结合和压缩多个 CSS 文件，这在大流量站点非常可取。


### 注册脚本文件

[[yii\web\View]]对象可以注册脚本，有两个专用方法：
用于内部脚本的[[yii\web\View::registerJs()|registerJs()]]和用于外部脚本文件的[[yii\web\View::registerJsFile()|registerJsFile()]]。内部脚本在配置和动态生成代码上非常有用。方法添加这些功能的使用如下：

```php
$this->registerJs("var options = ".json_encode($options).";", View::POS_END, 'my-options');
```

第一个参数是要插入页码真正的 JS 代码，第二个参数是确定脚本在页面的哪个位置插入，可能的值有：

- [[yii\web\View::POS_HEAD|View::POS_HEAD]] 头部
- [[yii\web\View::POS_BEGIN|View::POS_BEGIN]] 刚打开 `<body>` 后
- [[yii\web\View::POS_END|View::POS_END]] 刚关闭 `</body>` 前
- [[yii\web\View::POS_READY|View::POS_READY]] 文档 `ready` 事件执行代码时。这将自动注册[[yii\web\JqueryAsset|jQuery]]。
- [[yii\web\View::POS_LOAD|View::POS_LOAD]] 文档`load`事件执行代码时，这将自动注册[[yii\web\JqueryAsset|jQuery]]。

最后的参数是用来识别代码块的唯一脚本 ID ，ID 相同将替换存在的脚本代码而不是添加新的。如不提供， JS 代码会用自己来做脚本 ID 。

外部脚本可以如下这样添加：

```php
$this->registerJsFile('http://example.com/js/main.js', [JqueryAsset::className()]);
```

[[yii\web\View::registerJsFile()|registerJsFile()]]的参数和
[[yii\web\View::registerCssFile()|registerCssFile()]]的参数类似。上例中依赖 `JqueryAsset` 注册`main.js` 文件。就是说 `main.js` 文件添加在 `jquery.js` 后面。不指明这个依赖关系，
`main.js` 和 `jquery.js` 的相对位置就是未定义。

如同[[yii\web\View::registerCssFile()|registerCssFile()]]，强烈推荐使用[资源包](assets.md)来注册外部 JS 文件而不是使用[[yii\web\View::registerJsFile()|registerJsFile()]]。


### 注册资源包

如前所述，使用资源包替代直接使用 CSS 和 JS 是更好的方式。定义资源包的更多细节请参考本指南的[资源管理](assets.md)部分。使用已定义资源包是非常直观的：

```php
\frontend\assets\AppAsset::register($this);
```

### 布局

布局是表现页面通用部分的便利方式。通用部分可以在全部页面或至少你应用的大多数页面通用。通常布局包括`<head>` 部分，footer，主菜单和这样的元素。可以在[基础应用模板](apps-basic.md)找到布局的使用示例。这里将回顾一个非常基本、没有任何小部件或额外标记的布局：

```php
<?php
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <div class="container">
        <?= $content ?>
    </div>
    <footer class="footer">© 2013 me :)</footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
```

以上标记是一些代码，首先， `$content` 是一个变量，这个变量包含控制器 `$this->render()` 方法渲染视图的结果。

通过标准的 PHP  `use` 表达式来引入（ import ）[[yii\helpers\Html|Html]]助手类，该助手类通常用于绝大多数需要转义输出数据的视图。

一些特别的方法如 [[yii\web\View::beginPage()|beginPage()]]/[[yii\web\View::endPage()|endPage()]],
        [[yii\web\View::head()|head()]], [[yii\web\View::beginBody()|beginBody()]]/[[yii\web\View::endBody()|endBody()]]触发用于注册脚本、链接和其他页面处理的渲染事件。需要一直包括这些在布局以便渲染正常工作。


### 局部视图（partials）

通常在许多页面中需要复用一些 HTML 标记，而为此创建全功能的小部件又太夸张，这种情况可以使用局部。

局部视图也是一个视图，位于 `views` 下相应的视图目录，并约定以下划线 `_`开头。例如，渲染一系列用户简介的同时在其他地方显示单个简介。

首先需要定义一个用户简介的局部视图 `_profile.php` ：

```php
<?php
use yii\helpers\Html;
?>

<div class="profile">
    <h2><?= Html::encode($username) ?></h2>
    <p><?= Html::encode($tagline) ?></p>
</div>
```

然后在需要显示一系列用户的 `index.php` 视图文件使用：

```php
<div class="user-index">
    <?php
    foreach ($users as $user) {
        echo $this->render('_profile', [
            'username' => $user->name,
            'tagline' => $user->tagline,
        ]);
    }
    ?>
</div>
```

同样的方式在其他视图复用它来显示单个用户简介：

```php
echo $this->render('_profile', [
    'username' => $user->name,
    'tagline' => $user->tagline,
]);
```

当调用 `render()` 来渲染当前视图的局部视图，可以使用不同格式来指向局部视图。最经常使用的格式是所谓的相对路径视图名，如上例所示。局部视图文件和目录里当前视图的路径是相对的。如果局部视图位于子目录，要在视图名包含子目录名，如 `public/_profile` 。


也可以使用路径别名来指向一个视图，如， `@app/views/common/_profile` 。

也可以使用所谓的绝对路径视图名，如 `/user/_profile`, `//user/_profile`。绝对路径视图名以单斜线或双斜线开始。如果以单斜线开头，视图文件将在当前活动模块的视图路径搜寻，否则，将从应用根视图目录开始搜寻。

### 访问视图所处环境（控制器、小部件）

视图通常由控制器或小部件使用。这两种情况视图渲染对象通过 `$this->context` 在视图中都生效。如，需要在控制器渲染的视图中打印当前内部请求路径，可以使用以下代码：

```php
echo $this->context->getRoute();
```

### 缓存页面片段

更多有关页面片段缓存的内容请参考本指南的[缓存](caching.md)部分。

定制视图组件
--------------------------

既然视图已经是名为 `view` 的应用组件，现在可以用继承自[[yii\base\View]] 或 [[yii\web\View]]的自定义组件类来替换。通过`config/web.php`这样的配置文件就可以实现：

```php
return [
    // ...
    'components' => [
        'view' => [
            'class' => 'app\components\View',
        ],
        // ...
    ],
];
```
