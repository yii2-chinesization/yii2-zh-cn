#术语表Glossary


A
====================

alias路径别名
------------------

Alias is a string that's used by Yii to refer to the class or directory such as @app/vendor.

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

E
====================

extension扩展
------------------

Extension is a set of classes, asset bundles and configurations that adds more features to the application.

I
====================

installation安装
------------------

Installation is a process of preparing something to work either by following a readme file or by executing specially prepared script. In case of Yii it's setting permissions and fullfilling software requirements.

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

P
====================

package包
------------------

See bundle.

V
====================

vendor
------------------

Vendor is an organization or individual developer providing code in form of extensions, modules or libraries.