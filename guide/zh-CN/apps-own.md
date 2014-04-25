创建自己的应用结构
=======================================

基础[basic](apps-basic.md)与 [advanced](apps-advanced.md)应用模版已经可以满足你的大部分需要了，你也许会想要创建你自己的应用模板来开始你的项目。
While [basic](apps-basic.md) and [advanced](apps-advanced.md) application templates are great for most of your needs
you may want to create your own application template to start your projects with.

Application templates are repositories containing `composer.json` and registered as Composer packages so you can make
any repository a package and it will be installable via `create-project` command.

Since it's a bit too much to start building your template from scratch it is better to use one of built-in templates
as a base. Let's use basic template.

用 git 克隆基础应用模版
----------------------------------------

```
git clone git@github.com:yiisoft/yii2-app-basic.git
```

然后等着它下载好。既然我们不需要把我们的修改推送回 Yii 官方仓库，我们可以删除掉 `.git` 文件夹，和里面所有的文件。

修改文件
------------

现在，我们需要修改 `composer.json`，改变 `name`，`description`，`keywords`，`homepage`，`license`，`support` 这些部分，以匹配你的新模板。同时，调整 `require`，`require-dev`，`suggest`以及其它各项选项。

> **注意**：在 `composer.json` 文件里，在 `extra` 下有一个 `writable` 项，他是 Yii 团队
自主添加的一个功能，它可以允许你指定，在用你的模版安装完应用之后，它的各个文件的文件访问权限应该被设置为什么。

接下来，你就可以实际上手，随心所欲地修改你应用的结构啦，别忘了相应地修改 readme 必读文件哦。


制作包
--------------

创建一个git仓库,并且把你的文件推送上去。若你想要让他开源，GitHub是目前托管它最好的选择。
若它应该保持私有状态，那很多 git 仓库都可以帮你。（译者注：闭源仓库首推BitBucket，国内也有一些其他选择也不错）

然后，你需要去注册你的包，公开的包需要在[packagist](https://packagist.org/)注册。

若仓库是私有的则比较难办，但是详细的注册方式在[Composer 文档](https://getcomposer.org/doc/05-repositories.md#hosting-your-own)有很好的解释。（英文文档，中文的在[这里](https://github.com/5-say/composer-doc-cn/blob/master/cn-introduction/05-repositories.md#Hosting-your-own)，有兴趣的同学可以参与到该项目的翻译中来）

使用它
------

像这样，现在，你可以使用模板创建你的项目

```
php composer.phar create-project --prefer-dist --stability=dev mysoft/yii2-app-coolone new-project
```
