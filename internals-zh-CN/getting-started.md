开始进行 Yii2 开发
================

1. 克隆你 fork 的 Yii2 `git clone git@github.com:<yourname>/yii2.git`。
2. 移动到你的仓库目录 `cd yii2`.
3. 运行 `./build/build app/link basic` 安装基础应用模版的 composer 依赖。
   该命令会照常安装所有外来的 composer 依赖，但同时它会把 yii2 仓库跟当前检出的仓库链接起来，这样你就有了一个安装了所有代码的实例。
4. 如果你需要的话，安装 advanced 高级应用模版也是一样的：`./build/build app/link advanced`
   该命令同样会用于更新依赖包，它会在内部运行 `composer update`。
5. 现在你就有了一个可用于鼓♂捣 Yii 2 的运行试验场了.

你也可以添加 yii2 的上游仓库为远端，以便随时拉取最新改动：

```
git remote add upstream https://github.com/yiisoft/yii2.git
```

请参考 [Yii2 贡献者的 Git 工作流程](git-workflow.md) 获知关于 pull request 的详情。

单元测试
--------

要运行单元测试，你必须先安装 dev-repo 版本的那些 composer 包。在仓库根目录下运行 `composer update` 以获得相关软件包的最新版本。

此时你就可以通过运行 `phpunit` 来执行单元测试。

你可以限制执行测试的组别。比如，只执行与验证器和 redis 相关的测试，可以用如下命令
`phpunit --group=validators,redis`。

扩展
--------

要从事扩展方面的工作，你需要先把他们安装到某个你想用来试用他们的应用程序里。
只要如常地把它们添加到 `composer.json` 文件中即可。举例来说，你可以把 `"yiisoft/yii2-redis": "*"` 添加到基础模板应用的
`require` 板块下。

运行 `./build/build app/link basic` 会安装该扩展及其相关依赖包，并创建一个软连接到 `extensions/redis`
，这样你就可以直接操作 Yii2 仓库而不会是 composer 的供应商目录。

应用程序的功能测试及验收测试
-----------------------

查看 `apps/advanced/tests/README.md` 以及 `apps/basic/tests/README.md` 了解更多关于如何运行 Codeception 测试的相关内容。