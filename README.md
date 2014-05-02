Yii2 开发及用户贡献文档中文本土化项目 
==============
项目仓库：http://c11n.yii2.cn/
在线文档：http://apidoc.yii2.cn/
基于原文：[Doc 2.0](http://www.yiiframework.com/doc-2.0/index.html)
Yii2官方：[yii2.cn](http://yii2.cn)

简介 Introduction
----------------

进行Yii2的官方文档，官方扩展的说明文件，源码注释等文档的汉化工作。目前，主要是翻译Yii2的官方文档。

结构与传送门 Structure and Shortcuts
----------------

***************************
/ root
  ├──/ guide-old 上一版本的手册文件
  ├──/ guide 手册
  │    ├──[英文原文请跳转：doc.yii2.cn](http://doc.yii2.cn)
  │    └──翻译好的文档。
  ├──/ [internal](internal/) 翻译的官方内部文档，包括如何向Yii官方贡献代码，如何创建新的文档等
  ├──/ [news](news/) 值得注意的新闻翻译，如。
  ├──/ [messages](messages/) Yii框架本身的国际化文件，比如错误反馈的国际化，有关中文在zh-CN文件夹
  ├──/ [api-doc:apidoc.yii2.cn](http://apidoc.yii2.cn)
  └── Root根目录下放置说明，授权，翻译必读，术语表等各种文件。
***************************

加入我们 How to contribute
----------------

我们有一个QQ群，用于日常沟通，项目组织，翻译心得与Yii2开发的交流等。
想学东西或者对自己英文有信心的同学可以先来[![加下QQ群](http://pub.idqqimg.com/wpa/images/group.png)343188481](http://url.cn/SIMfwO)。

具体如何翻译呢？请参阅我们的[翻译流程指南](guide/README.md)

引用Golang台湾的朋友们的一句话：

**由於目前尚在起步階段，請不吝提出各種疑問或建議。**

给各位大爷跪了！Orz！

跪求大家挑错啊！Orz！

另外，友情小提示：翻译前请善用rebase功能
```shell
git pull --rebase upstream master
```
或
```shell
git fetch upstream
git merge upstream/master --ff-only # fast-forwarding only.也可以不加，会使用自动合并功能，遇冲突会停止，等待手动处理冲突。
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

鸣谢 Credits
----------------

###官方：
* Qiang（薛强 中国） Yii 1/Yii 2项目的领导人，同时为旅美中国大陆人，与中国的 Yii 开发者关系密切，有困难可以找强哥。
* Samdark（Alexander Makarov 俄罗斯）Yii 2主要开发者之一，对于国际化及手册贡献良多。
* cebe （Carsten Brandt 德国）Yii 2主要开发者之一，负责开发了api-doc系统，对于文档系统的规范化进行了很多设定。官方文档的完全体样貌可以在[stuff.cebe.cc/yii2docs](http://stuff.cebe.cc/yii2docs)略窥一斑。

###项目组织：（除某不要脸的项目策划外均为字母顺序排列）
* qiansen1386（钱森，昵称：东方孤思子 项目策划）旅居新加坡的中国留学生。
* AbrahamGreyson（昵称：Abraham）
* aliciamiao（昵称：miao）
* drogjh
* fmalee（昵称：远方·轮回）
* qufo
* Simon
* SuperPuppet（阙俊榕 昵称: 无敌木木）特长卖萌，精神病晚期患者
* yzxh24
* 更多请参考 Contributor，以及 Transifex 的组织成员。请所有曾经付出贡献的童鞋，请自觉地把你的名字列在鸣谢表中，坦白从严，抗拒更严。

###特别鸣谢：
* ~~Transifex 提供的免费开源项目仓库~~
* GitHub 提供好用的在线平台
* 东方孤思子同学**花大价钱买的域名** (其实也就几十块美元。。。)
* 有道词典
* Google Translate
* 万恶的 Microsoft 提供的免费 Translation API 机翻额度
* 万恶的腾讯提供的免费 QQ 群
* Google
* Wikipedia
* 全国科学技术名词审定委员会，及英汉双解计算机词典

