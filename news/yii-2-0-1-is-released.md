> [原文：www.yiiframework.com/news/](http://www.yiiframework.com/news/82/yii-2-0-1-is-released/)  
主翻译：@qiansen1386 (东方孤思子) 校对：也是这货~ 时间：2014年12月7号 转换工具：[PANDOC ONLINE](https://foliovision.com/seo-tools/pandoc-online)

> 特别提醒：文中 Yii 文档之外的外部链接绝大多数为原文链接，并未提供中文链接。如有需要可以自己找找大多数都有全世界的华人志愿者提供的汉化版本。如有特别需要，也可以给我们[提交翻译请求，不过最好是本身带 Markdown 格式的文件](https://github.com/yii2-chinesization/yii2-zh-cn/issues)。

Yii 2.0.2 发布了！
=====================

很荣幸地向大家宣布：Yii 框架 2.0.1 版本隆重面世了。要安装或升级到该版本的乡亲们，请前往 [http://www.yiiframework.com/download/](http://www.yiiframework.com/download/) 了解更多资讯。

2.0.1 版本是一个 Yii 2.0 的修订升级，包含 90 项小的功能改进和 bug 修复。完整的修改列表请参见 [change log](https://github.com/yiisoft/yii2/blob/2.0.2/framework/CHANGELOG.md)。特此感谢[所有的贡献人](https://github.com/yiisoft/yii2/graphs/contributors)，感谢他们为 Yii 的改进和提升所花费的宝贵时间，正因为有他们的支持才有了此次的发布。

你可以通过星标（star）或关注（watch）[Yii 2.0 GitHub 项目](https://github.com/yiisoft/yii2)跟进了解开发进度。也可以关注 Yii 的 [推特](https://twitter.com/yiiframework)或[脸熟小组](https://www.facebook.com/groups/yiitalk/) 与开发小组保持互动。

下面，我们将列举此次更新中的一些重点。

强制 Asset 转换（Forcing Asset Conversion）
------------------------

Asset bundle 支持自动化的 asset 转换，比如要把 LESS 转换为 CSS。然而，该操作要花费很大的代价，来确保能良好地对 Asset 源文件的改动进行检测，尤其是当部分前端资源中，还包含有其他的前端资源时。为了解决这个问题，你现在可以如下配置 `assetManager`，从而始终强制执行前端资源转换（译注：也就是不检查了）：

```php
[
    'components' =>  [
        'assetManager' => [
            'converter' => [
                'forceConvert' => true,
            ]
        ]
    ]
];
```

选择子查询（Selecting Sub-queries）
---------------------

Query builder 支持在不同的地方使用子查询。现在你也可以在 `SELECT` 部分中调用子查询。举例而言，

```php
$subQuery = (new Query)->select('COUNT(*)')->from('user');
$query = (new Query)->select(['id', 'count' => $subQuery])->from('post');
// $query represents the following SQL:
// SELECT `id`, (SELECT COUNT(*) FROM `user`) AS `count` FROM `post`
```

防止在 AJAX 中重复载入 CSS
--------------------------------

之前，Yii 已经提供了对于防止在 AJAX 响应中重复载入相同 JavaScript 文件的支持。现在它也提供了对防止在 AJAX 响应里重复载入相同 CSS 文件的支持。要使用该功能，你只需像这样简单地调用 `YiiAsset` asset bundle：

```php
yii\web\YiiAsset::register($view);
```

清空 schema 缓存（Flushing Schema Cache）
---------------------

引入了一个新的控制台命令，允许你清空 schema 缓存。他在你需要部署需要更改数据库 schema 到生产环境服务器的代码时很有用。直接运行以下命令：
A new console command is added to allow you to flush schema cache. This is useful when you deploy code that cause DB schema changes to production servers. Simply run the command as follows:

```cmd
yii cache/flush-schema
```

助手类增强(Helpers）
-----------------------

`Html::cssFile()` 方法现在支持 `noscript` 选项了，它会把生成的 `link` 标签封装进 `noscript` 标签。你也可以通过配置 asset bundle 的 `AssetBundle::cssOptions` 属性，以启用该选项，比如：

```php
use yii\helpers\Html;
 
echo Html::cssFile('/css/jquery.fileupload-noscript.css', ['noscript' => true]);
```

之前 `StringHelper::truncate()` 只支持处理纯文本格式的字符串。现在他也支持 HTML 格式的字符串啦，它会确保缩短后的返回值依旧为有效的 HTML 字符串。

`Inflector` 类新增了一个方法叫 `sentence()`，它会把一小撮单词串联成句子。比如说，

```php
use yii\helpers\Inflector;
 
$words = ['Spain', 'France'];
echo Inflector::sentence($words);
// 输出：Spain and France
 
$words = ['Spain', 'France', 'Italy'];
echo Inflector::sentence($words);
// 输出：Spain, France and Italy
 
$words = ['Spain', 'France', 'Italy'];
echo Inflector::sentence($words, ' & ');
// 输出：Spain, France & Italy
```

Bootstrap 扩展增强
-----------------------------------

首先，Twitter Bootstrap 现在已升级到 3.3.x 的版本。如果你想要继续使用之前的版本，你可以在你项目的 `composer.json` 中明确指定该版本。

一些 Bootstrap 小部件添加了一些属性。请参考[Class Reference](http://apidoc.yii2.cn/guide-widget-bootstrap.html)了解更多。

-   `yii\bootstrap\ButtonDropdown::$containerOptions`
-   `yii\bootstrap\Modal::$headerOptions`
-   `yii\bootstrap\Modal::$footerOptions`
-   `yii\bootstrap\Tabs::renderTabContent`
-   `yii\bootstrap\ButtonDropdown::$containerOptions`

MongoDB 扩展增强
---------------------------------

现在 `yii\mongodb\Query` 和 `yii\mongodb\ActiveQuery`均支持 `findAndModify` （查询并修改）操作。举个栗子，

```php
User::find()->where(['status' => 'new'])->modify(['$set'=>['status' => 'processing']]);
```

为了显示 MongoDB 的查询过程，还添加了一个新的 debug 面板。要启用该面板，只需参照如下配置 Yii debugger：

```php
[
    'class' => 'yii\debug\Module',
    'panels' => [
        'mongodb' => [
            'class' => 'yii\mongodb\debug\MongoDbPanel',
        ]
    ],
]
```

Redis 扩展增强
-------------------------------

Yii Redis 扩展现支持使用 UNIX socket 连接，其相较于基于 TCP 的连接方式快了 50%。要使用它，只需这样配置 redis 连接即可：

```php
[
    'class' => 'yii\redis\Connection',
    'unixSocket' => '/var/run/redis/redis.sock',
]
```