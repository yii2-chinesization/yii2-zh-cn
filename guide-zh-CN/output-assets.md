资源管理
===============

> 注意：该章节还在开发中。

YII 的资源是一个要引入页面的文件，可以是 CSS、JavaScript 或任何其它文件。 Yii 框架提供了许多使用资源的方法，从基础的如给文件添加 `<script src="...">` 标签（描述在[视图](view.md)），到高级的如发布不在服务器文件根目录下的文件、解决 JavaScript 依赖关系和 CSS 压缩，这些将在下文描述。


声明资源包
-----------------------

资源文件可以放在服务器可访问目录也可以隐藏在应用或 vendor 目录内。如果是后者，资源包喜欢发布自身到服务器可访问目录以便被网站引入。这个功能对扩展很有用，扩展可以在一个目录装载所有内容，让安装更容易。

要定义一个资源需要创建一个继承自[[yii\web\AssetBundle]]的类并根据需求设置属性。以下是资源定义示例，资源定义是基础应用模板的一部分，即`AppAsset` 资源包类，它定义了应用必需资源：

```php
<?php

use yii\web\AssetBundle as AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
```

以上 `$basePath` 指定资源从哪个可网络访问的目录提供服务。这是相对`$css` 和 `$js` 路径的根目录，如 `@webroot/css/site.css` 指向 `css/site.css` 。这里的 `@webroot` 是指向应用 `web` 目录的别名。

`$baseUrl` 用来指定刚才的 `$css` 和 `$js` 相对的根 URL ，如 `@web/css/site.css` 中的 `@web` 是一个 [别名]，对应你的网站根 URL 如 `http://example.com/` 。

如果你的资源文件放在网络无法访问的目录，Yii 扩展正是如此，这样你必须指定 `$sourcePath` 而不是 `$basePath` and `$baseUrl` 。原始路径的**所有文件**在注册前将被复制或符号链接（symlink）到你应用的 `web/assets` 目录。这种情况下 `$basePath` 和 `$baseUrl` 将在发布资源包时自动生成。这是发布完整目录的资源工作方式，目录内可以包括图片、前端文件等。

> **注意：** 不要使用d `web/assets` 目录放你自己的文件。它只用于资源发布。
> 当你创建网络可访问目录内的文件时，把它们放在类似 `web/css` 或 `web/js` 的文件夹内。

和其他资源包的依赖关系用 `$depends` 属性指定。这是个包括资源包完整合格类名的数组，资源包内的这些类应发布以便该资源包能正常工作。此例中， `AppAsset` 的Javascript 和 CSS 文件添加到 header 的[[yii\web\YiiAsset]]和[[yii\bootstrap\BootstrapAsset]]之后。

