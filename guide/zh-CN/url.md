URL 管理
==============

URL 管理在 Yii 中的概念相当简单，使用URL 管理的前提是应用在任何地方都使用
内部路由和参数。根据 URL配置，框架自己就能把路由翻译成 URL，反之亦然。这种方法允许您只需通过
编辑配置文件就能改变整站范围的 URL ,而不需要改动应用代码。

内部路由
---------------

Yii应用处理的内部路由通常指的是路由及参数。
每个控制器及其动作都有对应的内部路由，比如`site/index` 或 `user/create`。
前一例中的`site` 被称为 *controller ID* （控制器ID），而 `index` 被称为 *action ID*（动作ID）。
第二例中的`user` 是控制器ID，`create` 是动作ID。如果控制器在 *module* （模块）内部，
内部路由则以模块ID开头，比如 `blog/post/index` 是 
blog 模块的 post 控制器的 index 动作。

创建URL
-------------

为站点创建URL最重要的规则就是始终使用 URL 管理器，URL 管理器是一个名叫 `urlManager` 的内置应用组件。这个组件在Web应用和控制台应用中都可以通过 
`\Yii::$app->urlManager`. 组件提供以下两种创建 URL 的方法：

- `createUrl($params)`
- `createAbsoluteUrl($params, $schema = null)`

`createUrl()` 方法根据应用根目录的相对位置生成URL，比如 `/index.php/site/index/`。
`createAbsoluteUrl()` 方法生成的是绝对路径 URL ，即以主机名和协议开头的 URL ：
`http://www.example.com/index.php/site/index`. 前者适用于应用内部的URL，而后者
用于创建URL给外部资源使用，比如连接到第三方服务，发送邮件，
生成RSS提要等。

一些例子：

```php
echo \Yii::$app->urlManager->createUrl(['site/page', 'id' => 'about']);
// /index.php/site/page/id/about/
echo \Yii::$app->urlManager->createUrl(['date-time/fast-forward', 'id' => 105])
// /index.php?r=date-time/fast-forward&id=105
echo \Yii::$app->urlManager->createAbsoluteUrl('blog/post/index');
// http://www.example.com/index.php/blog/post/index/
```

URL采用哪种格式取决于 URL 的配置。
上面的例子可以输出以下格式的 URL ：

* `/site/page/id/about/`
* `/index.php?r=site/page&id=about`
* `/index.php?r=date-time/fast-forward&id=105`
* `/index.php/date-time/fast-forward?id=105`
* `http://www.example.com/blog/post/index/`
* `http://www.example.com/index.php?r=blog/post/index`

使用 [[yii\helpers\Url]] Url 助手可简化 URL 的创建，假设有 URL  `/index.php?r=management/default/users&id=10` ，以下说明
`Url` 助手是如何工作的：

```php
use yii\helpers\Url;

// 当前活动路由
// /index.php?r=management/default/users
echo Url::to('');

// 相同的控制器，不同的动作
// /index.php?r=management/default/page&id=contact
echo Url::toRoute(['page', 'id' => 'contact']);


// 相同模块，不同控制器和动作
// /index.php?r=management/post/index
echo Url::toRoute('post/index');

// 绝对路由，不管是被哪个控制器调用
// /index.php?r=site/index
echo Url::toRoute('/site/index');

// 区分大小写的控制器动作 `actionHiTech` 的 url 格式
// /index.php?r=management/default/hi-tech
echo Url::toRoute('hi-tech');

// 控制器和动作都区分大小写的 url，如'DateTimeController::actionFastForward' ：
// /index.php?r=date-time/fast-forward&id=105
echo Url::toRoute(['/date-time/fast-forward', 'id' => 105]);

//  从别名中获取 URL 
// http://google.com/
Yii::setAlias('@google', 'http://google.com/');
echo Url::to('@google');

// 获取当前页的标准 URL 
// /index.php?r=management/default/users
echo Url::canonical();

// 获得 home 主页的 URL
// /index.php?r=site/index
echo Url::home();

Url::remember() ; //  保存URL以供下次使用
Url::previous(); // 取出前面保存的 URL 
```

> **小技巧**： 为生成一个指向 # 号（锚连接 ID ）的 URL ，比如 `/index.php?r=site/page&id=100#title`， 你要
  指定 `#` 参数 ，采用  `Url::to(['post/read', 'id' => 100, '#' => 'title'])` 来创建。

自定义 URL 
----------------

