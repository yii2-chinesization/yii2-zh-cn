翻译必读 Translating Guide
============================

目录
----

- [流程](#workflow)
- [注意事项](#rules)
- [工具](#tools)
- [扩展阅读](#others)

流程 <a name="workflow"/>
----
翻译流程如下：
**认领（更新README） → 翻译 → 建Pull Request（简称PR） / 直接提交 → 完成认领（更新README）→等待官方合并或其他评论**
[了解详情](#workflow-translate)

更新流程如下：
**认领（更新README） → 根据官方的对应文档 Commits History 更新翻译文档 → PR / 提交 → 完成认领（更新README）→等待官方合并或其他评论**
[了解详述](#workflow-update)

校对流程如下：

```php
while(看文档 x){
    if(发现不爽不顺不对的地方){
        点击 Edit 改了它（们）;
        PR / 提交;
    }
    if(文档看完了) break;
}
if(改进之后的文档 x 让人身心愉悦){更新README，将文档 x 的状态改为“已校对";}
```

>注意：校阅文档的更多说明请参考[校阅手册](translation-proofreading.md)，为了让你的翻译尽可能接近理想中校对后的状态，建议翻译人员也看一下。

###具体翻译/更新的流程 <a name="workflow-translate"/>

先简单介绍两个库：

* **官方库的Fork**：yii-chinesization/yii2 官方授权我们维护的框架库，用于收集大家翻译的文档，并随时反馈给官方。地址：`https://github.com/yii2-chinesization/yii2.git`
* **文档库**：yii2-chinesization/yii2-zh-cn 只用于存放翻译指南资料及老翻译的备份，**新翻译请别放在这里**。地址：`https://github.com/yii2-chinesization/yii2-zh-cn.git`


注意事项 <a name="rules"/>
-------

1. 更新README指更新[yii2-chinesization/yii2-zh-cn仓库下的guide/README](guide/README.md#shuoming)中文件目录里各个文件前，用于标注翻译状态的标签，详情请看[README前的说明](guide/README.md#shuoming)
2. 除非紧贴标点符号，否则`英文单词/数字`与`中文`之间应加一个空格。如：`我们说 Yii 2.0 而非Yii2.0。`
3. 可以翻译且易于理解的术语，尽量用翻译后的形式，除非无法翻译或英文已经约定俗成，如：`Cookie/Session`。前人已经翻译的术语，请参见[术语表（Glossary）](translation-glossary.md)。如果表中没有请添加您自己的翻译。如果对已有的术语翻译存在质疑，可以创建[issue](https://github.com/yii2-chinesization/yii2-zh-cn/issues)，或者在 QQ 群里沟通。总之，尽量避免“同词不同译”的发生。
4. 若原文中包含 ```*斜体*```，一律改为 ```**粗体**```：原因是中文是方块字，汉字本就没有倾斜风格，而雅黑等主流中文字体的倾斜效果都处理得不好，会影响阅读体验。


工具 <a name="tools"/>
----

1. 我们使用git作为版本控制，GitHub作为官方仓库。
   推荐使用 [SourceTree](http://www.sourcetreeapp.com/) 或 GitHub 官方客户端作为 Git 前端。关于Git的使用方法，可以看[廖雪峰大大的史上最浅显易懂的 Git 教程](http://www.liaoxuefeng.com/wiki/0013739516305929606dd18361248578c67b8067c8c017b000)或查阅[官方中文文档《Pro Git》](http://git-scm.com/book/zh)，或 [Google](https://google.com)/[StackOverflow](http://stackoverflow.com) 之。
2. 关于沟通交流，可选的渠道有：QQ群：343188481，[Issue](https://github.com/yii2-chinesization/yii2-zh-cn/issues)，[官方论坛中文板块](http://www.yiiframework.com/forum/index.php/forum/16-chinese/)
3. [微软术语搜索](http://www.microsoft.com/Language/zh-CN/Search.aspx?langID=zh-cn)：用于查询微软是如何翻译相关术语的。我们的术语表大量参考了微软家族的术语翻译，因为最全，最权威。
4. 同一文档的翻译人员和校对人员最好不同，以便核查勘误。
   
扩展阅读 <a name="others"/>
-------









*************************************************************
旧
*************************************************************
<a name="git-workflow"></a>
#### 参考 Git 操作流程 --重要！

有些朋友可能不太清楚如何用 Git 参与翻译工作，我这里写一个简单的流程，大家可以参考一下：

1. 首先 fork 这个项目以及由我们负责维护的 [Yii2 分支](https://github.com/yii2-chinesization/yii2/)
2. 把 fork 过去的两个项目也就是你名下的那两个项目分别 clone 到你的本地
3. 在命令行运行 `git branch temp` 来创建一个新分支，这里用 `temp`，你也可以用 `translating` 或其他任何名字
4. 运行 `git checkout temp` 来切换到新分支
5. 添加官方的远端库，命名为 upstream（也可以是其他名字），用来获取更新
    * 在**文档库的目录**内，运行 `git remote add upstream https://github.com/yii2-chinesization/yii2-zh-cn.git` 把汉化组的文档库添加为远端库
    * 在 **yii2 目录**内，运行 `git remote add upstream https://github.com/yii2-chinesization/yii2.git` 把汉化组的官方库添加为远端库
    * 例外：如果你同时 fork 了yiisoft/yii2，你可以把 yii2-chiesization 远端，命名为 `chinesization`，或其他你能明白的名字。这样修改以后请对应修改掉下面的 `upstream` 改为你命名的远端名称，如 `chinesization`

步骤1~5是一个初始化流程，只需要做一遍就行，之后请一直在 temp （或其他名字）分支进行修改。

6. 分别在两个目录内，运行 `git remote update` 更新两库
7. 分别在两个目录内，运行 `git fetch upstream master` 拉取两库的更新到本地
8. 分别在两个目录内，使用 `git checkout temp` 切换回你的日常分支后，运行 `git rebase upstream/master` 将两库的更新合并到你的分支

如果修改过程中我们的仓库（不管是文档库还是官方库）有了更新，在对应的库目录下重复6、7、8步即可。
也可以简写为 `git pull --rebase upstream master` 一条命令。或者你用 SourceTree 等 GUI 的话，在 push 面板下勾选用变基替代合并。也可以起到相同的作用，巧用变基，可以避免不必要的合并。

修改之后，首先 Push 到你的库，然后登录 GitHub，在你的库的首页可以看到一个 `pull request` 按钮，点击它，填写一些说明信息，
然后提交即可。

> 特别注意：在官方进行重大调整的时候，我们有可能会临时更改默认分支，请注意把master对应更换成新的默认分支。

如果没有直接修改权限，你需要创建 Pull Request 简称 PR。最好可以把相关PR都挂在同一个 issue 之下，便于交流。
如果你翻译地较多，在群里吱一声，就可以提升为写权限，这样就可以直接 push 了，即使是有权限也建议使用 PR 处理
大规模多次零散的翻译提交，这样管理和沟通都会方便。

<a name="detailed-workflow"></a>
#### 具体翻译流程 --极其重要！！

* 翻译前，请先在 **文档库** 认领并登记[原文日期](#get-date)，认领信息登记在[文档库/guide-zh-CN/README](guide-zh-CN/README.md)，将待翻译改为翻译中，后加原文日期和你的 GitHub ID。例如：`【翻译中-20140505-你的 GitHub 名】`。 **[点此了解原文日期的获取](#get-date)**
* 在官方库内的英文原版目录内，把你要翻译的文件的最新版本复制到（官方库内的，不是文档库）`guide-zh-CN`
目录内，确保 GitHub
里官方文件的最后修改日期（不是本地文件日期哦）和你认领时填写的编辑时间吻合。
* 翻译地过程中请多多参考术语表，这能大幅减少你寻求准确翻译的时间。如果术语表中没有的翻译，可以参考
[有道词典](http://dict.youdao.com/) 和 [微软术语搜索](http://www.microsoft.com/Language/zh-cn/Search.aspx)等工具。
* 翻译完成后提交给 `yii-chinesization/yii2` 官方库，别忘了更改文档库里 [README里的翻译状态](guide-zh-CN/README.md)，参考
[汉化进度](#tags) 章节。

认领机制
--------
<a name="README"></a>
### README

在文档库的 `guide` 目录内的 `README.md` 是整个文档的目录索引。[打开看下](https://github.com/yii2-chinesization/yii2-zh-cn/blob/master/guide-zh-CN/README.md)，你会发现，在每个条目的的前方会有一个标签，而后方则有着一些日期。他们是做神马的呢？

针对每一个不同的文件我们分别挂上一些标签，以及翻译时所参考的英文原文的版本日期。标签用来标注翻译的状态，日期用来在后续文档更新及审阅时，方便找到对应的英文版本，并根据 GitHub 所提供的 History 功能对照着修改我们的译文，使得我们的译文和英文始终保持对应状态。防止文档老旧，更新不及时的情况。

<a name="tags"></a>
#### 文档汉化进度分为以下几种状态：

等待认领：

- 【待翻译】任何翻译人员都可认领
- 【需更新】指官方文档已更新，需要翻译人员或校对进行更新，及翻译。

无需认领：

- 【翻译中】表示该文档已被某位翻译人员认领，正在翻译中，请等他完成再刊错。
- 【已完成】官方文档无更新，已完成翻译和校对的中文文档，表示该文档翻译结束。

翻译状态：

- 【未校对】粗翻完成后更改
- 【已更新】标明新增了内容，后面往往跟其他标签如：
    * 【已更新|未校对】更新且汉化完成，但未经严格校对
    * 【已更新|翻译中】更新大量内容，尚未完全汉化
- 【已完成】校对完毕更新为该状态，文档需要更新时更改标签为【需更新】

临时状态：

- 待定中 - 文章整体都被官方标记为TBD，也就是待定的部分。
- 编撰中 - 官方正在编撰或完善中的文档，官方没定稿，我们搞不了。

<a name="get-date"></a>
#### 原文的版本日期

没有原文的版本日期，就难以判别该文章需不需要与官方文档进行更新，也难以给读者一个信息时效性上的提醒。
所以需要在Readme和正文中加入文档的原文日期，是**`原文日期`**不是**`翻译成稿日期`**。

原文日期的获取，可以在 GitHub 的页面上查看，也可以使用 `git log` 查看你所选版本所对应的 last committed date。
```
git log ./path/file.md
```

写日期不写 hash 主要基于两点考虑：

1. 日期更容易修改，更不容易出错。
2. 日期更直观，能明确知道该文档是否需要更新，而不需搜索寻找复杂的 commits 列表。

缺点是，如果一天之内官方有多次更新，校对者需要检查当天的后几次 commits 是否也需要更新。

翻译技巧与注意事项
-----------------

>小技巧: **最好不要直接在原文上修改**：如果zh-CN文件夹里没有相应的文件请先复制原文进来。翻译的时候，最好一句一句地翻译，先翻译好中文再删除英文；
也可以复制一下英文原句，然后在克隆的句子上修改；也可以充分发挥大显示器的特长，左手原文右手译文。
这样可以省的翻译到一半突然想不起来英文原文，节省掉再去查找原文的时间，减少打扰，提高翻译质量和效率。

- 翻译时不必中英文一一对应，先读懂英文的意思，然后用中文表达相同或相近的意思即可。想象着我们自己就是手册的原作者，写出我们所想要表达的意思即可。有的时候，一一对应反而不好翻译。

- 翻译时可以善用工具，除了有道和 Google，参考[Yii 1.1 的手册翻译](http://www.yiiframework.com/doc/guide/1.1/zh_cn/index)也是不错的办法。可能有些文字，涉及较深的专业知识，其含义本身就算是中文也不太好理解。你可以选择跳过，也可以选择查阅相关维基百科，来搞明白这些概念，方便的话请随后把这些概念加入[术语表](#intro-glossary)。

- 粗翻时也无需太过注重信达雅的要求。这些部分可以在后续校对工作时，慢慢达到。关于信达雅的确切含义，请见后面校对篇[信达雅](translation-proofreading.md#xin-da-ya)部分的说明。


### 需特殊注意的文字

#### 维持原样
有些字符串是不应该被翻译的，比如：

1. 源代码。注：源代码我们不翻译，但是注释还是要翻译的，别漏了。源代码里面的部分内容被卖萌地翻译了。但是切记注意保持功能的正确性。
2. 众所周知的概念，如Codeception，Alias，AssetManager，这类概念仅在标题处注释其中文名称即可，具体翻译中可以不使用其中文名称，原因有二，其一，这些是 Yii
的独有概念，其在语境中的含义和在其他编程语言中可能不尽相同。其二，为了国际化的需要，由于工作量的原因，我们不太可能翻译世界上出现的每一篇有关
 Yii 的教程和心得分享。所以看懂原文就显得比较重要，而这些概念在有道和
 Google 上翻译的都是英文领域的概念，而非Yii语境下的概念，帮助大家对其英文原词建立相应的概念就显得比较重要。
3. Markdown语法所需特殊符号。[请参考校阅手册的 Markdown 部分](translation-proofreading.md#markdown)，以及 [Markdown
语法推荐标准](markdown-code-style.md)

#### URL

如果是原文中的URL是维基百科或PHP手册，且提供了相应的中文翻译，我们最好把URL改为中文版本页面的URL。

如果没有，则最好注明**英文**字样。

比如：

    [pivot table]: http://en.wikipedia.org/wiki/Pivot_table "Pivot table（数据透视表（中间表），英文，维基百科）"

#### 斜体字



附录
------------------
### 参考阅读

- [校对手册](translation-proofreading.md)：如果说翻译是从无到有，校对就是从有到优。读校对手册，就像作者要摸透编辑的心。
才能写出编辑满意的高质量文章。
- [术语表](translation-glossary.md) 对术语的翻译有任何意见都请随时建立issue大家交流讨论，我们也会不定期向全社会征集对于术语翻译的意见与建议。
- [Yii 文档风格指南](documentation_style_guide.md)：主要记述了官方编写文档时的考虑与原则，理解他们有利于我们理解官方的
叙述风格，增加翻译的准确性。
- [Markdown 编写规范（试行）](markdown-code-style.md)

