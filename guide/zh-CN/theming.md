主题
=======

一个主题是一个视图和布局文件的目录。渲染时，主题中的每个文件将覆盖应用中对应的文件。
单个应用可以使用多个主题，每一个都提供完全不同的体验。
任何时候都只有一个主题可以被激活。

> 提示: 主题通常不意味着要重新分配，因为视图是应用特定的。(Themes usually do not meant to be redistributed since views are too application specific)。如果你想
再分别的定制外观和感觉，可以尝试使用[资源包](assets.md)中的CSS和JavaScript文件替代(If you want to redistribute customized look and feel consider CSS and JavaScript files in form of [asset bundles](assets.md) instead)。

配置当前主题
-------------------------

主题配置通过应用的`view`组件来定义。因此，你需要在你的应用的配置文件里进行设置：


```php
'components' => [
	'view' => [
		'theme' => [
			'pathMap' => ['@app/views' => '@webroot/themes/basic'],
			'baseUrl' => '@web/themes/basic',
		],
	],
],
```

在上面的例子中，`pathMap`定义了去哪里查找视图文件，而`baseUrl`则定义了引用资源的基础URL(base URL)。
例如，如果`pathMap`为`['/web/views' => '/web/themes/basic']`，那么主题原本的视图则由引用`/web/views/site/index.php`文件变成引用`/web/themes/basic/site/index.php`文件。

使用多重路径
--------------------

主题可以由一个路径映射到到多个路径。例如，

```php
'pathMap' => [
	'/web/views' => [
		'/web/themes/christmas',
		'/web/themes/basic',
	],
]
```

在这个例子中，视图会先去搜索`/web/themes/christmas/site/index.php`文件，如果该文件不存在，则搜索`/web/themes/basic/site/index.php`文件。如果还没找到，应用中的视图文件将被引用。

在你想临时或是有条件的覆盖一些视图时，这个功能会非常有用。