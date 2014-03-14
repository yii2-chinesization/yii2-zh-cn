Yii2 贡献者的 Git 工作流程
===================================

你想助 Yii2 一臂之力吗？太棒了！为了你的提交能更快被通过，请遵循下列步骤（前两步仅需做一次）。 如果你是 Git 和 Github 新人，也许想先看看 [Github 帮助(http://help.github.com/)， [尝试 Git](https://try.github.com)
或了解下 [Git 内部的数据模型](http://nfarina.com/post/9868516270/git-is-simpler)。

### 1. 在 Github 上 [Fork](http://help.github.com/fork-a-repo/) Yii 的代码仓库并克隆你的 Fork 至本地环境

```
git clone git@github.com:YOUR-GITHUB-USERNAME/yii2.git
```

如果你在 Linux 上通过 Github 设置 Git 遇到问题，或遇到“Permission Denied (publickey)”之类的错误，则你必须要[使用 Github 和 Git 安装](http://help.github.com/linux-set-up-git/)。

### 2. 将 Yii 代码主仓库添加到 git remote 里命名为“upstream”

进入到你克隆的 Yii 代码目录，通常来讲是“yii2”。然后键入以下命令：

```
git remote add upstream git://github.com/yiisoft/yii2.git
```

### 3. 确保已经为你正在工作的内容创建了一个 issue。

所有新特性和 bug 修复都应该有一个相关 issue 为讨论和文档提供单一参考点。花点儿时间浏览现有 issue 列表，查看你想要做的东西是否已经被提出，如果发现匹配项请在该 issue 留下评论，表明你想要做这项工作。如果没有发现匹配项，请为你的计划新建一个 issue。这样有助于团队成员评审你的建议，并在此期间提供你适当反馈。

> 如果是小更改或文档问题，则不需要新建一个 issue， 一个 pull request 即可。

### 4. 从 Yii 代码主仓库中 Fetch 最新的代码

```
git fetch upstream
```

你应该在每次开始贡献代码时都做到这一点，以保证你工作在最新的代码上。

### 5. 以当前 Yii 代码仓库的 master 分支为基础为你添加的特性新建一个分支

> 这很重要，因为当你使用 master 分支时，将不能提交超过一个 pull request 请求。

每个 bug 修复和更改都应该放进单独的分支。分支名应该是描述性的，并且以相关 issue 编号为开头。如果你没修复任何特定的 issue，就忽略数字。
例如：

```
git checkout upstream/master
git checkout -b 999-name-of-your-branch-goes-here
```

### 6. 施展才华，编写代码

确保代码运行正常：）

始终欢迎对代码进行单元测试。测试和良好的代码覆盖率能极大减轻审验你所贡献的代码的任务。同时也接受解决了 issue 描述问题但单元测试失败的代码。

### 7. 更新 CHANGELOG 文件

编辑 CHANGELOG 文件加入你的更改，你应该把它们写在文件最上方的“Work in progress”页头下面，这个更改记录文件看起来应该是这样：

```
Bug #999: a description of the bug fix (Your Name)
Enh #999: a description of the enhancement (Your Name)
```

`#999` 是 `Bug` 或 `Enh` 涉及到的 issue 编号。更改记录文件应该由类型（`Bug`, `Enh`）进行分组并以 issue 编号进行排序。

对于微小的更改，譬如语法修正和文档修正，则不需要更新 CHANGELOG 文件。

### 8. Commit 你的更改

把你想要 Commit 的文件/更改添加到暂存区 [Git 暂存区](http://gitref.org/basic/#add) ：

```
git add path/to/my/file.php
```

你可以使用 `-p` 参数去只提交更改的文件的一部分。

Commit 你的更改需要附加一段描述性信息。该信息要确保使用 `#xxx` 包含了 issue 编号这样 Github 就可以自动把你的 Commit 链向对应 issue。

```
git commit -m "A brief description of this change which fixes #42 goes here"
```

### 9. 在你的分支 Pull 下 Yii 代码的最新版本
`960
```
git pull upstream master
```

这将保证在你发起 pull request 前你的代码是最新版的。如果 pull 时有合并冲突，你应该修复它们并再次 commit 更改。这将保证 Yii 团队能够点击一下便轻松合并你的代码。

### 10. 已解决的任何冲突，都要把你的代码 push 到 Github

```
git push -u origin 999-name-of-your-branch-goes-here
```

使用 `-u` 参数确保你的分支能自动从 Github 分支上 pull 和 push。这样当你下次输入 `git push` 它将知道从往哪里 push。

### 11. 针对 upstream 发起 [pull request](http://help.github.com/send-pull-requests/)

在你的 Github 仓库点击“Pull Request”，在右侧选择分支并在评论栏输入一些细节。在任何地方输入评论 `#999` 链向 pull request， 999是 issue 编号。

> 请注意每个 pull-request 应该解决单个问题。

### 12. 有人会审验你的代码

有人会审验你的代码，你可能想要进行一些修改，如果这样请返回步骤 #6（如果你当前的 pull request 仍然开启那么不需要新建另一个）。如果你的代码被接受它将会被合并到主分支，并成为下一个 Yii 发行版的一部分。如果没有，也别沮丧，人们各有所需，Yii 并不能兼顾一切，你的代码将会一直在 Github 上为需要它的人做参考。

### 13. 收尾工作

代码被接受或拒绝后你就可以删除工作过的本地分支和远程分支 `origin` 了。

```
git checkout master
git branch -D 999-name-of-your-branch-goes-here
git push origin --delete 999-name-of-your-branch-goes-here
```

### 请注意：

为了尽早进行回归测试，每次代码被合并进 Yii 代码库都将由 [Travis CI](http://travis-ci.org) 进行自动化测试。 作为核心团队不希望这项服务负载过重，如果 pull request 符合下述条件，合并描述将被添加 [`[ci skip]`](http://about.travis-ci.org/docs/user/how-to-skip-a-build/) 标签用以忽略本次测试：

* 仅仅影响 Javascript， CSS 或图片文件，
* 更新了文档，
* 仅修改了固定的字符（如翻译文本的更新）

这些将会节约 Travis 从自动测试，到执行那些未被测试过的代码的时间。

### 命令概览 (针对高级贡献者)

```
git clone git@github.com:YOUR-GITHUB-USERNAME/yii2.git
git remote add upstream git://github.com/yiisoft/yii2.git
```

```
git fetch upstream
git checkout upstream/master
git checkout -b 999-你的分支名字

/* 施展才华，编写代码 */

git add path/to/my/file.php
git commit -m "一个关于修复了 #42 问题的简要说明"
git pull upstream master
git push -u origin 999-你的分支名字
```
