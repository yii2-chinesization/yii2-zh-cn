基础应用模板
==========================

Yii 基础应用模板是非常适用于小型项目或框架学习。

基础应用模板包括四个页面：主页、关于页、联系页和登录页。
联系页显示了一个联系表单，用户可以填写需求并提交给站长。假设站点连接好一个邮件服务器，管理员邮箱也在配置文件中做好设置，这个联系表单将会工作。而登录页就用于用户访问受限内容前进行身份验证。


安装
------------

Yii 框架安装需要使用[Composer](http://getcomposer.org/)。如你的系统还没有 Composer ，请到
[http://getcomposer.org/](http://getcomposer.org/)下载, 或在 Linux/Unix/MacOS 运行以下命令:

~~~
curl -s http://getcomposer.org/installer | php
~~~

然后可以使用以下命令创建 Yii 基础应用：

~~~
php composer.phar create-project --prefer-dist --stability=dev yiisoft/yii2-app-basic /path/to/yii-application
~~~

现在设置你的 Web 服务器根目录到 /path/to/yii-application/web 就可以通过 `http://localhost/` 访问该应用。


目录结构
-------------------

基础应用没有很明显的分离应用目录，以下是基础应用目录结构：

- `assets` - 应用资源文件目录
  - `AppAsset.php` - 应用资源文件如 CSS 、JS 等定义文件，细节请参看[资源管理](assets.md)。
- `commands` - 控制台命令目录
- `config` - 配置文件目录
- `controllers` - web 控制器
- `models` - 模型
- `runtime` - 日志、状态、缓存文件
- `views` - 视图
- `web` - 入口目录.

根目录包括一系列文件

- `.gitignore` 包括要被 GIT 版本控制系统忽略的目录清单。有些文档不需要上传到源码库，就在该文件列明。
- `codeception.yml` - Codeception 配置
- `composer.json` - Composer 配置，细节在下面描述
- `LICENSE.md` - 版权文件，在此放你的项目许可，特别是开源项目
- `README.md` - 安装模板的基础信息，可以用你的项目及安装相关信息来替换
- `requirements.php` - Yii 必要环境检查文件
- `yii` - 控制台应用引导文件
- `yii.bat` - Windows 下的控制台应用引导文件


### 配置

该目录包括配置文件：

- `console.php` - 控制台应用配置文件
- `params.php` - 应用共享参数
- `web.php` - web 应用配置文件
- `web-test.php` - 用于运行功能测试的web 应用配置文件

以上这些文件都是返回用于配置应用相应属性的数组，更多细节请参考本指南[配置](configuration.md)部分。

### 视图

视图目录包括应用使用的视图模板，基础应用模板包括这些视图模板：

```
layouts
    main.php
site
    about.php
    contact.php
    error.php
    index.php
    login.php
```

`layouts` 包括 HTML 布局文件，除了内容的页面标记：文件类型、头信息、主菜单、 footer 等。
其他目录就是典型的控制器视图了，根据约定，控制器所属的视图文件放在该控制器目录下，如`SiteController` 视图在`site` 目录下。视图名也通常匹配相应的控制器和动作名。局部视图通常以下划线开头。

### web

该目录是 web 根目录， web 服务器通常指向该目录，从这里开始执行。

```
assets
css
index.php
index-test.php
```

`assets` 包括公开的资源文件，如 CSS 、JS 文件等。发布流程是自动的，不需要对该目录做任何事，只要确保Yii 框架对该目录有足够的写入权限即可。

`css` 显然包括的是 CSS 文件，用于无需用资源管理器压缩和合并的全局 CSS 文件。

`index.php` 是主要的 web 应用引导文件，即唯一的入口文件。 `index-test.php` 是功能测试的入口文件。

配置 Composer
--------------------


应用模板安装后，调整默认的 `composer.json` 是好的做法，该文件在根目录下：

```json
{
    "name": "yiisoft/yii2-app-basic",
    "description": "Yii 2 Basic Application Template",
    "keywords": ["yii", "framework", "basic", "application template"],
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
            "runtime",
            "web/assets"
        ],
        "executable": [
            "yii"
        ]
    }
}
```

首先升级基础信息，修改 `name`, `description`, `keywords`, `homepage` 和 `support` 以匹配你的项目。

现在是有趣的部分，在 `require` 部分添加更多你的项目需要引入的包。所有的包都来自[packagist.org](https://packagist.org/)，请到该网站自由的浏览有用的代码。

After your `composer.json` is changed you can run `php composer.phar update --prefer-dist`, wait till packages are downloaded and
installed and then just use them. Autoloading of classes will be handled automatically.
修改了 `composer.json` 后运行 `php composer.phar update --prefer-dist` 将下载包，完成后安装即可使用包了。类加载是自动处理的。