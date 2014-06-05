安装
============

两种方式安装 Yii 框架：
* 用[Composer](http://getcomposer.org/) (推荐)
* 下载包括所有站点需要文件和 Yii 框架文件的应用模板

用Composer安装
-----------------------

安装 Yii 的推荐方式是使用 Composer 包管理器。如果你还没安装 Composer，请先从 [http://getcomposer.org/](http://getcomposer.org/) 下载后运行以下命令安装：
```
curl -s http://getcomposer.org/installer | php
```

强烈建议 composer 安装后全局均可访问。

遇到问题或需要更多信息，请参考 composer 官方指南：
* [Linux](http://getcomposer.org/doc/00-intro.md#installation-nix)
* [Windows](http://getcomposer.org/doc/00-intro.md#installation-windows)

composer 安装完毕，就可以下载可用的 Yii 应用模板
来创建新的 Yii web 应用。根据你的需要选择合适的模板来启动你的项目。

当前有两个可用的应用模板：

- [基础应用模板](https://github.com/yiisoft/yii2-app-basic) - 只是一个只有前台的基础应用。
- [高级应用模板](https://github.com/yiisoft/yii2-app-advanced) - 由前台、后台、控制台、通用（共享代码）和支持环境组成。

安装指引请参考以上链接，要了解更多这些应用模板背后的想法和恰当的用法，请参考[基础应用模板](apps-basic.md) and [高级应用模板](apps-advanced.md) 文档。

如你不想使用模板，要从头开始，可以在[创建自己的应用结构](apps-own.md)文档了解更多信息，只推荐高级用户使用。

用压缩包安装
-------------------

用下载的压缩文件安装包括两个步骤：
   1. 从[yiiframework.com](http://www.yiiframework.com/download/)下载应用模板。
   2. 解压下载文件。

如果只需要 Yii 框架文件，可以从[github](https://github.com/yiisoft/yii2-framework/releases)下载压缩包。从头创建应用要遵循这些描述在[创建自己的应用结构](apps-own.md)的步骤。仅推荐高级用户使用。
> 提示：Yii 框架本身并不需要安装在 web 可访问的目录。
 Yii 应用只有一个入口脚本，通常是必须暴露给 web 用户的唯一文件(如放在web 目录内)。其他 PHP 脚本，包括 Yii 框架，应保护起来不能被 web 访问，防止潜在的黑客攻击。


必要条件
------------

安装 Yii 后，需要验证服务器是否满足 Yii 的要求。可以在浏览器或命令行运行检查脚本验证。

通过压缩包或 composer 安装好 Yii 应用模板后，在应用根目录将发现`requirements.php` 文件。

使用以下命令在命令行运行此脚本：
```
php requirements.php
```

浏览器运行该脚本须确保该脚本可被 web 服务器访问，然后访问`http://hostname/path/to/yii-app/requirements.php` 。
Linux环境可创建一个软连接使其能访问，使用以下命令创建软连接：

```
ln -s requirements.php ../requirements.php
```

高级应用的 `requirements.php` 我就在根目录的下两级，所以需要使用 `ln -s requirements.php ../../requirements.php` 来创建软连接。
Yii 2 要求 PHP 5.4.0或更高版本. Yii 在Windows 和 Linux下均通过了[Apache HTTP server](http://httpd.apache.org/)和[Nginx HTTP server](http://nginx.org/)的使用测试。
Yii 在其他提供PHP 5.4以上版本的 web 服务器和平台也可以使用。


推荐的 Apache 配置
--------------------------------

Yii 能在默认的 Apache web 服务器配置下运行。Yii 在 框架文件夹使用 `.htaccess`作为安全措施，拒绝受限来源访问。

Yii 应用的所有页面请求默认必须经过引导文件，通常命名为`index.php`，放置在应用的`web` 目录。返回的 URL 格式是`http://hostname/index.php/controller/action/param/value`。

要在 URL 上隐藏引导文件，添加 `mod_rewrite` 指令到 `.htaccess` 文件（或添加指令到虚拟主机配置文件 `httpd.conf` 你的网站目录部分）。
可应用的指令是：

~~~
RewriteEngine on

# If a directory or a file exists, use it directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
# Otherwise forward it to index.php
RewriteRule . index.php
~~~


推荐的 Nginx 配置
-------------------------------

Yii 同样能用于流行的 [Nginx](http://wiki.nginx.org/) web 服务器, 只要PHP安装为 [FPM SAPI](http://php.net/install.fpm)。以下是  Nginx 的 Yii 应用配置主机的示例，配置要求服务器发送所有资源不存在的请求到引导文件，返回 URL 不包含 `index.php` 。

```
server {
    set $yii_bootstrap "index.php";
    charset utf-8;
    client_max_body_size 128M;

    listen 80; ## listen for ipv4
    #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

    server_name mysite.local;
    root        /path/to/project/web;
    index       $yii_bootstrap;

    access_log  /path/to/project/log/access.log  main;
    error_log   /path/to/project/log/error.log;

    location / {
        # Redirect everything that isn't real file to yii bootstrap file including arguments.
        try_files $uri $uri/ /$yii_bootstrap?$args;
    }

    # uncomment to avoid processing of calls to unexisting static files by yii
    #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
    #    try_files $uri =404;
    #}
    #error_page 404 /404.html;

    location ~ \.php$ {
        include fastcgi.conf;
        fastcgi_pass   127.0.0.1:9000;
        #fastcgi_pass unix:/var/run/php5-fpm.sock;
    }

    location ~ /\.(ht|svn|git) {
        deny all;
    }
}
```

要使用这个配置，需要设置 `php.ini` 文件中的 `cgi.fix_pathinfo=0` ，以避免系统很多不必要的`stat()` 调用。

注意，运行 HTTPS 服务器需要添加`fastcgi_param HTTPS on;` 以便 Yii 正确地侦查连接是否安全。