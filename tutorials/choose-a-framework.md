如何理性选择框架 Choose a Framework Wisely
=========================

PHP 的创始人曾说过：

Creator of PHP:

> *“有勇气在合适的地方，大胆使用PHP；有气魄在不合适的地方，果断放弃PHP；有智慧区别这两种应用场景”*（其实是[知乎](http://zhi.hu/Ttzf)）

> *"Use PHP Only When You Need it - Rasmus Lerdorf"*.

同理可得，某一个框架没有办法包打天下的。 *（Yii 是个例外，Yii 是个例外，Yii 是个例外）*

Same idea, Trying to use One Framework for everything sometimes maybe not efficient. *(Yii can, Yii can, Yii can)*

怎样选择一个框架呢？请看下文

We'll explain how to choose to fit your need?

## 入门心法 Your First Framework
对于每一个蜀山派弟子而言，师父都会带他们去藏书阁选择一个入门心法，以为筑基之用。

For your first Framework, how to choose is really personal.

弟子们在面对琳琅满目的发光玉简之时，心头多少会有些许忐忑。贫道今日为尔等一一解析

<En WIP>

首先先要讲关于心法的几大流派：1. 追求急速 2. 追求至简 3.追求全面

<En WIP>

1. 速度流
代表 Yaf，Phalcon，代表人物鸟哥（风雪之隅 Laruence，PHP开发组中首位国人）。这类心法通常不寻求自身的炼体筑基（PHP），而是另辟蹊径
召唤异世界（C扩展）的力量灌注体身，原则上来说这就不是心法，而是召唤术。缺点就是C不是PHP，难以在PHP代码中继承与修改。它假定你们
在日常使用中不会修改框架本身，（实际上后期需要自定义某些功能时，经常可能修改到）因为可以放弃灵活性，故而可以在速度这一单项上，
让其他心法望尘莫及。

2. 至简流
代表 CodeIgniter，额官方已经停运了。简而言之，入门容易，但缺少上层心法。用来入门挺好，可以学到基本的MVC概念，可惜不更新了。

3. 全栈流
代表ZF2，Symfony2，大而全，代码优美，文档丰富，尝试解决宇宙间的一切问题。最高级的心法，可惜上手偏难，运行效率低。

## 灵活变通 Switch When Needed