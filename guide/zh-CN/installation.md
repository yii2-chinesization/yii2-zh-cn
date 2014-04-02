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

For the advanded app the `requirements.php` is two levels up so you have to use `ln -s requirements.php ../../requirements.php`.
高级应用的 `requirements.php`
Yii 2 requires PHP 5.4.0 or higher. Yii has been tested with the [Apache HTTP server](http://httpd.apache.org/) and
[Nginx HTTP server](http://nginx.org/) on Windows and Linux.
Yii may also be usable on other web servers and platforms, provided that PHP 5.4 or higher is supported.


Recommended Apache Configuration
--------------------------------

Yii is ready to work with a default Apache web server configuration. As a security measure, Yii comes with `.htaccess`
files in the Yii framework folder to deny access to those restricted resources.

By default, requests for pages in a Yii-based site go through the bootstrap file, usually named `index.php`, and placed
in the application's `web` directory. The result will be URLs in the format `http://hostname/index.php/controller/action/param/value`.

To hide the bootstrap file in your URLs, add `mod_rewrite` instructions to the `.htaccess` file in your web document root
(or add the instructions to the virtual host configuration in Apache's `httpd.conf` file, `Directory` section for your webroot).
The applicable instructions are:

~~~
RewriteEngine on

# If a directory or a file exists, use it directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
# Otherwise forward it to index.php
RewriteRule . index.php
~~~


Recommended Nginx Configuration
-------------------------------

Yii can also be used with the popular [Nginx](http://wiki.nginx.org/) web server, so long it has PHP installed as
an [FPM SAPI](http://php.net/install.fpm). Below is a sample host configuration for a Yii-based site on Nginx.
The configuration tells the server to send all requests for non-existent resources through the bootstrap file,
resulting in "prettier" URLs without the need for `index.php` references.

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

When using this configuration, you should set `cgi.fix_pathinfo=0` in the `php.ini` file in order to avoid many unnecessary system `stat()` calls.


Note that when running a HTTPS server you need to add `fastcgi_param HTTPS on;` in order for Yii to properly detect if
connection is secure.
