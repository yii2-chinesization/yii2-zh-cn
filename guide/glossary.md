#术语表Glossary


A
====================

alias路径别名
------------------

Alias is a string that's used by Yii to refer to the class or directory such as @app/vendor.

Active Record
------------------

Active Record（中文名：活动记录）是一种领域模型模式，特点是一个模型类对应关系型数据库中的一个表，而模型类的一个实例对应表中的一行记录。关系型数据库往往通过外键来表述实体关系，Active Record 在数据源层面上也将这种关系映射为对象的关联和聚集。Active Record 适合非常简单的领域需求，尤其在领域模型和数据库模型十分相似的情况下。

application
------------------

The application is the central object during HTTP request. It contains a number of components and with these is getting info from request and dispatching it to an appropriate controller for further processing.

The application object is instantiated as a singleton by the entry script. The application singleton can be accessed at any place via \Yii::$app.

assets
------------------

Asset refers to a resource file. Typically it contains JavaScript or CSS code but can be anything else that is accessed via HTTP.

B
====================

bundle
------------------

Bundle, known as package in Yii 1.1, refers to a number of assets and a configuration file that describes dependencies and lists assets.

C
====================

configuration配置
------------------

Configuration may refer either to the process of setting properties of an object or to a configuration file that stores settings for an object or a class of objects.

D
====================


E
====================

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

T
====================

U
====================

V
====================

vendor
------------------

Vendor is an organization or individual developer providing code in form of extensions, modules or libraries.

W
====================

X
====================

Y
====================

Z
====================
