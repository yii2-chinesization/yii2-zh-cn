控制器
==========

> 注意：该章节还在开发中。

控制器是应用的重要部分。它决定处理如何输入请求并创建响应。

通常控制器接收 HTTP 数据请求，返回 HTML、JSON 或 XML 格式的数据，响应请求。

基础
------

控制器位于应用的 `controllers` 目录，命名规范为 `SiteController.php`(控制器名+Controller)， `Site` 部分包括一系列动作。

基本的 web 控制器通常继承自[[yii\web\Controller]]：

```php
namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
        // 将渲染 "views/site/index.php"
        return $this->render('index');
    }

    public function actionTest()
    {
        // 仅打印 "test" 到浏览器
        return 'test';
    }
}
```

如你所见，控制器通常包括一系列动作，这些动作是公开的类方法，以`actionSomething`(action+动作名) 形式命名。
动作的输出结果，就是这些方法返回的结果：可以是字符串或[[yii\web\Response]]的实例，[示例](#custom-response-class)。
返回值将被 `response` 应用组件处理，该组件可以把输出转变为不同格式，如 JSON,XML。默认行为是输出原始的值（不改变输出值）。


路由（路径）
------

每个控制器动作有相应的内部路径。上例中 `actionIndex` 的路径是 `site/index` ，而 `actionTest` 的路径是 `site/test` 。在这个路径中 `site` 是指控制器 ID ，而 `test` 是动作 ID 。

访问确定控制器和动作的默认 URL 格式是`http://example.com/?r=controller/action` 。这个行为可以
完全自定义。更多细节请参考[URL 管理](url.md)。

如果控制器位于模块内，其动作的路径格式是 `module/controller/action` 。

控制器可以位于应用或模块的控制器目录的子目录，这样路径将在前面加上相应的目录名。如，有个 `UserController` 控制器位于 `controllers/admin` 目录下，该控制器的 `actionIndex` 动作的路径
将是 `admin/user/index` ， `admin/user` 是控制器 ID 。

如指定的模块、控制器或动作未找到，Yii 将返回“未找到”的页面和 HTTP 状态码 404 。

> 注意：如果模块名、控制器名或动作名包含驼峰式单词，内部路径将使用破折号。如`DateTimeController::actionFastForward` 的路径将是 `date-time/fast-forward`。

### 预设值

如用户未指定任何路由，如使用 `http://example.com/` 这样的 URL ，Yii 将启用默认路径。默认路径由[[yii\web\Application::defaultRoute]]方法定义，且 `site` 即 `SiteController` 将默认加载。

控制器有默认执行的动作。当用户请求未指明需要执行的动作时，如使用 `http://example.com/?r=site` 这样的 URL ，则默认的动作将被执行。当前预设的默认动作是 `index` 。
设置[[yii\base\Controller::defaultAction]]属性可以改变预设动作。

动作参数
-----------------

如前所述，一个简单的动作只是以 `actionSomething` 命名的公开方法。现在来回顾一下动作从 HTTP 获取参数的途径。

### 动作参数

可以为动作定义具名实参，会自动填充相应的 `$_GET` 值。这非常方便，不仅因为短语法，还因为有能力指定预设值：

```php
namespace app\controllers;

use yii\web\Controller;

class BlogController extends Controller
{
    public function actionView($id, $version = null)
    {
        $post = Post::find($id);
        $text = $post->text;

        if ($version) {
            $text = $post->getHistory($version);
        }

        return $this->render('view', [
            'post' => $post,
            'text' => $text,
        ]);
    }
}
```

上述动作可以用`http://example.com/?r=blog/view&id=42` 或`http://example.com/?r=blog/view&id=42&version=3` 访问。前者 `version` 没有指定，将使用默认参数值填充。

### 从请求获取数据


如果动作运行的数据来自 HTTP请求的POST 或有太多的GET 参数，可以依靠 request 对象以 `\Yii::$app->request` 的方式来访问：
```php
namespace app\controllers;

use yii\web\Controller;
use yii\web\HttpException;

class BlogController extends Controller
{
    public function actionUpdate($id)
    {
        $post = Post::find($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }

        if (\Yii::$app->request->isPost) {
            $post->load(Yii::$app->request->post());
            if ($post->save()) {
                return $this->redirect(['view', 'id' => $post->id]);
            }
        }

        return $this->render('update', ['post' => $post]);
    }
}
```

独立动作类
------------------

如果动作非常通用，最好用单独的类实现以便重用。创建 `actions/Page.php` ：

```php
namespace app\actions;

class Page extends \yii\base\Action
{
    public $view = 'index';

    public function run()
    {
        return $this->controller->render($view);
    }
}
```

以下代码对于实现单独的动作类虽然简单，但提供了如何使用动作类的想法。实现的动作可以在控制器中如下这般使用:

```php
class SiteController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'about' => [
                'class' => 'app\actions\Page',
                'view' => 'about',
            ],
        ];
    }
}
```

如上使用后可以通过 `http://example.com/?r=site/about` 访问该动作。

动作过滤器
--------------

可能会对控制器动作使用一些过滤器来实现如确定谁能访问当前动作、渲染动作结果的方式等任务。

动作过滤器是[[yii\base\ActionFilter]]子类的实例。

使用动作过滤器是附加为控制器或模块的行为（behavior）。下例展示了如何为 `index` 动作开启 HTTP 缓存：

```php
public function behaviors()
{
    return [
        'httpCache' => [
            'class' => \yii\filters\HttpCache::className(),
            'only' => ['index'],
            'lastModified' => function ($action, $params) {
                $q = new \yii\db\Query();
                return $q->from('user')->max('updated_at');
            },
        ],
    ];
}
```

可以同时使用多个动作过滤器。过滤器启用的顺序定义在`behaviors()`。如任一个过滤器取消动作执行，后面的过滤器将跳过。

过滤器附加到控制器，就被该控制器的所有动作使用；如附加到模块（或整个应用），则模块内所有控制器的所有动作都可以使用该过滤器（或应用的所有控制器的所有动作可以使用该过滤器）。

创建新的动作过滤器，继承[[yii\base\ActionFilter]]并覆写[[yii\base\ActionFilter::beforeAction()|beforeAction()]] 和 [[yii\base\ActionFilter::afterAction()|afterAction()]]方法，前者在动作运行前执行，而后者在动作运行后执行。[[yii\base\ActionFilter::beforeAction()|beforeAction()]]返回值决定动作是否执行。如果过滤器的 `beforeAction()` 返回 false ，该过滤器之后的过滤器都会跳过，且动作也不会执行。

本指南的[授权](authorization.md)部分展示了如何使用[[yii\filters\AccessControl]]过滤器，[缓存](caching.md)部分提供有关[[yii\filters\PageCache]] 和 [[yii\filters\HttpCache]]过滤器更多细节。
这些内置过滤器是你创建自己的过滤器的良好参考。

捕获所有请求
----------------

有时使用一个简单的控制器动作处理所有请求是有用的。如，当网站维护时显示一条布告。动态或通过应用配置文件配置 web 应用的 `catchAll` 属性可以实现该目的：

```php
return [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    // ...
    'catchAll' => [ // <-- 这里配置
        'offline/notice',
        'param1' => 'value1',
        'param2' => 'value2',
    ],
]
```

上面 `offline/notice` 指向 `OfflineController::actionNotice()` 。 `param1` 和 `param2` 是传递给动作方法的参数。

自定义响应类
---------------------

```php
namespace app\controllers;

use yii\web\Controller;
use app\components\web\MyCustomResponse; //继承自 yii\web\Response

class SiteController extends Controller
{
    public function actionCustom()
    {
        /*
         * 这里做你自己的事
         * 既然 Response 类继承自 yii\base\Object,
         * 可以在__constructor() 传递简单数组初始化该类的值
         */
        return new MyCustomResponse(['data' => $myCustomData]);
    }
}
```

也可参考
--------

- [控制台](console.md)
