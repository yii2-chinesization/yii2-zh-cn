Composer
========

Yii2 使用 Composer 作为其依赖库管理工具。Composer 是一个 PHP 实用程序，可以自动处理所需的库和扩展的安装。
，从而使这些第三方资源时刻保持更新，你无需再手动管理项目的各种依赖项。

安装 Composer
-------------------

请参考 Composer 官方提供的对应你操作系统的安装指南来安装 Composer。

* [Linux](http://getcomposer.org/doc/00-intro.md#installation-nix)
* [Windows](http://getcomposer.org/doc/00-intro.md#installation-windows)

你可以在官方指南中找到全部的细节，这之后，你要么直接在 [http://getcomposer.org/](http://getcomposer.org/) 下载 Composer，要么运行下列 命令在下载该软件：

```
curl -s http://getcomposer.org/installer | php
```

向你的项目中添加更多包
------------------------------------

[Installing a Yii application](installation.md) 的操作会在您项目的根目录中创建 'composer.json' 文件。
你可以在这个文件中罗列所有你应用所需要的包。对于 Yii 的网站来说，`require`的部分是这个文件中最重要的部分。

```json
{
    "require": {
        "Michelf/php-markdown": ">=1.3",
        "ezyang/htmlpurifier": ">=4.6.0"
    }
}
```

在 `require` 段落里，你需要详细指定你需求的每一个包的名称以及版本。
上面的例子是指，你需要 Michaelf 制作的1.3版本或以上的 PHP-Markdown package。
同理还有 Ezyang 的大于等于4.5版本的 HTMLPurifier。
关于这个语法的更多细节，请参考 [official Composer documentation](http://getcomposer.org) 或正在更新的[Composer 中文文档https://github.com/5-say/composer-doc-cn](https://github.com/5-say/composer-doc-cn)。

完整的Composer php支持软件包列表可以从[packagist](http://packagist.org/)获取

一旦你编辑了 `composer.json` 文件，你可以调用 Composer 来安装指定的依赖。
第一次安装使用 dependencies ,请使用该命令:

```
php composer.phar install --prefer-dist
```

该命令必须在Yii项目目录下`composer.json`文件所在目录下执行
这取决于你的操作系统和安装方式，可能你需要设置php可执行环境变量
和 `composer.phar` 脚本。

对于已经存在的安装，你可以以下命令来让Composer帮你更新依赖库：

```
php composer.phar update --prefer-dist
```

一样，你需要提供他们的各自的引用路径

相同的是，只要稍等片刻，你所需要的包就会被安装完毕，可以使用到你的 Yii 应用里去了。
无需针对各自的包再进行多余的配置。


用某一特定版本的包
------------------------------------

Yii始终使用各个依赖库的最新版本，
但是如果你真的需要的话，你也可以使用一个特定的更老的版本。
关于这一点，一个很好的例子就是 jQuery，它在它的 2.0 版本中已经 [放弃对老 IE 浏览器的支持（英文）](http://jquery.com/browser-support/)。
当你需要通过 Composer 安装 Yii 时，他所安装的是最新版的 2.X 版本。当你需要使用 jQuery 的 1.10 
来支持 IE 浏览器（译者温馨地提醒您，珍爱生命，远离IE），你可以通过调整你的 composer.json 来请求一个特定版本的jQuery，比如这样：

```json
{
    "require": {
        ...
        "yiisoft/jquery": "1.10.*"
    }
}
```


FAQ
---

### 收到"You must enable the openssl extension to download files via https"错误提示

（意指你必须打开openssl扩展来通过https下载文件）如果你正在使用（WAMP），请看 [StackOverflow 上的这篇问答（英文）](http://stackoverflow.com/a/14265815/1106908)。

### 收到"Failed to clone <URL here>, git was not found, check that it is installed and in your Path env."错误提示

（意思是<URL>的git克隆失败：没找到git，你需要确认已经安装了git或者检查PATH环境）你可以通过要么安装git，要么在 `install` 或 `update` 命令的结尾添加 `--prefer-dist` 参数来解决。


另见
--------

- [Composer官方文档|Official Composer documentation](http://getcomposer.org).