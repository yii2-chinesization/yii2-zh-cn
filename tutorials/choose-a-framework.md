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
代表 Yaf，Phalcon，代表人物鸟哥（Laruence，博客-[风雪之隅](www.laruence.com/)，PHP开发组中首位国人）。这类心法通常不寻求自身的炼体筑基（PHP），而是另辟蹊径
召唤异世界（C扩展）的力量灌注体身，原则上来说这就不是心法，而是召唤术。缺点就是C不是PHP，难以在PHP代码中继承与修改。它假定你们
在日常使用中不会修改框架本身，（实际上后期需要自定义某些功能时，经常可能修改到）因为可以放弃灵活性，故而可以在速度这一单项上，
让其他心法望尘莫及。

2. 至简流
比较早的代表有 CodeIgniter，可惜官方已经停运了，后来者在极简主义方面做得也有声有色，比如 [Toro](https://github.com/anandkunal/ToroPHP)，[Slim](https://github.com/codeguy/Slim)等。
简而言之，入门容易，定位灵活，但功能偏弱，另外缺少上层心法，难以进阶。用来入门挺好，可以学到基本的MVC概念。

3. 全栈流
代表ZF2，Symfony2，大而全，代码优美，文档丰富，尝试解决宇宙间的一切问题。最高级的心法，可惜上手偏难，运行效率低。

samdark: ZF 2 and Symfony2 are very "enterprise". This "enterprise" shit is too complex.
"ZF2 is too "scientific".You can write a thesis about ZF2 and its design. Don't get me wrong, I love best practices,
standards, design patterns, etc.
But ZF2 devs dive too deep into "science". Symfony2 is better but not enough.
What’s bad in these “enterprise” things?• Design-patterns oriented instead of practically oriented. Emphasizing on patterns.
• Easier to unit-test, harder to develop and learn.
• Almost impossible to delegate routine work to less competent developers w/o spending lots of time teaching them first.
• High risk for project owner.
That reminds me… Java past• “Their main thesis to support that complexity is… hold your breath…
 fasten your seat belts: if it were easier, more stupid people would be using it!. Ta-da!!”
“J2EE is no way simple. However the reality is simple: for J2EE to survive - we have to make it simple to build, deploy and manage”

有没有别的了呢？实践流，识贱是检验真理的唯一标准：Practical frameworks:
• Development should be straightforward. We’re not doing complex stuff for webapps most of the time.
• Easy learning.
• Less magic.
• Less configuration.
• As simple API as possible.

Reference：http://www.slideshare.net/samdark/yii-frameworks-and-where-php-is-heading-to

代表 Laravel 与 Yii

## 灵活变通 Switch When Needed