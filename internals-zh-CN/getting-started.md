开始使用 Yii2 进行开发
===================

从主仓库克隆代码，并在本地搭建尅有运行的程序，最好的方式就是使用 `yii2-dev` 的 Composer 包。

1. `git clone https://github.com/yiisoft/yii2-app-basic`.
2. 从克隆下来的代码中移除 `.git` 目录。
3. 修改 `composer.json`。移除所有稳定版依赖，修改为 `"yiisoft/yii2-dev": "*"`。
4. 执行 `composer create-project`命令。不要添加 `--prefer-dist` 参数因为它不会下载 Git 仓库内容。
5. 现在你就可以使用最新代码去开发了。

请注意， `yii2-dev` 扩展的依赖不是自动加载的。如果你想使用扩展，检查是否有相关依赖并将其添加到你的 `composer.json`。你可以通过执行 `composer show yiisoft/yii2-dev` 看到相关依赖。

如果你是核心开发者也无需额外步骤。可以直接在 `vendor/yiisoft/yii2-dev` 修改框架代码然后 push 到代码主仓库。

如果你不是核心开发者或者想使用自己的派生版来 pull request：

1. Fork `https://github.com/yiisoft/yii2` 并得到你自己的代码仓库地址，类似 `git://github.com/username/yii2.git`。
2. 编辑 `vendor/yiisoft/yii2-dev/.git/config`。修改 remote `origin` 地址为你的仓库地址。

```
[remote "origin"]
  url = git://github.com/username/yii2.git
```

> 提示：Fork 一个包和 Push 回你的 fork 并发起pull request给维护者，这样的工作流程对于所有通过 Composer 安装的扩展都一样。

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