Bootstrap小部件
=================

创造性地，Yii内置提供[Bootstrap 3](http://getbootstrap.com/)标记和组件架​​构（也被称为"Twitter Bootstrap"）的支持。Bootstrap是一种性能优良，响应式的框架，可以大大加快您的开发客户端的过程。

Bootstrap的核心由两部分构成：

- CSS的基础定义，如珊格布局系统、排版、辅助类及响应式工具。
- 现成的组件，如菜单、分页、对话框、标签页等。

基础知识
------

有封装bootstrap基础到PHP代码中，但应用到HTML本身却是非常简单的。你可以在[bootstrap文档](http://getbootstrap.com/css/)找到有关使用基础样式的详细资料。Yii提供了一个简单的方法让你在网页中使用bootstrap资源，你只要在应用配置目录下的`AppAsset.php`文件中加入一行：

```php
public $depends = [
	'yii\web\YiiAsset',
	'yii\bootstrap\BootstrapAsset', // 就是这行了
	// 'yii\bootstrap\BootstrapThemeAsset' // 取消注释会将 bootstrap 2 的样式应用到 bootstrap 3
];
```

通过Yii的资源管理功能使用bootstrap可以让你最简化bootstrap资源(resources)，并且方便的与自己的资源结合。

Yii小部件
-----------

Yii将最具复合性的组件包装成小部件，以允许更强大的语法和与框架进行整合。
所有小部件都属于 `\yii\bootstrap` 命名空间：

- [[yii\bootstrap\Alert|Alert]]
- [[yii\bootstrap\Button|Button]]
- [[yii\bootstrap\ButtonDropdown|ButtonDropdown]]
- [[yii\bootstrap\ButtonGroup|ButtonGroup]]
- [[yii\bootstrap\Carousel|Carousel]]
- [[yii\bootstrap\Collapse|Collapse]]
- [[yii\bootstrap\Dropdown|Dropdown]]
- [[yii\bootstrap\Modal|Modal]]
- [[yii\bootstrap\Nav|Nav]]
- [[yii\bootstrap\NavBar|NavBar]]
- [[yii\bootstrap\Progress|Progress]]
- [[yii\bootstrap\Tabs|Tabs]]


直接使用Bootstrap目录的.less文件
-------------------------------------------

如果你想[在自己的less文件中直接引用Bootstrap css](http://getbootstrap.com/getting-started/#customizing)，您需要停止加载原本的bootstrap css文件。
你可以通过在[[yii\bootstrap\BootstrapAsset|BootstrapAsset]]里设置CSS属性为空做到这一点。
要实现该功能，你需要配置 `assetManager` 应用组件，如下所示：

```php
    'assetManager' => [
        'bundles' => [
            'yii\bootstrap\BootstrapAsset' => [
                'css' => [],
            ]
        ]
    ]
```
