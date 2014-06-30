创建自己的应用结构
=======================================

> 注意：该章节还在开发中。

尽管 [基础 App](apps-basic.md) 和 [高级 App](apps-advanced.md) 应用模板能满足你绝大多数的需求，你仍然想从建立自己的应用模板来开始项目开发。

应用模板是包括 `composer.json` 配置文件并注册为 Composer 包的版本库，这使得它成了包，可用`create-project` 命令安装。

Since it's a bit too much to start building your template from scratch it is better to use one of built-in templates
as a base. Let's use basic template.
既然它有点过大，那么最好是使用内置模板之一作为基础来从头建立你自己的模板。让我们使用基础模板。

从 git 克隆基础模版版本库
----------------------------------------

```
git clone git@github.com:yiisoft/yii2-app-basic.git
```

等待下载完成。既然我们不需要推送我们的修改回 Yii 官方仓库，可以删除 `.git` 及其所有内容。

修改文件
------------

现在，我们需要修改 `composer.json` ，改变 `name`，`description`，`keywords`，`homepage`，`license`，`support` 以匹配你的新模板。调整 `require`，`require-dev`，`suggest`和其它选项。

> **注意**： `composer.json` 文件的 `extra` 下有一个 `writable` 项，是 Yii 自主添加的功能，它允许你在使用模板创建好应用后指定和设置文件级权限（按每个文件授权）。

接下来就可以真正随意地修改新应用的结构啦，别忘了相应地修改 readme 必读文件哦。


制作成包
--------------

建立一个git仓库，推送你的文件到那。若要开源，GitHub是托管它的最好选择。
若它要保持私有，也有 git 仓库能实现。（译者注：闭源仓库首推BitBucket，国内也有一些其他选择也不错）

然后必须注册你的包，公开的模板应注册到[packagist](https://packagist.org/)。

私有包比较难办但详细的注册方式可查阅[Composer 文档](https://getcomposer.org/doc/05-repositories.md#hosting-your-own)（英文文档，这里有[中文版](https://github.com/5-say/composer-doc-cn/blob/master/cn-introduction/05-repositories.md#Hosting-your-own)，有兴趣的同学可以参与该项目的翻译）。

使用它
------

这就成了，现在，你可以使用自己的模板创建项目：

```
php composer.phar create-project --prefer-dist --stability=dev mysoft/yii2-app-coolone new-project
```
