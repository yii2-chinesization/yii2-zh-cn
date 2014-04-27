资源管理
===============

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

In the above keys are `properties` of `AssetController`. `bundles` list contains bundles that should be compressed. These are typically what's used by application.
`targets` contains a list of bundles that define how resulting files will be written. In our case we're writing
everything to `path/to/web` that can be accessed like `http://example.com/` i.e. it is website root directory.
以上数值键是 `AssetController` 的 `properties` 。该资源控制器的属性之一 `bundles`  列示所包括的拟压缩资源包。

> Note: in the console environment some path aliases like '@webroot' and '@web' may not exist,
  so corresponding paths inside the configuration should be specified directly.

JavaScript files are combined, compressed and written to `js/all-{ts}.js` where {ts} is replaced with current UNIX
timestamp.

`jsCompressor` and `cssCompressor` are console commands or PHP callbacks, which should perform JavaScript and CSS files
compression correspondingly. You should adjust these values according to your environment.
By default Yii relies on [Closure Compiler](https://developers.google.com/closure/compiler/) for JavaScript file compression,
and on [YUI Compressor](https://github.com/yui/yuicompressor/). You should install this utilities manually, if you wish to use them.

### Providing compression tools

The command relies on external compression tools that are not bundled with Yii so you need to provide CSS and JS
compressors which are correspondingly specified via `cssCompressor` and `jsCompression` properties. If compressor is
specified as a string it is treated as a shell command template which should contain two placeholders: `{from}` that
is replaced by source file name and `{to}` that is replaced by output file name. Another way to specify compressor is
to use any valid PHP callback.

By default for JavaScript compression Yii tries to use
[Google Closure compiler](https://developers.google.com/closure/compiler/) that is expected to be in a file named
`compiler.jar`.

For CSS compression Yii assumes that [YUI Compressor](https://github.com/yui/yuicompressor/) is looked up in a file
named `yuicompressor.jar`.

In order to compress both JavaScript and CSS, you need to download both tools and place them under the directory
containing your `yii` console bootstrap file. You also need to install JRE in order to run these tools.

You may customize the compression commands (e.g. changing the location of the jar files) in the `config.php` file
like the following,

```php
return [
       'cssCompressor' => 'java -jar path.to.file\yuicompressor.jar  --type css {from} -o {to}',
       'jsCompressor' => 'java -jar path.to.file\compiler.jar --js {from} --js_output_file {to}',
];
```

where `{from}` and `{to}` are tokens that will be replaced with the actual source and target file paths, respectively,
when the `asset` command is compressing every file.


### Performing compression

After configuration is adjusted you can run the `compress` action, using created config:

```
yii asset /path/to/myapp/config.php /path/to/myapp/config/assets_compressed.php
```

Now processing takes some time and finally finished. You need to adjust your web application config to use compressed
assets file like the following:

```php
'components' => [
    // ...
    'assetManager' => [
        'bundles' => require '/path/to/myapp/config/assets_compressed.php',
    ],
],
```

Using asset converter
---------------------

Instead of using CSS and JavaScript directly often developers are using their improved versions such as LESS or SCSS
for CSS or Microsoft TypeScript for JavaScript. Using these with Yii is easy.

First of all, corresponding compression tools should be installed and should be available from where `yii` console
bootstrap file is. The following lists file extensions and their corresponding conversion tool names that Yii converter
recognizes:

- LESS: `less` - `lessc`
- SCSS: `scss`, `sass` - `sass`
- Stylus: `styl` - `stylus`
- CoffeeScript: `coffee` - `coffee`
- TypeScript: `ts` - `tsc`

So if the corresponding tool is installed you can specify any of these in asset bundle:

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

In order to adjust conversion tool call parameters or add new ones you can use application config:

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

In the above we've left two types of extra file extensions. First one is `less` that can be specified in `css` part
of an asset bundle. Conversion is performed via running `lessc {from} {to} --no-color` where `{from}` is replaced with
LESS file path while `{to}` is replaced with target CSS file path. Second one is `ts` that can be specified in `js` part
of an asset bundle. The command that is run during conversion is in the same format that is used for `less`.