缺省情况下， Yii 用 query string （查询字符串）的格式，如`/index.php?r=news/view&id=100`。
为了让 URL 更人性化，比如更易读。你需要在应用配置文件中，配置一下`urlManager` 组件，
通过"pretty"（美化）URL选项，你可以把查询字符串格式的 URL 转换成目录格式的 URL（`/index.php/news/view?id=100`）。
而禁用`showScriptName`参数将去除 URL 的 `index.php` 一部分。
这里是应用配置文件中与此相关的部分：

```php
<?php
return [
    // ...
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
];
```

请注意，只有当 web 服务器正确配置Yii时该配置内容才能正常工作，参阅 
[installation](installation.md#recommended-apache-configuration).

### 命名参数

URL 格式规则可以关联一些 `GET` 参数，这些 `GET` 参数以如下格式出现在规则表达式中：

```
<ParameterName:ParameterPattern>
```

`ParameterName`（参数名）是 `GET` 参数的名称，可选的`ParameterPattern`（参数表达式）是可选的，是一个正则表达式，用于
匹配`GET`参数的值。如果`ParameterPattern`被省略，表示这条规则将匹配
除 `/` 外的任何字符。 当创建一个URL时，这些占位参数将会替换成
相应的参数值；当解析一个URL时，相应的GET参数将会被填充到解析结果中。

让我们用一些例子来说明 URL 规则是怎么工作的。假设我们的规则集由三个规则组成：

```php
[
    'posts'=>'post/list',
    'post/<id:\d+>'=>'post/read',
    'post/<year:\d{4}>/<title>'=>'post/read',
]
```

- 调用 `Url::toRoute('post/list')` 将生成 `/index.php/posts`. 第一条规则被应用。
- 调用 `Url::toRoute(['post/read', 'id' => 100])` 生成 `/index.php/post/100`. 第二条规则被应用。
- 调用 `Url::toRoute(['post/read', 'year' => 2008, 'title' => 'a sample post'])` 生成
    `/index.php/post/2008/a%20sample%20post`. 第三条规则被应用。
- 调用 `Url::toRoute('post/read')` 生成 `/index.php/post/read`. 没有规则被用应, 仅仅是应用了
  约定。

总之，当使用 `createUrl` 来生成 URL 时，路由和 `GET` 参数传递到用于决定
哪条规则被应用的方法中。如果传递到 `createUrl()` 的 `GET` 参数里有任何一个关联到规则的参数，
而且路由参数也匹配规则路由，那这条规则将用于生成 URL 。

如果传递到 `Url::toRoute` 的 `GET` 参数比规则要求的多，则多余的参数
将出现在查询字符串中，例如，如果我们调用  `Url::toRoute(['post/read', 'id' => 100, 'year' => 2008])` ，
会得到 `/index.php/post/100?year=2008`.

正如我们前面提到的， URL 规则的另一个目的是解析请求的 URL 地址，自然，这是一个创建
URL 地址的逆过程。例如，当用户请求 `/index.php/post/100` 时，上例中
第二条规则将被应用，即解析了路由 `post/read` 和 `GET` 参数 `['id' => 100]` 的规则
（通过 `Yii::$app->request->get('id')` 得到）。

###　参数化路由

我们可以引用规则中路由部分的命名参数，这将允许规则被应用于匹配标准（criteria）的多路由中。
命名参数也可以减少应用所需的规则数量，
以全面改进性能。

举例说明如何用命名参数来参数化路由：

```php
[
    '<controller:(post|comment)>/<id:\d+>/<action:(create|update|delete)>' => '<controller>/<action>',
    '<controller:(post|comment)>/<id:\d+>' => '<controller>/read',
    '<controller:(post|comment)>s' => '<controller>/list',
]
```

在以上例子中，在规则的路由部分使用了两个命名参数：控制器和动作。前者匹配一个 post 或 comment 的路由 ID ，后者匹配创建、更新和删除的动作 ID 。你也可以另外命名这些参数，只要它们和出现在 URL 中的 GET 参数没有冲突。

使用上述规则，URL  `/index.php/post/123/create`  将解析成post 控制器的 create 动作的路由，其 GET 参数是
`id=123` 。而给定路由  `comment/list` 和 `GET` 参数 `page=2` ，将创建URL  `/index.php/comments?page=2`。

### 参数化主机名

创建和解析 URL 也可以在规则中包括主机名。主机名的一部分将提取
作为  `GET` 参数。处理二级域名特别有用。如，
URL `http://admin.example.com/en/profile` 可以解析为 GET 参数`user=admin` 和 `lang=en` 。另一方面，
包括主机名的规则也可以用于创建带有参数化主机名的 URL 。

为应用参数化主机名，只需要用主机信息简单定义 URL 规则，如：

```php
[
    'http://<user:\w+>.example.com/<lang:\w+>/profile' => 'user/profile',
]
```

上例中主机名的第一部分被视为用户参数，
而路径信息的第一部分被视为语言参数。这个规则响应 `user/profile` 路由。

注意，当 URL 被使用参数化主机名的规则创建时，[[yii\web\UrlManager::showScriptName]] 将不再起作用。

还要注意，如果应用位于 WEB 根目录的子文件夹，包含参数化主机名的任何规则都*不能*包括子文件夹。
如，应用位于 `http://www.example.com/sandbox/blog` ，
那么仍然使用上面相同的规则，而不需要加上  `sandbox/blog` 。

### URL 伪后缀

```php
<?php
return [
    // ...
    'components' => [
        'urlManager' => [
            'suffix' => '.html',
        ],
    ],
];
```

### 处理 REST 请求

TBD:
- RESTful 风格路由: [[yii\filters\VerbFilter]], [[yii\filters\UrlManager::$rules]]
- Json API:
  - 响应: [[yii\web\Response::format]]
  - 请求: [[yii\web\Request::$parsers]], [[yii\web\JsonParser]]


URL 解析
-----------

除了完美创建 URL 外， Yii 也可以转换自定义格式的 URL 到内部路由和参数。

### URL 精确解析

默认，如果 URL 无定制规则且匹配默认格式如 `/site/page`，Yii 将允许相应的控制器的动作执行。这个行为（behavior，特有名词见词汇表）也可以配置为失效，这时将弹出 404 错误（没有找到该页面）。

```php
<?php
return [
    // ...
    'components' => [
        'urlManager' => [
            'enableStrictParsing' => true,
        ],
    ],
];
```

创建你自己的规则类
------------------------------

[[yii\web\UrlRule]] 类被用在解析 URL 到参数和基于参数创建 URL两方面。
尽管框架实现的URL 规则已经非常灵活，能够满足绝大多数项目的需求，
但仍有一些情况使用你自己的规则类才是最好的选择。如，在一个汽车交易网站，
我们想支持类似 `/Manufacturer/Model` 这样的 URL 格式，这个 URL 中的 `Manufacturer` 和  `Model` 都要匹配数据表的某些数据。
缺省规则类不适用这样的需求，因为它通常依赖无关数据库的静态正则表达式。

我们可以通过继承[[yii\web\UrlRule]] 来编写新的 URL 规则类并使用在一个或多个 URL 规则中。
以上面的汽车交易网站为例，我们可以在应用配置中定义以下 URL 规则：

```php
// ...
'components' => [
    'urlManager' => [
        'rules' => [
            '<action:(login|logout|about)>' => 'site/<action>',

            // ...

            ['class' => 'app\components\CarUrlRule', 'connectionID' => 'db', /* ... */],
        ],
    ],
],
```

通过以上配置，我们可以使用自定义的 URL 规则类 `CarUrlRule`来处理
 `/Manufacturer/Model` 格式的 URL 了。这个类可以这样写：

```php
namespace app\components;

use yii\web\UrlRule;

class CarUrlRule extends UrlRule
{
    public $connectionID = 'db';

    public function createUrl($manager, $route, $params)
    {
        if ($route === 'car/index') {
            if (isset($params['manufacturer'], $params['model'])) {
                return $params['manufacturer'] . '/' . $params['model'];
            } elseif (isset($params['manufacturer'])) {
                return $params['manufacturer'];
            }
        }
        return false;  // 规则没有被应用
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        if (preg_match('%^(\w+)(/(\w+))?$%', $pathInfo, $matches)) {
            // 输入$matches[1] 和 $matches[3] 看看
            // 如果它们匹配了数据库中的厂商和模型，
            // 赋值给$params['manufacturer'] 和 $params['model']
            // 并返回['car/index', $params]。
        }
        return false;  // 规则没有被应用
    }
}
```

除了上述用法，自定义 URL 规则类还可以实现许多目的。
如，我们可以写规则类来记录 URL 解析和创建请求的日志。
开发阶段这是非常有用处的。
我们也可以写规则类来显示特定的 404 错误类以防止所有其他 URL 规则
解析当前请求失败。注意这种情况，特定类的规则
必须定义在最后一条规则。
