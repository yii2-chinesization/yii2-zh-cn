 Yii 扩展
=============

Yii 框架设计得易于扩展。新增特性可以添加到你的项目，然后给你自己复用于其他项目或作为正式的 Yii 扩展分享给其他人。

代码风格
----------

为和 Yii 核心代码的约定保持一致，你的扩展应当遵循特定的代码风格：

- 使用[框架核心的代码风格](https://github.com/yiisoft/yii2/wiki/Core-framework-code-style).
- 使用[phpdoc](http://www.phpdoc.org/)记录类、方法和属性。
- 扩展类 *不要* 使用前缀。不要使用 `TbNavBar`, `EMyWidget` 这样的格式。

> 注意从文档输出考虑可以在代码中使用 Markdown 。用 Markdown 可使用这样的语法 `[[name()]]`, `[[namespace\MyClass::name()]]`链接到属性和方法。

### 命名空间

Yii 2 依赖命名空间来组织代码（PHP 5.3 以上版本支持命名空间）。如果你要在你的扩展使用命名空间：

- 命名空间的任何地方都不要使用 `yiisoft`。
- 不要使用 `\yii`, `\yii2` 或 `\yiisoft` 作为根命名空间。
- 命名空间应使用这样的语法： `vendorName\uniqueName` 。

选定唯一命名空间对避免命名冲突是非常重要的，也会使类自动加载更快。唯一和一致的命名例子是：

- `samdark\wiki`
- `samdark\debugger`
- `samdark\googlemap`

发布扩展
------------

除了代码本身，整个扩展的发布也应当有这些特定的东西。

扩展应该有一个英文版的 `readme.md` 文件，该文件应清楚描述扩展能做什么、环境要求、如何安装和使用。 README 应使用 Markdown 写作。如果想提供 README 文件的翻译版本，以 `readme_ru.md` 这样的格式命名，其中 `ru` 是你要翻译的目标语言（在这个例子中是 Russian 俄国）。
  
包括一些屏幕截图作为文档的部分是个好主意，特别是你的扩展作为小部件发布。

推荐在[Github](https://github.com)托管你的扩展。

扩展也应在[Packagist](https://packagist.org)注册以便能够通过 Composer 安装。

### Composer 包命名

应明智地选择你的扩展包命名，因为你不应该以后再更改包名（更改包名会导致失去 Composer 统计数据，使别人无法通过旧名安装这个包）。

如果扩展是特别为 Yii2 制作的（如，不能用作单独的 PHP 库），推荐命名如下：

```
yii2-my-extension-name-type
```

其中：

- `yii2-` 是前缀。
- 扩展名以`-` 分隔单词并尽量简短。
-  `-type` 后缀可以是 `widget`, `behavior`, `module` 等，根据你的扩展功能确定后缀类型。

### 依赖关系

你开发的一些扩展可能有其依赖关系，如依赖其他扩展或第三方库。当依赖关系存在，需要在你的扩展的 `composer.json` 文件导入（require）依赖关系。肯定也会使用相应版本的约束条件，如`1.*`, `@stable` 等要求。

最后，当你的扩展以文档版本发布时，必须再次确认必要环境没有导入不包含 `stable` 版本的 `dev` 包。换言之，扩展发布稳定版本只应依靠稳定的依赖关系。

### 版本管理

当你维护和升级扩展时：

- 使用[语义明确的版本管理](http://semver.org)规则。
- 使用格式一致的版本库标记，因为 composer 把标记看作为版本的字符串，如 `0.2.4`, `0.2.5`,`0.3.0`,`1.0.0` 。

### composer.json

Yii2 使用 Composer 来安装 Yii2 和管理 Yii2 的扩展。为实现这一目标：

- 如果你的扩展是为 Yii2 定制的，请在 `composer.json` 文件使用 `yii2-extension` 类型。
- 不要使用 `yii` 或 `yii2` 作为 Composer vendor 名。
- 在 Composer 包名或 Composer vendor 名都不要使用 `yiisoft` 。

如果扩展类直接放在版本库根目录内，可以在你的 `composer.json` 文件以以下方式使用 PSR-4 自动加载器：

```json
{
    "name": "myname/mywidget",
    "description": "My widget is a cool widget that does everything",
    "keywords": ["yii", "extension", "widget", "cool"],
    "homepage": "https://github.com/myname/yii2-mywidget-widget",
    "type": "yii2-extension",
    "license": "BSD-3-Clause",
    "authors": [
        {
            "name": "John Doe",
            "email": "doe@example.com"
        }
    ],
    "require": {
        "yiisoft/yii2": "*"
    },
    "autoload": {
        "psr-4": {
            "myname\\mywidget\\": ""
        }
    }
}
```

以上代码中， `myname/mywidget` 是包名，将注册到[Packagist](https://packagist.org)。通常包名和Github 的版本名是一致的。同样， `psr-4` 自动加载器会映射 `myname\mywidget` 命名空间到这些类所处的根目录。

更多该语法的细节内容请参考[Composer 文档](http://getcomposer.org/doc/04-schema.md#autoload).

### 引导扩展

有时，希望扩展在应用的引导阶段执行一些代码。如，扩展要响应应用的`beginRequest` 事件，可以要求扩展使用者显性附加扩展的事件处理器到应用事件。当然更好的方式是自动完成这些事。为实现该目标，可以通过实现[[yii\base\BootstrapInterface]]接口来创建一个引导类。

```php
namespace myname\mywidget;

use yii\base\BootstrapInterface;
use yii\base\Application;

class MyBootstrapClass implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->on(Application::EVENT_BEFORE_REQUEST, function () {
             // 这里处理一些事情
        });
    }
}
```

然后把这个 bootstrap 类列入 `composer.json` ：

```json
{
    "extra": {
        "bootstrap": "myname\\mywidget\\MyBootstrapClass"
    }
}
```

当扩展在应用中安装后，Yii 将自动挂钩（hook up）这个引导类并在为每个请求初始化应用时调用其 `bootstrap()` 方法。

使用数据库
---------------------

扩展有时必须使用它们自己的数据库表，这种情况：

- 如果扩展建立或更改了数据库模式，应该一直使用 Yii 数据库迁移而不是 SQL 文件或定制脚本。
- 数据库迁移应应用于不同的数据库系统。
- 不要在数据库迁移中使用 Active Record 模型。


资源
------

- 注册资源[通过包](assets.md).

事件
------

TBD

国际化
----

- 如果扩展输出信息用于终端用户，它们应使用 `Yii::t()` 包裹以便翻译。
- 异常和其他面向开发者的信息不需要翻译。
- 考虑为 `yii message` 命令提供 `config.php` 以简化翻译。

测试扩展
----------------------

- 为 PHPUnit 添加单元测试。
