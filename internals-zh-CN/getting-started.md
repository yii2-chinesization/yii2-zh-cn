开始使用 Yii2 进行开发
=====================================

从主仓库克隆代码，并在本地搭建尅有运行的程序，最好的方式就是使用 `yii2-dev` 的 Composer 包。

1. `git clone git@github.com:yiisoft/yii2-app-basic.git`.
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

An Alternative way
------------------

1. Clone your fork of yii2 `git clone git@github.com:<yourname>/yii2`.
2. Change into the repo folder `cd yii2`.
3. run `./build/build app/link basic` to install composer dependecies for the basic app.
   This command will install foreign composer packages as normal but will link the yii2 repo to
   the currently checked out repo, so you have one instance of all the code installed.
4. Do the same for the advanced app if needed: `./build/build app/link advanced`
   This command will also be used to update dependecies, it runs `composer update` internally.
5. Now you have a working playground for hacking on Yii 2.

You may also add the yii2 upstream repo to pull the latest changes:

```
git remote add upstream https://github.com/yiisoft/yii2.git
```

### Unit tests

To run the unit tests you have to install composer packages for the dev-repo.
Run `composer update` in the repo root directory to get the latest packages.

You can now execute unit tests by running `./vendor/bin/phpunit`.

You may limit the tests to a group of tests you are working on e.g. to run only tests for the validators and redis
`./vendor/bin/phpunit --group=validators,redis`.

### Extensions

To work on extensions you have to install them in the application you want to try them in.
Just add them to the `composer.json` as you would normally do e.g. add `"yiisoft/yii2-redis": "*"` to the
`require` section of the basic app.
Running `./build/build app/link basic` will install the extension and its dependecies and create
a symlink to `extensions/redis` so you are not working the composer vendor dir but the yii2 repo directly.
