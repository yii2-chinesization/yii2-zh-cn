Yii2 开发及用户贡献文档中文本土化组织
==============
进行Yii2的官方文档，官方扩展的说明文件，源码注释等文档的汉化工作。目前，主要是翻译Yii2的权威指南。
本仓库快捷传送门：[c11n.yii2.cn](http://c11n.yii2.cn/)，
临时在线文档：[apidoc.yii2.cn](http://apidoc.yii2.cn/)，
基于原文：[Doc 2.0](http://www.yiiframework.com/doc-2.0/index.html)，
Yii2 官方传送门：[yii2.cn](http://yii2.cn)。

结构与传送门 Structure and Shortcuts
----------------
***************************
/ root  
  ├──/ guide-zh-CN 权威指南  
  │    ├──[英文原文请跳转：doc.yii2.cn](http://doc.yii2.cn)  
  │    └──[目录及翻译状态](guide-zh-CN/README.md)  
  ├──/ guide-old 上一版本的权威指南文件  
  ├──/ [internals-zh-CN](internals-zh-CN/) 翻译的官方内部文档，包括如何向Yii官方贡献代码，如何创建新的文档等  
  ├──/ [news](news/) 值得注意的新闻翻译，如：发行注记等  
  ├──/ [messages](messages/) Yii 框架本身的国际化文件，比如各个语言的错误描述，有关中文在zh-CN文件夹
  ├──/ [framework](framework/) 框架文件，翻译注释文本  
  ├──/ [api-doc:http://apidoc.yii2.cn](http://apidoc.yii2.cn)  
  └── Root根目录下放置[说明](README.md)，[授权](LICENSE.md)，[翻译手册](translation-guide.md)，[校对手册](translation-proofreading.md)
  [术语表](translation-glossary.md)等各种文件
***************************

加入我们 How to contribute
----------------

**如果你想加入 Yii2 的大家庭，参与到汉化项目中，交流翻译心得与Yii2开发的经验等，可以先来[![加下QQ群](http://pub.idqqimg.com/wpa/images/group.png)343188481](http://url.cn/SIMfwO)。**

具体如何翻译呢？请参阅我们的[翻译手册](translation-guide.md)与[校对手册](translation-proofreading.md)

引用Golang呆湾的朋友们的一句话：

**由於目前尚在起步階段，請不吝提出各種疑問或建議。**

各位大爷，有空帮个忙，没空捧个场啦，跪求大家参与，分享，挑错啊！orz orz orz

参与流程 Workflow
--------
更丰富的流程正在讨论整理中，请耐心等待……

获取最新的档案信息可以：
```shell
git pull --rebase upstream master
# rebase参数代指“变基”，在只更新不提交时很好用
```
或
```shell
git fetch upstream
git merge upstream/master --ff-only
# 可选参数：fast-forwarding-only，不加则进行合并操作，若失败需手动处理冲突。
```

协议 Licence
----------------

总体上，官方提供与用户贡献的所有文档资料都基于[GNU Free Documentation License (GFDL)](http://www.gnu.org/copyleft/fdl.html)
简单的说明请见 [LICENSE.md](LICENSE.md)。
对于 Yii 的各项协议的更多详细说明，请前往 [www.yiiframework.com/license](http://www.yiiframework.com/license/) 了解更多内容。
