#术语表Glossary


A
====================

action
------------------
动作

Active Record
------------------

Active Record（中文名：活动记录）是一种领域模型模式，特点是一个模型类对应关系型数据库中的一个表，而模型类的一个实例对应表中的一行记录。关系型数据库往往通过外键来表述实体关系，Active Record 在数据源层面上也将这种关系映射为对象的关联和聚集。Active Record 适合非常简单的领域需求，尤其在领域模型和数据库模型十分相似的情况下。

ActiveForm
------------------

活动表单

Argument
------------------

Argument与Parameter:
1. parameter是指函数定义中参数，而argument指的是函数调用时的实际参数。
2. 简略描述为：parameter=形参(formal parameter)， argument=实参(actual parameter)。
3. 在不很严格的情况下，现在二者可以混用，一般用argument，而parameter则比较少用。

一般说来，两个是可以互换的。但是 C 程序员的习惯是：parameter 是参数，而 argument 是参数的值。也就是说，函数原型的参数列表，是 parameter list，比如
int sum(int a, int b);
而当使用的时候
int sum;
sum = sum(10, 20);
10 和 20 则是 argument。
这个习惯也影响了很多其他语言的程序员。如果要混合两者的意义，一般用 argument，而 parameter 则比较少用。
argument 有的时候也被称作 actual parameter。
对应的中文术语是
parameter = 形参 (估计是「形式参数」简称)
argument = 实参 (估计是「实际参数」简称)
我想，将parameter译为“参数”，将argument译为“参数赋值”就比较清楚了。


alias路径别名
------------------

别名是被 Yii 使用来指向类或目录的字符串，格式如 @app/vendor 。


application
------------------

The application is the central object during HTTP request. It contains a number of components and with these is getting info from request and dispatching it to an appropriate controller for further processing.

The application object is instantiated as a singleton by the entry script. The application singleton can be accessed at any place via \Yii::$app.

assets
------------------

Asset refers to a resource file. Typically it contains JavaScript or CSS code but can be anything else that is accessed via HTTP.
资源文件，通常包括 JS 和 CSS 代码，可以通过 HTTP 被访问。

attribute
---------------

为区别 Property 和 Attribute ,我们将Attribute称为特性，特指对象属性，以区别于类的成员属性property

在OOA/OOD中的使用Attribute表示属性，指对象（Object）的特征（Feature）,是一个描述（或者说声明），描述对象在编译时或运行时的特征，属于面向对象分析与设计中的概念。

Property是指编程过程中的字段，也即类的成员变量（Member Variable），是指类向外提供的数据区域。property属于编程语言中的概念。

Field 字段，在指代类的成员变量时和 Property 通用。

基于目前最新的UML2.0规范：
* 总体上来说，Attribute是Property的子集，Property会在适当的时机表现为Attribute；
* Property出现在类图的元模型中，代表了Class的所有结构化特征；Attribute没有出现在元模型中，它仅仅在Class的概念中存在，没有相应的语法了；
* Property有详细的定义和约束，而Attribute没有详细的定义，因此也不能用OCL写出其约束。
* Property和Attribute都是M2层的概念。在M1层，它们的实例是具体类的属性；在M0层，它们的实例的实例是具体对象的槽中存储的值。

B
====================

bundle
------------------

资源包，包括资源集合及一个描述了资源依赖关系和资源清单的配置文件。

C
====================

configuration配置
------------------

Configuration may refer either to the process of setting properties of an object or to a configuration file that stores settings for an object or a class of objects.

D
====================


E
====================

eager loading
------------------
预先加载，和延迟加载（lazy loading）相反
预先加载通过级联查询读取主表同时将关联表相关数据也一并读回来，目的是减少对数据库的访问次数，提升应用的运行效率。

extension扩展
------------------

Extension is a set of classes, asset bundles and configurations that adds more features to the application.

G
====================

H
====================


I
====================

installation安装
------------------

Installation is a process of preparing something to work either by following a readme file or by executing specially prepared script. In case of Yii it's setting permissions and fullfilling software requirements.

I18N
-----------------
i18n（其来源是英文单词 internationalization的首末字符i和n，18为中间的字符数）是“国际化”的简称。

J
====================

K
====================

L
====================

M
====================

module子模块
------------------

Module is a sub-application which contains MVC elements by itself, such as models, views, controllers, etc. and can be used withing the main application. Typically by forwarding requests to the module instead of handling it via controllers.

N
====================

namespace命名空间
------------------

Namespace refers to a PHP language feature which is actively used in Yii2.

named arguments
----------------

具名实参

O
===================


P
====================

package包
------------------

See bundle.

Q
====================

R
====================

RBAC
-----------------
参考百度百科。

基于角色的访问控制（Role-Based Access Control）作为传统访问控制（自主访问，强制访问）的有前景的代替受到广泛的关注。在RBAC中，权限与角色相关联，用户通过成为适当角色的成员而得到这些角色的权限。这就极大地简化了权限的管理。在一个组织中，角色是为了完成各种工作而创造，用户则依据它的责任和资格来被指派相应的角色，用户可以很容易地从一个角色被指派到另一个角色。角色可依新的需求和系统的合并而赋予新的权限，而权限也可根据需要而从某角色中回收。角色与角色的关系可以建立起来以囊括更广泛的客观情况。

RBAC支持三个著名的安全原则：最小权限原则，责任分离原则和数据抽象原则。
RBAC认为权限授权实际上是Who、What、How的问题。在RBAC模型中，who、what、how构成了访问权限三元组,也就是“Who对What(Which)进行How的操作”。


S
=======================

schema
-----------------------

模式（schema）是用于 在一个大项目中的各个小项目。每个小项目的表, 放在各自的模式（schema）下面。这样, 遇到小项目里面有相同名字的表的话,不会发生冲突。例如一个公司的系统，里面分2个子系统,分别为财务系统和人力资源系统。这2个子系统, 共用一个数据库。那么财务系统的表,可以放在务的模式（schema）。人力资源系统的表，放在人力资源系统的模式里面。这2个子系统，能够互相访问对方的表但是又不因为表重名的问题影响对方。


scalar value 标量值
----------------------

标量，只有大小,没有方向的物理量。


T
====================

U
====================

V
====================


vendor
------------------

Vendor 是代码供应商（开发者个人或组织），以扩展、模块或第三方库等形式提供代码。

W
====================

widget
---------------
小部件，是一小块可以在任意一个基于HTML的Web页面上执行的代码，它的表现形式可能是视频，地图，新闻，小游戏等等。它的根本思想来源于代码复用，通常情况下，Widget的代码形式包含了HTML,JavaScript以及CSS等。

X
====================

Y
====================

Z
====================
