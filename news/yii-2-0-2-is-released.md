> [原文：www.yiiframework.com/news/](http://www.yiiframework.com/news/84/yii-2-0-2-is-released/)  
主翻译：@qiansen1386 (东方孤思子) 校对：也是这货~ 时间：2015年1月11号 转换工具：[PANDOC ONLINE](https://foliovision.com/seo-tools/pandoc-online)

> 特别提醒：文中 Yii 文档之外的外部链接绝大多数为原文链接，并未提供中文链接。如有需要可以自己找找大多数都有全世界的华人志愿者提供的汉化版本。如有特别需要，也可以给我们[提交翻译请求，不过最好是本身带 Markdown 格式的文件](https://github.com/yii2-chinesization/yii2-zh-cn/issues)。

Yii 2.0.2 发布了！
=====================

很荣幸地向大家宣布：Yii 框架 2.0.2 版本隆重面世了。要安装或升级到该版本的乡亲们，请前往 [http://www.yiiframework.com/download/](http://www.yiiframework.com/download/) 了解更多资讯。

2.0.2 版本是一个 Yii 2 的修订升级，包含 40 项小的功能改进和 bug 修复。完整的修改列表请参见 [change log](https://github.com/yiisoft/yii2/blob/2.0.2/framework/CHANGELOG.md)。特此感谢[所有的贡献人](https://github.com/yiisoft/yii2/graphs/contributors)，感谢他们为 Yii 的改进和提升所花费的宝贵时间，正因为有他们的支持才有了此次的发布。

你可以通过星标（star）或关注（watch）[Yii 2.0 GitHub 项目](https://github.com/yiisoft/yii2)跟进了解开发进度。也可以关注 Yii 的 [推特](https://twitter.com/yiiframework)或[脸熟小组](https://www.facebook.com/groups/yiitalk/) 与开发小组保持互动。

下面，我们将列举此次更新中的一些重点。

路由别名（Route Alias）
-----------

之前，核心框架代码只支持代表文件路径和 URL 的别名。现在，我们添加了对路由别名的支持。具体而言，你可以给路由设置别名，然后当你需要创建相应的 URL 的时候，就可以用别名指向该路由。路由别名注意通过 `Url::to()` 以及 `Url::toRoute()`助手方法实现。举例而言，

```php
use yii\helpers\Url;
     
Yii::setAlias('@posts', 'post/index');
 
// /index.php?r=post/index
echo Url::to(['@posts']);
echo Url::toRoute('@posts');
```

你会发现，当路由被设计为非固定值，且你不想每次路由改变的时候，都修改该路由的所有 URL 生成代码。此时，路由别名就会非常之有用。
You may find route alias to be useful when your route design is not fixed and you want to avoid changing your URL creation code everywhere when your route design is changed.

依赖组件配置 Dependent Component Configuration
---------------------------------

许多组件都包含有需要被设置为某一个依赖应用组件的 ID 的属性，比如：`yii\caching\DbCache::db`、`yii\web\CacheSession::cache`。优势，为了避免引入新的应用组件或出于便于单元测试的目的，你可能需要直接用一个可用于创建该依赖组件的配置数组，来设置该属性。现在，你可以这样做：

Many components contain properties that should be configured to be the ID of a dependent application component, such as `yii\caching\DbCache::db`, `yii\web\CacheSession::cache`. Sometimes, to avoid introducing new application component or for unit testing purpose, you may want to directly configure such a property with a configuration array that can be used to create the dependent component. You can do so now like the following:

```php
$cache = Yii::createObject([
    'class' => 'yii\caching\DbCache',
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => '...',
    ],
]);
```
如果你在开发一个依赖于外部组件的新类，你可以用以下方法轻易地获得类似效果：

If you are developing a new class that depends on an external component, you may use the following approach to obtain the similar support readily:

```php
use yii\base\Object;
use yii\db\Connection;
use yii\di\Instance;
 
class MyClass extends Object 
{
    public $db = 'db';
 
    public function init() 
    {
        $this->db = Instance::ensure($this->db, Connection::className());
    }
}
```

以上代码中，`db` 属性可被设置为以下三种格式的初始值中的任意一种：

-   指向某应用组件 ID 的字符串；
-   某 `yii\db\Connection` 实例；
-   可用于生成 `yii\db\Connection` 实例的配置数组。

永久的文章网址（Immutable Slug）
--------------

> 译者注： Slug 就是通过文章的 ID 或标题等属性值自动生成的以 `-` 分隔的 URL。[wordpress 方面有一个不错的解释](http://codex.wordpress.org/zh-cn:%E9%A1%B5%E9%9D%A2#.E4.BF.AE.E6.94.B9.E9.A1.B5.E9.9D.A2.E7.9A.84URL.EF.BC.88.E6.88.96.22Slug.22.EF.BC.89)，以下简称 slug。

若你正在使用 `yii\behaviors\SluggableBehavior`，你现在可以应用一个名为 `immutable` 的新属性。通过把它设置为 true，可以确保一旦 slug 已经生成过了，那么即使是对应的源属性值被修改过，它也不会再更改了。这对 SEO 的优化特别有用，因为你不会想要修改一个已经发布的 slug URL 的。（译注：已经被收录的文章修改 URL 轻则降低权重，重则直接删除。科学研究表明，随意修改 URL 是毁灭网站排名权重的重要手段之一）

日期选择器的语言回退（DatePicker Language Fallback）
----------------------------

`yii\jui\DatePicker` 小部件现在支持语言回退。主要在当它的 `language` 属性被设置为，含有地区标识或本身被分为多段的区域码时，会很有用。说具体点就是，假如 `language` 被设置为 `de-DE`（德语-奥地利）, 且该小部件无法找到名为 `/ui/i18n/datepicker-de-DE.js` 的语言文件，它就会回退到语言 `de` 也就是德语，并尝试寻找 `/ui/i18n/datepicker-de.js` 语言文件。

传递验证错误（Passing Validation Errors）
-------------------------

`yii\base\Model` 类现在有了一个方便的方法叫 `addErrors()`，它可以让你把验证错误，从一个模型类传递到另一个。举例而言，假如现在有一个表单模型类（Form Model），该表单模型包含有一个活动记录模型（ActiveRecord Model），你可能会需要把表单模型的验证错误传递到活动记录之中，此时你就可以轻松地调用该方法来实现：

```php
use yii\base\Model;
use yii\db\ActiveRecord;
 
class MyForm extends Model 
{
    public $model;
 
    public function process()
    {
        // ...
        if (!$this->validate()) {
            $this->model->addErrors($this->getErrors());
            // ....
        }
    }
}
```
                    