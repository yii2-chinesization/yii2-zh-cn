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

我们强烈建议你在本地安装一个全局的 Composer。

使用 Composer
---------------------

[安装 Yii 应用](installation.md) 的操作

```
composer.phar create-project --stability dev yiisoft/yii2-app-basic
```

会创建一个你项目的根目录，并在其中创建 `composer.json` 和`compoer.lock` 文件。

前者列出了你的项目所依赖的程序包以及版本号，后者写入了已经安装的包的明确版本信息（项目就锁定了这些指定的版本号了）。因此 `composer.lock` 也应该被 [提交到版本控制系统（VCS）中](https://getcomposer.org/doc/01-basic-usage.md#composer-lock-the-lock-file)。

这两个文件跟Composer的两个命令 `update` 和 `install` 关系很密切。
通常，当你处理你自己的项目是，比如部署一个新的开发（或生产）拷份，你需要用到

```
composer.phar install
```

以确保你安装跟 `composer.lock`注明版本一样的包。

若你只想单单更新你项目中的某一些包，你应该运行

```
composer.phar update
```

举例来说，`dev-master` 里的包，会在你调用 `update` 时，始终使用最新版本的包；而在你调用 `install` 时则不会，除非你已经更新过 `composer.lock` 文件。

对于上面的命令，其实还有一些可选的参数，分别有不同的作用。最常用的就是 `--no-dev` 命令，他会跳过那些声明在 `require-dev` 处的包。还有 `--prefer-dist`，他会在有可下载的包的时候就直接下载他们的压缩包，而不去检查 `vendor` 文件夹中的那些代码仓库。

> Composer 命令必须在你的 Yii 项目的文件夹中被执行，来让 Composer 找到 `composer.json` 文件。
这取决于你的操作系统和安装方式，可能你需要设置php可执行环境变量
和 `composer.phar` 脚本。


向你的项目中添加更多包
------------------------------------

用下列命令添加两个新的包到你的项目中：

```
composer.phar require "michelf/php-markdown:>=1.3" "ezyang/htmlpurifier:>4.5.0"
```

他会处理依赖关系，并更新你的 `composer.json` 文件。
上面的例子是指，你需要 Michaelf 制作的1.3版本或以上的 PHP-Markdown package。
同理还有 Ezyang 的大于等于4.5版本的 HTMLPurifier。

关于这个语法的更多细节，请参考 [official Composer documentation](http://getcomposer.org) 或正在更新的[Composer 中文文档https://github.com/5-say/composer-doc-cn](https://github.com/5-say/composer-doc-cn)。

完整的Composer php支持软件包列表可以从[packagist](http://packagist.org/)获取。你也可以用 `composer.phar require` 命令来询问搜索包.

### 手动编辑你的版本限制

您也可以手动编辑 `composer.json` 文件。如同上面的命令一样，在 `require` 部分中，指定每个所需包的名称和版本。

```json
{
    "require": {
        "michelf/php-markdown": ">=1.4",
        "ezyang/htmlpurifier": ">=4.6.0"
    }
}
```

一旦您已编辑 `composer.json`，你就可以调用Composer来下载最新的依赖库。之后运行

```
composer.phar update michelf/php-markdown ezyang/htmlpurifier
```


> 取决于不同的包，可能会需要一些额外的配置 （比如， 您必须在配置文件中先注册模块再使用），但自动加载类的应由Composer完成。


用某一特定版本的包
------------------------------------

Yii始终会使用各个兼容良好的依赖库的最新版本，但是只要你需要，你依然可以选用其他更早的版本。

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

### 我应该向代码池中提交 Vendor 文件夹下的依赖库么?

简而言之：否，不应该。想看详细解释，[请移步](https://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).


另见
--------

- [Composer官方文档|Official Composer documentation](http://getcomposer.org).