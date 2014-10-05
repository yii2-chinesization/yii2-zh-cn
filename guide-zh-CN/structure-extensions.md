扩展
==========
扩展是专门用来在Yii应用程序中使用和提供准备使用的功能的可再发行软件包。例如:[yiisoft/yii2-debug](tool-debugger.md)扩展增加了手动
高度工具条在你应用程序的每个页面的底部，以帮助你更容易理解页面是如何生成的。你可以使用扩展以加快开发进程。你同样可以共享你的扩展
包给其他开发人员，传播发扬开源精神。


> 补充：我们使用"Extensions(扩展)"一词来指用于Yii的专用软件程序包。
  可以不特定在Yii中使用的通用软件程序包，我们将使用术语"package"或"library"是指它们。

## 使用扩展 <a name="using-extensions"></a> 
要使用一个扩展，你需要先安装它。在[Composer](https://getcomposer.org/)可以找到大多数扩展。你可以通过以下两种简单的步骤来安装:

1. 修改你程序中`composer.json`文件，指定要安装的扩展(Composer packages)。
2. 运行`composer install`安装指定的扩展

> 注意: 如果没有安装Composer，你必须先安装它。

默认情况下，Composer安装包登记在[Packagist](https://packagist.org/)这个最大的开源包信息库中，你可以在那里查找相应的扩展，
创建你自己的资源库(https://getcomposer.org/doc/05-repositories.md#repository)然后配置Composer并使用它。
如果你要共享一个封闭的开放扩展到项目中，这对你同样有用。

扩展通过Composer安装在'BasePath/vendor'目录下。`BasePath`参考程序的[base path](structure-applications.md#basePath)。
Composer是一个依赖管理，如果你安装了一个包，它还将安装所有依赖的包。

例如，安装`yiisoft/yii2-imagine`扩展，如下修改`composer.json`：
```json
{
    // ...

    "require": {
        // ... other dependencies

        "yiisoft/yii2-imagine": "*"
    }
}
```

安装完成后,你可以看到`yiisoft/yii2-imagine`目录在`BasePath/vendor`下。
你同样会看到其他目录 `imagine/imagine`已安装了其中包含的依赖包。

> 补充：`yiisoft/yii2-imagine`是一个由Yii开发和维护的核心扩展。所有
  核心扩展托管[Packagist](https://packagist.org/)，并像`yiisoft/yii2-xyz`进行命名，`xyz`用于对应不同的扩展。

现在你可以像你程序的一部分一样的来安装扩展。
以下示例告诉你如何使用`yiisoft/yii2-imagine`提供的`yii\imagine\Image`扩展。

```php
use Yii;
use yii\imagine\Image;

// generate a thumbnail image
Image::thumbnail('@webroot/img/test-image.jpg', 120, 120)
    ->save(Yii::getAlias('@runtime/thumb-test-image.jpg'), ['quality' => 50]);
```

> 补充：扩展类通过[Yii class autoloader](concept-autoloading.md)自动加载。


### 自动安装扩展 <a name="installing-extensions-manually"></a>

在某些特别情况下，你可能需要局部或全部通过手动安装扩展，而不是通过Composer。
那么你可以


1.下载扩展并解压到`vendor`文件夹。
2.如果扩展提供了自动加载类，就添加到autoloaders。
3.下载并按照指示安装所有依赖的扩展。



如果扩展没有提供自动加载类，但如下[PSR-4 standard](http://www.php-fig.org/psr/psr-4/)，
你可以使用Yii提供的自动加载类加载扩展。你仅仅在扩展的要目录下，为该扩展的声明[root alias](根别名)
(concept-aliases.md#defining-aliases)。示例：假设你已经目录中`vendor/mycompany/myext`安装了扩展，
并且扩展类使用了`myext`命名空间，那么你可以在你的应用主体配置中包括以下代码

```php
[
    'aliases' => [
        '@myext' => '@vendor/mycompany/myext',
    ],
]
```



## 创建一个扩展 <a name="creating-extensions"></a>

如果你觉得有必要与他们分享你的代码时，你可以创建一个扩展。
扩展可以包含任何你喜欢的代码，比如一个辅助类，窗口小部件，模块等

建议你创建一个[Composer包]标准的扩展(https://getcomposer.org/)，
使它可以更容易被其他用户安装和使用，如最后一个小节所述。

以下是你可以按照创建一个扩展的Composer包的基本步骤。

   
   
1. 为你的扩展创建一个项目，并将其登记在VCS资源库，如[github.com](https://github.com)。
	 关于扩展的开发和维护工作均在这个VCS资源库来完成。
2. 在项目的根目录下创建一个命名为`composer.json`的文件，被用于Composer。 了解更多详情请请参阅下一小节。
3. 登记你的扩展到Composer资源库，如[Packagist](https://packagist.org/)，这样其他用于就可以通过Composer找到你扩展并安装使用。

### `composer.json` <a name="composer-json"></a>

每一个Composer包都必须包含一个`composer.json`文件在他的根目录中。该文件包含有关该软件包的元数据。
你可能可以在[Composer 手册](https://getcomposer.org/doc/01-basic-usage.md#composer-json-project-setup)中找到关于此包完整规范的说明。
下面示例显示yiisoft/yii2-imagine`扩展的`composer.json`文件。

```json
{
    // package name
    "name": "yiisoft/yii2-imagine",

    // package type
    "type": "yii2-extension",

    "description": "The Imagine integration for the Yii framework",
    "keywords": ["yii2", "imagine", "image", "helper"],
    "license": "BSD-3-Clause",
    "support": {
        "issues": "https://github.com/yiisoft/yii2/issues?labels=ext%3Aimagine",
        "forum": "http://www.yiiframework.com/forum/",
        "wiki": "http://www.yiiframework.com/wiki/",
        "irc": "irc://irc.freenode.net/yii",
        "source": "https://github.com/yiisoft/yii2"
    },
    "authors": [
        {
            "name": "Antonio Ramirez",
            "email": "amigo.cobos@gmail.com"
        }
    ],

    // package dependencies
    "require": {
        "yiisoft/yii2": "*",
        "imagine/imagine": "v0.5.0"
    },

    // class autoloading specs
    "autoload": {
        "psr-4": {
            "yii\\imagine\\": ""
        }
    }
}
```


#### 包名称<a name="package-name"></a>


在Composer中，任何一个包都有必须有一个唯一标识的名称。
名字的格式为`vendorName/projectName`。例如包名为`yiisoft/yii2-imagine`，供应商名字和项目名字分别为`yiisoft` 和 `yii2-imagine`。

不要使用`yiisoft`作为供应商名称，因为它是保留给Yii的核心代码使用。

我们建议你前缀`yii2-`来命名项目名称，代表用于Yii2扩展。例如，`myname/yii2-mywidget`。
这样很容易让用户识别是否为属于Yii2的扩展。

#### 包类型 <a name="package-type"></a>

指定`yii2-extension`的扩展包的类型是很重要的，便以在安装的时候包可以被识别为Yii的扩展。


当用户运行 `composer install`安装扩展时，`vendor/yiisoft/extensions.php`文件将自动更新以包含新的扩展。
Yii程序通过这个文件知晓哪些扩展被安装。(该信息可通过以下方式访问[[yii\base\Application::extensions]])


#### 依赖项 <a name="dependencies"></a>

你的扩展依赖于Yii(当然)，所以必须在`composer.json`的`require`列表中加入`yiisoft/yii2`。
如果你的扩展同样依赖于其他扩展或第三方类库，你也同样要在列表中加入。
请确保你也可以为每一个依赖列出相应的版本限制(如. `1.*`, `@stable`)。
当你发布稳定的扩展时，请使用稳定的依赖。


很多JavaScript/CSS包通过[Bower](http://bower.io/)或[NPM](https://www.npmjs.org/)进行管理，而不是Composer。
Yii使用[Composer asset plugin](https://github.com/francoispluchino/composer-asset-plugin)以通过Composer管理各类型的包。
如果你的扩展依赖Bower上的包，你可以通过`composer.json`中的依赖性列表列出，示例：

```json
{
    // package dependencies
    "require": {
        "bower-asset/jquery": ">=1.11.*"
    }
}
```

上面的代码提示依赖于Bower上`jquery`包。在一般情况下，你在`composer.json`中可以使用`bower-asset/PackageName`隐射到Bower，
并使用`npm-asset/PackageName`隐射到对应的NPM包。当Composer安装一个Bower或NPM时，默认情况下包内容将分别被安装到
`@vendor/bower/PackageName`和`@vendor/npm/Packages`目录。这两个目录同样可以参照使用简短的别名`@bower/PackageName` 和
`@npm/PackageName`。

关于更多的资源管理信息，请阅读[资源管理](structure-assets.md#bower-npm-assets)部分

#### 自动加载类<a name="class-autoloading"></a>

为了让你的类被Yii类或Composer类的自动加载器自动加载，你需要在`composer.json`文件中指定的`autoload`项，如下所示：

```json
{
    // ....

    "autoload": {
        "psr-4": {
            "yii\\imagine\\": ""
        }
    }
}
```

你可以列出一个或多个根命名空间及其相应的文件路径。

当扩展被安装在一个应用程序，Yii的会为每个扩展列出的根命名空间。
这里的一个[别名]concept-aliases.md#extension-aliases)指对应于该命名空间中的目录。
例如，上面的`autoload`声明将对应到一个名为`@yii/imagine`的别名。


### 推荐实践 <a name="recommended-practices"></a>


由于扩展意味着被其他的人使用，你经常需要采取额外的开发工作。 
下面我们介绍在创造高品质的扩展一些常用的建议做法。

#### 命名空间 <a name="namespaces"></a>

为了避免你的类名称冲突，以使类能在扩展中自动加载，你应该使用命名空间，并按照[PSR-4 standard](http://www.php-fig.org/psr/psr-4/)
和[PSR-0 standard](http://www.php-fig.org/psr/psr-0/)的标准命名扩展的类名称。

你的命名空间应该以`vendorName\extensionName`开始，其中`extensionName`取名与包中的项目名称相似，但又不能包含`yii2-`前缀。
示例，`yiisoft/yii2-imagine`的扩展，我们使用`yii\imagine`作为该类的命名空间。


不要使用`yii`,`yii2`或`yiisoft`作为供应商名称，这些名称被保留用于Yii核心代码。


#### 引导类 <a name="bootstrapping-classes"></a>

有时，你可能希望你的应用程序在扩展过程 [引导进程](runtime-bootstrapping.md) 阶段中执行一些代码。示例，你的扩展也许在应用主体的`beginRequest`
事件调整一些环境参数。虽然你可以使用手动扩展的形式附加你的'beginRequest' 事件扩展中进行处理，但更好的方式是自动执行此操作。

为了实现这一目标，你可以创建一个所谓*bootstrapping class*通过[[yii\base\BootstrapInterface]]实现。
示例，

```php
namespace myname\mywidget;

use yii\base\BootstrapInterface;
use yii\base\Application;

class MyBootstrapClass implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->on(Application::EVENT_BEFORE_REQUEST, function () {
             // 定义操作
        });
    }
}
```

然后在'composer.json' 文件中列表列出扩展类

```json
{
    // ...

    "extra": {
        "bootstrap": "myname\\mywidget\\MyBootstrapClass"
    }
}
```


当扩展被安装到应用主体，Yii将自动实例化引导类，并在事件请求的引导进程之间
执行他的方法[[yii\base\BootstrapInterface::bootstrap()|bootstrap()]]


#### 使用数据库 <a name="working-with-databases"></a>


你的扩展可能需要访问数据库。不要想当然的以为你的扩展会通过会应用主体的`Yii::$db`始终连接数据库。相反，你应该声明一个`db`属性用于访问目标数据库的类。
该属性允许用户定制扩展连接数据库，同样也可以扩展使用。举个例子，你可以参考[[yii\caching\DbCache]]类，看看它是如何声明和使用`db`属性



如果你的扩展需要创建指定的数据库表结构或者修改数据库架构，你将要

－ 提供 [迁移](db-migrations.md) 来操作数据库架构；
－ 试着使迁移适用于不同的关系型数据库DBMS；
-  避免迁移中使用[活动记录](db-active-record.md)


#### 使用资源 <a name="using-assets"></a>


如果你的扩展是一个小部件或一个模块，那有可能它可能需要使用一些 [资源](structure-assets.md)。
示例，一个模块可能会显示一些网页，其中包含图像，JavaScript和CSS。因为文件的扩展都是相同的目录下的所有非Web访问
安装在应用主体中时，通过以下两个选择使资源文件可以直接访问：

－ 手动复制对应的资源文件到WEB访问的目标文件夹下；
－ 声明[资源包] (structure-assets.md)并依靠资源包发布机制自动复制该资源包到WEB访问的目标文件夹下


我们建议你使用第二种方法，使你的扩展可以更容易地被其他人使用。
更多的细节请参阅[资源]部分。

#### 国际化和本地化 <a name="i18n-l10n"></a>

你的扩展可以在不同语言的应用主体中使用。因此，如果你的扩展要显示内容到最终用户，你需要尝试应用[国际化和本地化](tutorial-i18n.md)。
特别指出的是

- 如果你的扩展要显示消息到最终用户，那该消息应被包装成`Yii::t()`，这样的话将会被翻译。开发者的消息(诸如内部异常消息)不需要被翻译。
- 如果扩展显示数字，日期等等信息，那应该使用[[yii\i18n\Formatter]]格式规则与之相匹配。
For more details, please refer to the [Internationalization](tutorial-i18n.md) section.
更多的细节请参阅[国际化](tutorial-i18n.md)部分。

#### 测试 <a name="testing"></a> 

如果你希望你的扩展被他人完美的使用，而不会产生任何问题。你应该把他分享给公众进行测试。


推荐你创建各种测试案例来测试你的扩展，而不是依靠手工测试。每次当你释放扩展的新版本，你可能只需要运行这些测试案例，以确保 
一切都在良好的状态。Yii提供测试支持，它可以帮助你更轻松地编写单元测试，验收测试和功能测试。
更多的细节请参阅[测试](test-overview.md)部分。

#### 版本 <a name="versioning"></a>


你应该为每次发布的扩展设置版本号(例如:`1.0.1`)。我们建议你按照[语义版本](http://semver.org)的做法确定版本号。

#### 发布 <a name="releasing"></a>


为了让其他人知道你的扩展，你需要将它发布给公众。

如果这是你第一次发布扩展，你应该对一个Composer存储库中注册，比如[Packagist](https://packagist.org/)。然后，你需要做的就是
简单地创建一个发布标签(例如`v1.0.1`)在你的扩展的VCS资源库，并通知有关新版本的Composer资料库。


在扩展的版本中，除了代码文件，你也应该考虑包括以下内容，以帮助其他人了解并使用你的扩展：


* 在包的根目录中的自述文件：它描述了你的扩展功能以及如何安装和使用它。 
	我们建议你按[标记](http://daringfireball.net/projects/markdown/)格式写入自述文件，并命名为`readme.md`。
* 在包的根目录更新日志文件：他将按标记格式列出每次的版本修改变化写入自述文件，并命名为`changelog.md`。
* 在包的根目录下的升级文件：它提供了有关如何从扩展的旧版本进行升级的说明。我们建议你按[标记]
  (http://daringfireball.net/projects/markdown/)格式写入自述文件，并命名为`upgrade.md`。
* 如果你在自述文件中无法表达更多的功能，你应该提供图文并茂的教程等。

> 补充：代码注释可以按标记格式写入。`yiisoft/yii2-apidoc`扩展提供了一个可以帮助你生成基于代码备注的API文档。

> 补充：我们建议你遵守一定的编码风格，你可以参照[内核代码风格](https://github.com/yiisoft/yii2/wiki/Core-framework-code-style).

## 内核扩展 <a name="core-extensions"></a>

Yii提供了由Yii的开发团队开发和维护的以下核心扩展。它们都被登记在[Packagist](https://packagist.org/)并可方便地安装在
[使用扩展]#using-extensions)部分


- [yiisoft/yii2-apidoc](https://github.com/yiisoft/yii2-apidoc):
  提供了一个可扩展的，高性能的API文档生成器。它也被用来生成所述核心框架的API文档。
  
- [yiisoft/yii2-authclient](https://github.com/yiisoft/yii2-authclient):
  提供了一组常用的身份验证的客户端，比如Facebook OAuth2客户端，GitHub的OAuth2客户端。

- [yiisoft/yii2-bootstrap](https://github.com/yiisoft/yii2-bootstrap):
  提供了一组封装的组件和插件的小部件[引导](http://getbootstrap.com/)。
  
- [yiisoft/yii2-codeception](https://github.com/yiisoft/yii2-codeception):
  提供了一个基于[Codeception](http://codeception.com/)的测试支持
  
- [yiisoft/yii2-debug](https://github.com/yiisoft/yii2-debug):
  提供一个Yii应用的调试支持。当该扩展被使用，调试工具条将出现在每个页面的底部。该扩展同样可以提供标准页面以显示更多更详细的调试信息。
  
- [yiisoft/yii2-elasticsearch](https://github.com/yiisoft/yii2-elasticsearch):
  提供了用于[Elasticsearch](http://www.elasticsearch.org/)使用支持。他包括了基本的检索支持，同样可以实现了活动记录[Active Record](db-active-record.md)部分
  储存到Elasticsearch。
  
- [yiisoft/yii2-faker](https://github.com/yiisoft/yii2-faker):
  提供基于[Faker](https://github.com/fzaninotto/Faker)用于生成虚拟数据。
  
- [yiisoft/yii2-gii](https://github.com/yiisoft/yii2-gii):
  提供一个基于页面的代码生成器,这是高度可扩展的，并且可以用于快速生成模型，表单，模块，CRUD等。
  
- [yiisoft/yii2-imagine](https://github.com/yiisoft/yii2-imagine):
  提供了基于[Imagine](http://imagine.readthedocs.org/)常用的图像处理功能
  
- [yiisoft/yii2-jui](https://github.com/yiisoft/yii2-jui):
  提供了一组互动封装[JQuery UI](http://jqueryui.com/)及小部件。
  
- [yiisoft/yii2-mongodb](https://github.com/yiisoft/yii2-mongodb):
  提供基于[MongoDB](http://www.mongodb.org/)的支持。它包括的功能，如基本的查询，迁移，活动记录，缓存，代码生成等。
  
- [yiisoft/yii2-redis](https://github.com/yiisoft/yii2-redis):
  提供基于[redis](http://redis.io/)的支持。它包括的功能，如基本的查询，活动记录，缓存等。
  
- [yiisoft/yii2-smarty](https://github.com/yiisoft/yii2-smarty):
  提供基于[Smarty]模板引擎(http://www.smarty.net/).
  
- [yiisoft/yii2-sphinx](https://github.com/yiisoft/yii2-sphinx):
  提供基于[Sphinx](http://sphinxsearch.com)的支持。它包括的功能，如基本的查询，活动记录，代码生成等。
  
- [yiisoft/yii2-swiftmailer](https://github.com/yiisoft/yii2-swiftmailer):
  提供基于[swiftmailer](http://swiftmailer.org/)电子邮件发送功能。
  
- [yiisoft/yii2-twig](https://github.com/yiisoft/yii2-twig):
  提供基于[Twig]模板引擎(http://twig.sensiolabs.org/).
