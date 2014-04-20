高级应用模板
=============================

该模板适用于大型项目开发，分离前后台使应用可以分别安装到多个服务器。该应用模板还提供了一些令人喜爱的特性、必不可少的数据库、非常棒的注册和密码存储。


安装
------------

### 用 Composer 安装

Yii 应用安装需要使用[Composer](http://getcomposer.org/)。如你的系统还没有 Composer ，请到
[http://getcomposer.org/](http://getcomposer.org/)下载, 或在 Linux/Unix/MacOS 运行以下命令:

~~~
curl -s http://getcomposer.org/installer | php
~~~

然后使用以下命令创建 Yii 高级应用：

~~~
php composer.phar create-project --prefer-dist --stability=dev yiisoft/yii2-app-advanced /path/to/yii-application
~~~

开始
---------------

安装应用后，必须执行以下步骤来初始化应用，只需做一次：

1. 执行 `init` 命令并依据你的情况选择 `dev` or `prod`环境。

   ```
   php /path/to/yii-application/init
   ```
2. 创建新的数据库并在 `common/config/main-local.php` 相应地调整 `components db` 配置。
3. 以控制台命令 `yii migrate` 运行数据库合并。
4. 设置 web 服务器的文件根目录：

- 前台是 `/path/to/yii-application/frontend/web/` ，使用 `http://frontend/` 访问。
- 后台是 `/path/to/yii-application/backend/web/` ，使用 `http://backend/` 访问。

目录结构
-------------------

根目录包括以下子目录：

- `backend` - web 应用后台
- `frontend` - web 应用前台
- `common` - 所有应用共享文件
- `console` - 控制台应用
- `environments` - 环境配置


根目录还包括以下文件：

- `.gitignore` 包括要被 GIT 版本控制系统忽略的目录清单。有些文档不需要上传到源码库，就在该文件列明。
- `composer.json` - 细节在下面描述的 Composer 配置
- `init` - 初始化脚本，并描述在 "细节在下面描述的 Composer 配置"
- `init.bat` - Windows 下的初始化脚本
- `LICENSE.md` - 版权文件，在此放你的项目许可，特别是开源项目
- `README.md` - 安装模板的基础信息，可以用你的项目及安装相关信息来替换
- `requirements.php` - Yii 必要环境检查文件
- `yii` - 控制台应用引导文件
- `yii.bat` - Windows 下的控制台应用引导文件

应用
------------

高级模板有三个应用：前台、后台和控制台。前台通常面向终端用户，项目自身。后台是管理平台，诸如数据分析等功能。控制台通常用于守护程序和底层服务器管理，也用于应用部署、合并及资源处理。

还有个 `common` 目录，包括的文件在不止一个应用中使用。如，`User` 模型。

前台和后台都是 web 应用，都包括 `web` 目录，该目录是设置服务器指向的根目录。

每个应用有其自己的命名空间和对应的路径别名，也适用于通用目录。

配置和环境
--------------

直接配置应用会产生很多问题：

- 每个应用成员都有自己的配置选项，直接配置会影响其他应用成员.
- 生产环境的数据库密码和 API keys 不应该出现在版本库里。
- 考虑有很多服务器：开发服务器、测试服务器、发布服务器。每个都应该有其单独的配置。
- 为每个状况定义所有的配置选项是重复的，也需要太多时间维护。

Yii 使用环境概念解决以上问题就非常简单。每个环境用`environments` 目录下的一系列文件表示。用 `init` 命令来变换环境，它所做的其实是复制环境目录的所有文件来替换所有应用所在根目录文件。

通常环境包括应用的引导文件如 `index.php` 和后缀名为`-local.php` 的配置文件。这些要添加到 `.gitignore` ，不要提交到源码库。

为避免重复，配置可相互覆写。如，前台按以下顺序读取配置：

- `common/config/main.php`
- `common/config/main-local.php`
- `frontend/config/main.php`
- `frontend/config/main-local.php`

参数按以下顺序读取配置：

- `common/config/params.php`
- `common/config/params-local.php`
- `frontend/config/params.php`
- `frontend/config/params-local.php`


后面的配置文件会覆写前面的配置文件。

以下是完整配置顺序：

![高级应用配置](images/advanced-app-configs.png)

配置 Composer
--------------------

应用模板安装后，调整默认的 `composer.json` 是好的做法，该文件在根目录下：

```json
{
    "name": "yiisoft/yii2-app-advanced",
    "description": "Yii 2 Advanced Application Template",
    "keywords": ["yii", "framework", "advanced", "application template"],
    "homepage": "http://www.yiiframework.com/",
    "type": "project",
    "license": "BSD-3-Clause",
    "support": {
        "issues": "https://github.com/yiisoft/yii2/issues?state=open",
        "forum": "http://www.yiiframework.com/forum/",
        "wiki": "http://www.yiiframework.com/wiki/",
        "irc": "irc://irc.freenode.net/yii",
        "source": "https://github.com/yiisoft/yii2"
    },
    "minimum-stability": "dev",
    "require": {
        "php": ">=5.4.0",
        "yiisoft/yii2": "*",
        "yiisoft/yii2-swiftmailer": "*",
        "yiisoft/yii2-bootstrap": "*",
        "yiisoft/yii2-debug": "*",
        "yiisoft/yii2-gii": "*"
    },
    "scripts": {
        "post-create-project-cmd": [
            "yii\\composer\\Installer::setPermission"
        ]
    },
    "extra": {
        "writable": [
            "backend/runtime",
            "backend/web/assets",

            "console/runtime",
            "console/migrations",

            "frontend/runtime",
            "frontend/web/assets"
        ]
    }
}
```

首先升级基础信息，修改 `name`, `description`, `keywords`, `homepage` 和 `support` 以匹配你的项目。

现在是有趣的部分，在 `require` 部分添加更多你的项目需要引入的包。所有的包都来自[packagist.org](https://packagist.org/)，请到该网站自由的浏览有用的代码。

修改了 `composer.json` 后运行 `php composer.phar update --prefer-dist` 将下载包，完成后安装即可使用包了。类会自动加载。

创建后台到前台的链接
------------------------

经常需要创建后台应用到前台应用的链接。前台应用已经有其单独的 URL 管理器规则，你需要给后台应用复制 URL 管理器并重新命名以区分：

```php
return [
    'components' => [
        'urlManager' => [
            // 这是后台 URL 管理器配置
        ],
        'urlManagerFrontend' => [
            // 这是前台 URL 管理器配置
        ],

    ],
];
```

配置完成就可以使用以下代码获得指向前台的 URL：

```php
echo Yii::$app->urlManagerFrontend->createUrl(...);
```
