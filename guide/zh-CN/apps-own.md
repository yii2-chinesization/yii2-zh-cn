创建自己的应用结构
=======================================

While [basic](apps-basic.md) and [advanced](apps-advanced.md) application templates are great for most of your needs
you may want to create your own application template to start your projects with.

Application templates are repositories containing `composer.json` and registered as Composer packages so you can make
any repository a package and it will be installable via `create-project` command.

Since it's a bit too much to start building your template from scratch it is better to use one of built-in templates
as a base. Let's use basic template.

从git克隆基础应用模版
Clone basic template repository from git
----------------------------------------

```
git clone git@github.com:yiisoft/yii2-app-basic.git
```

And wait till it's downloaded. Since we don't need to push our changes back to Yii's repository we delete `.git` and all
of its contents.

修改文件
Modify files
------------
现在，我们需要修改`composer.json`，改变`name`, `description`, `keywords`, `homepage`, `license`, `support`匹配你的新模板，调整 `require`, `require-dev`, `suggest`以及其它选项
Now we need to modify `composer.json`. Change `name`, `description`, `keywords`, `homepage`, `license`, `support`
to match your new template. Adjust `require`, `require-dev`, `suggest` and the rest of the options.

> **Note**: In `composer.json` file `writable` under `extra` is functionality added by Yii that allows you to specify
> per file permissions to set after an application is created using the template.

Next actually modify the structure of the future application as you like and update readme.


制作包
--------------

创建一个git仓库,并且把你的文件推送上去。
Create git repository and push your files there. If you're going to make it open source github is the best way to host it.
If it should remain private, any git repository would do.

然后，你需要去注册你的包，公开的包需要在packagist](https://packagist.org/).注册。

Then you need to register your package. For public templates it should be registered at [packagist](https://packagist.org/).

私有的包是比较狡猾的(尼玛这个要怎么翻译的好。。。)，他的注册方式在[Composer documentation](https://getcomposer.org/doc/05-repositories.md#hosting-your-own).有很好的解释
For private ones it is a bit more tricky but well defined in
[Composer documentation](https://getcomposer.org/doc/05-repositories.md#hosting-your-own).

使用
------

像这样，现在，你可以使用模板创建你的项目

```
php composer.phar create-project --prefer-dist --stability=dev mysoft/yii2-app-coolone new-project
```