这里的[[yii\web\YiiAsset]]添加 Yii 的 JavaScript库，而[[yii\bootstrap\BootstrapAsset]]包括[Bootstrap](http://getbootstrap.com)前端框架。

资源包是常规类，所以如需定义更多资源包，以唯一名创建同样的类即可。新建的类可以放到任何地方，但惯例是放到应用的 `assets` 目录。

此外，可以在注册和发布资源时指定 `$jsOptions`, `$cssOptions` 和 `$publishOptions` 参数分别传递到[[yii\web\View::registerJsFile()]], [[yii\web\View::registerCssFile()]] 和 [[yii\web\AssetManager::publish()]]。

[别名]( basics.md#path-aliases) "Yii 的路径别名"


### 特定语言的资源包

如需定义包括基于语言的 JavaScript 文件的资源包，需要这样写：

```php
class LanguageAsset extends AssetBundle
{
    public $language;
    public $sourcePath = '@app/assets/language';
    public $js = [
    ];

    public function registerAssetFiles($view)
    {
        $language = $this->language ? $this->language : Yii::$app->language;
        $this->js[] = 'language-' . $language . '.js';
        parent::registerAssetFiles($view);
    }
}
```

当注册资源包到视图时使用以下代码设置语言：

```php
LanguageAsset::register($this)->language = $language;
```


注册资源包
------------------------

资源包类通常要注册到视图文件或[小部件](view.md#widgets)，以 css 和 javascript 文件来提供功能。
特例是以上定义的 `AppAsset` 类， `AppAsset` 类添加到应用的主布局文件并注册到该应用的任何页面。注册资源包简单到调用[[yii\web\AssetBundle::register()|register()]]方法即可实现：

```php
use app\assets\AppAsset;
AppAsset::register($this);
```

现在视图这个语境中， `$this` 就指向 `View` 类。注册资源到小部件视图，视图实例用 `$this->view` ：

```php
AppAsset::register($this->view);
```

> 注意：如需修改第三方资源包，推荐基于第三方资源包建立你自己的资源包并使用 CSS 和 JavaScript 的功能来修改行为（behaviors），不要直接修改或覆盖原文件。


覆写资源包
------------------------

有时需要覆写整个应用范围内的某些资源包。这种情况的例子有从 CDN 而不是你自己的服务器加载 jQuery 。配置 `assetManager` 应用组件来实现，基础应用模板是在 `config/web.php` 配置：

```php
return [
    // ...
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                     'sourcePath' => null,
                     'js' => ['//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js']
                ],
            ],
        ],
    ],
];
```

以上添加了资源包定义到[[yii\web\AssetManager::bundles|bundles]]资源管理器属性，数组键是拟覆写的资源包类的合格完整类名，而数组值是拟设置的类属性及对应值数组。

 `sourcePath` 设置为 `null` 是告诉资源管理器在 `js` 以 CDN 链接来覆写本地文件时不要复制。

启动符号链接（symlinks）
----------------------

资源管理器能使用符号链接，不用复制文件。符号链接默认是关闭的，因为它在虚拟主机通常无法使用。如果你的主机环境支持符号链接，就肯定能通过应用配置启用这个功能：

```php
return [
    // ...
    'components' => [
        'assetManager' => [
            'linkAssets' => true,
        ],
    ],
];
```

启用符号链接有两个好处，第一是无须复制所以更快，第二是资源会链接源文件保持最新。

压缩和合并资源
--------------------------------

要改进应用性能可以压缩和合并多个 CSS 或 JS 文件到更少的文件以便减少 HTTP 请求次数和页面加载所需下载量。Yii 提供了一个控制台命令使你能一次完成压缩和合并。

### 准备配置

要使用 `asset` 命令需先准备配置文件，可使用以下命令生成内置模板的配置文件：

```
yii asset/template /path/to/myapp/config.php
```

模板如下：

```php
<?php
/**
 * "yii asset" 控制台命令的配置文件
 * 注意控制台环境下有些路径别名可能不存在，如 '@webroot' 和 '@web'
 * 请先定义找不到的路径别名
 */
return [
    // 为 JavaScript 文件压缩调整 command/callback 命令：
    'jsCompressor' => 'java -jar compiler.jar --js {from} --js_output_file {to}',
    // 为 CSS 文件压缩调整 command/callback 命令：
    'cssCompressor' => 'java -jar yuicompressor.jar --type css {from} -o {to}',
    // 要压缩的资源包列表：
    'bundles' => [
        // 'yii\web\YiiAsset',
        // 'yii\web\JqueryAsset',
    ],
    // 输出的已压缩资源包：
    'targets' => [
        'app\config\AllAsset' => [
            'basePath' => 'path/to/web',
            'baseUrl' => '',
            'js' => 'js/all-{ts}.js',
            'css' => 'css/all-{ts}.css',
        ],
    ],
    // 资源管理器配置：
    'assetManager' => [
        'basePath' => __DIR__,
        'baseUrl' => '',
    ],
];
```

以上数值键是 `AssetController` 的 `properties` 。该资源控制器的属性之一 `bundles`  列表包括拟压缩资源包，通常被应用程序使用。`targets` 包括定义文件编写方式的输出资源包列表。我们的例子中编写所有文件到 `path/to/web` ，以 `http://example.com/` 来访问，这是个网站根目录。

> 注意：控制台环境有些路径别名不存在，如 '@webroot' 和 '@web' ，所以在配置文件中的相应路径要直接指定。


JavaScript 文件将压缩合并写入 `js/all-{ts}.js` ，其中 {ts} 将替换为当前的 UNIX 时间戳。

`jsCompressor` 和 `cssCompressor` 是控制台命令或 PHP 回调函数，它们分别执行 JavaScript 和 CSS 文件压缩。你可以根据你的环境调整这些值。默认情况下，Yii 依靠[Closure Compiler](https://developers.google.com/closure/compiler/)来压缩 JavaScript 文件，依赖[YUI Compressor](https://github.com/yui/yuicompressor/)压缩 CSS 文件。如果你想使用它们请手动安装这些实用程序。


### 提供压缩工具

命令依靠未绑定到Yii 的外部压缩工具，所以你需要指定 `cssCompressor` 和 `jsCompression` 属性来分别提供 CSS 和 JS 的压缩工具。如果压缩工具指定为字符串将视为 shell 命令模板，该模板包括两个占位符： `{from}` 将用源文件名替换，而 `{to}` 将用输出的文件名替换。另一个指定压缩工具的方法是使用有效的 PHP 回调函数。

Yii 压缩 JavaScript 默认使用名为 `compiler.jar` 的[Google Closure compiler](https://developers.google.com/closure/compiler/) 压缩工具。

Yii 压缩 CSS 使用名为 `yuicompressor.jar` 的[YUI Compressor](https://github.com/yui/yuicompressor/)压缩工具。

要同时压缩 JavaScript 和 CSS ，需要下载以上两个工具并放在和 `yii` 控制台引导文件同一个目录下，并须安装 JRE 来运行这些工具。

要自定义压缩命令（如更改 jar 文件位置），请在 `config.php` 中如下设置：

```php
return [
       'cssCompressor' => 'java -jar path.to.file\yuicompressor.jar  --type css {from} -o {to}',
       'jsCompressor' => 'java -jar path.to.file\compiler.jar --js {from} --js_output_file {to}',
];
```

其中 `{from}` 和 `{to}`  `asset` 是占位符，将在命令压缩文件时分别被真实的源文件路径和目标文件路径替换。


### 执行压缩

配置调整完成后可以运行 `compress` 动作，使用已创建的配置：

```
yii asset /path/to/myapp/config.php /path/to/myapp/config/assets_compressed.php
```

现在进程将占用一点时间并最终完成。你需要调整你的 web 应用配置来使用已压缩的资源文件，如下：

```php
'components' => [
    // ...
    'assetManager' => [
        'bundles' => require '/path/to/myapp/config/assets_compressed.php',
    ],
],
```

使用资源转换器
---------------------

通常开发人员不直接使用 CSS 和 JavaScript 而是使用改进版本如 CSS 的 LESS 或 SCSS 和 JavaScript 的微软出品 TypeScript 。在 Yii 中使用它们是非常简单的。

首先，相应的压缩工具已经安装在 `yii` 控制台引导程序同目录下且可用。以下列示了文件扩展和相应的 Yii 转换器能识别的转换工具名。

- LESS: `less` - `lessc`
- SCSS: `scss`, `sass` - `sass`
- Stylus: `styl` - `stylus`
- CoffeeScript: `coffee` - `coffee`
- TypeScript: `ts` - `tsc`

如果相应的工具已安装，就可以在资源包指定它们：

```php
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.less',
    ];
    public $js = [
        'js/site.ts',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
```

要调整转换工具调用参数或添加新的调用参数，可以使用应用配置：

```php
// ...
'components' => [
    'assetManager' => [
        'converter' => [
            'class' => 'yii\web\AssetConverter',
            'commands' => [
                'less' => ['css', 'lessc {from} {to} --no-color'],
                'ts' => ['js', 'tsc --out {to} {from}'],
            ],
        ],
    ],
],
```

以上列示了两种外部文件扩展，第一个是 `less` ，指定在资源包的 `css` 部分。转换通过运行 `lessc {from} {to} --no-color` 来执行，其中`{from}` 以 LESS 文件路径替换而 `{to}` 用目标 CSS 文件路径替换。第二个文件扩展是 `ts` ，指定在资源包的 `js` 部分。这个命令在转换时运行，格式同 `less`。