Yii2 开发及用户贡献文档中文本土化项目 
==============
本仓库快捷传送门：http://c11n.yii2.cn/
临时在线文档：http://apidoc.yii2.cn/
基于原文：[Doc 2.0](http://www.yiiframework.com/doc-2.0/index.html)
Yii2 官方传送门：[yii2.cn](http://yii2.cn)

简介 Introduction
----------------

进行Yii2的官方文档，官方扩展的说明文件，源码注释等文档的汉化工作。目前，主要是翻译Yii2的权威指南。

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
  ├──/ [messages](messages/) Yii 框架本身的国际化文件，比如错误反馈的国际化，有关中文在zh-CN文件夹  
  ├──/ [framework](framework/) 框架文件，翻译注释文本  
  ├──/ [api-doc:http://apidoc.yii2.cn](http://apidoc.yii2.cn)  
  └── Root根目录下放置[说明](README.md)，[授权](LICENSE.md)，[翻译必读](translation-guide.md)，[术语表](glossary.csv)等各种文件  
***************************

加入我们 How to contribute
----------------

我们有一个QQ群，用于日常沟通，项目组织，翻译心得与Yii2开发的交流等。
想学东西或者对自己英文有信心的同学可以先来[![加下QQ群](http://pub.idqqimg.com/wpa/images/group.png)343188481](http://url.cn/SIMfwO)。

具体如何翻译呢？请参阅我们的[翻译流程指南](translation-guide.md)

引用Golang台湾的朋友们的一句话：

**由於目前尚在起步階段，請不吝提出各種疑問或建議。**

给各位大爷跪了！Orz！

跪求大家挑错啊！Orz！

另外，友情小提示：翻译前请善用rebase功能
```shell
git pull --rebase upstream master # rebase参数代指“变基”，在只更新不提交时很好用
```
或
```shell
git fetch upstream
git merge upstream/master --ff-only # fast-forwarding-only 可选，不加则进行合并操作，若失败需手动处理冲突。
```

协议 Licence
----------------

请前往：http://www.yiiframework.com/license/ 了解更多内容。
官方提供与用户贡献的所有文档资料基于[GNU Free Documentation License (GFDL)](http://www.gnu.org/copyleft/fdl.html) 。

###必须：

* 授予使用者与原协议完全相同的自由权（CopyLeft什么意思，你们都懂得）
* 通过一个指向源文件的连接明示原作者

###可以：

* 复制
* 修改
* 再分发