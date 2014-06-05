主题
=======

主题是视图和布局文件目录。渲染视图时，激活主题的每个文件将覆盖应用中对应的视图或布局文件。
单个应用可以使用多个主题，每个主题可以提供完全不同的视觉效果。
任何时候都只有一个主题被激活。

> 注意: 主题通常不需要单独发布，因为视图是和应用捆绑的。如果你想重新发布定制的界面外观，可以使用[资源包](assets.md)中的CSS和JavaScript文件替代。

配置激活主题
-------------------------

主题配置定义在应用的`view`组件中。所以，激活主题需要在应用的配置文件里进行如下设置：


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

上例中，`pathMap` 路径图定义了查找视图文件的路径，而`baseUrl` 则定义了被这些文件所引用的资源的根URL(base URL)。
例如，如果`pathMap`为`['/web/views' => '/web/themes/basic']`，那么已激活主题的应用视图文件`/web/views/site/index.php`就相应的变成`/web/themes/basic/site/index.php` 视图文件了。

使用多重路径
--------------------

可以把多个主题路径映射到同一个视图路径。例如，

```php
'pathMap' => [
	'/web/views' => [
		'/web/themes/christmas',
		'/web/themes/basic',
	],
]
```

上例，视图会先搜索`/web/themes/christmas/site/index.php`文件，如果该文件不存在，则搜索`/web/themes/basic/site/index.php`文件。如果还没找到，应用视图文件将被引用。

在你想临时或是有条件的覆盖一些视图时，这个功能会非常有用。