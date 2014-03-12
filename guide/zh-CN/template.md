使用模板引擎
======================

默认情况下，Yii使用PHP作为模板语言，但可以通过配置让Yii支持其他模版渲染，如[Twig](http://twig.sensiolabs.org/) 或是 [Smarty](http://www.smarty.net/)。

`view` 组件负责渲染视图。您可以在此添加一个自定义的模板引擎配置以改变组件的行为：

```php
[
	'components' => [
		'view' => [
			'class' => 'yii\web\View',
			'renderers' => [
				'tpl' => [
					'class' => 'yii\smarty\ViewRenderer',
					//'cachePath' => '@runtime/Smarty/cache',
				],
				'twig' => [
					'class' => 'yii\twig\ViewRenderer',
					//'cachePath' => '@runtime/Twig/cache',
					//'options' => [], /*  Array of twig options */
					'globals' => ['html' => '\yii\helpers\Html'],
				],
				// ...
			],
		],
	],
]
```

在上面的代码中，无论是Smarty还是Twig都配置完成，它们可以通过视图文件激活。但是，为了让这些扩展应用到你的项目，你还需要在你的`composer.json`文件中添加如下代码：

```
"yiisoft/yii2-smarty": "*",
"yiisoft/yii2-twig": "*",
```
该代码要被添加到 `composer.json` 的 `require` 部分。修改并保存文件后，您就可以通过命令运行 `composer update --preder-dist` 安装扩展了。

Twig
----

要使用Twig，你需要创建一个以 `.twig` 为扩展名的模版文件（要使用其他扩展名，请在配置组件中做相应修改）。
不同于标准的视图文件，使用Smarty的时候你必须在你的控制器中扩展 `$this->render()` 或是 `$this->renderPartial()` 的调用：

```php
echo $this->render('renderer.twig', ['username' => 'Alex']);
```

＃＃＃附加方法

Yii添加了以下的构造到标准的Twig语法中：

```php
<a href="{{ path('blog/view', {'alias' : post.alias}) }}">{{ post.title }}</a>
```

在内部，`path()` 函数调用的是Yii的 `Url::to()` 方法。

＃＃＃附加变量

在Twig模板中，你还可以使用这些变量：

- `app`，相当于 `\Yii::$app`
- `this`, 相当于当前的 `View` 对象

＃＃＃全局

您可以在应用配置的 `globals` 变量中添加全局的辅助方法或是指定值。您可以同时定义Yii的辅助类和你自己的变量：


```php
'globals' => [
	'html' => '\yii\helpers\Html',
	'name' => 'Carsten',
],
```

配置完成后，您就可以以如下方式在你的模板中使用全局变量：

```
Hello, {{name}}! {{ html.a('Please login', 'site/login') | raw }}.
```

＃＃＃附加过滤器

可以在应用的配置文件的 `filters` 选项中添加额外的过滤器：

```php
'filters' => [
	'jsonEncode' => '\yii\helpers\Json::encode',
],
```

然后在模板中可以使用：

```
{{ model|jsonEncode }}
```


Smarty
------

要使用Smarty，你需要创建一个以 `.tpl` 为扩展名的模版文件（要使用其他扩展名，请在配置组件中做相应修改）。不同于标准的视图文件，使用Smarty的时候你必须在你的控制器中扩展 `$this->render()` 或是 `$this->renderPartial()` 的调用：

```php
echo $this->render('renderer.tpl', ['username' => 'Alex']);
```

＃＃＃附加方法

Yii添加了以下的构造到标准的Smarty语法中：

```php
<a href="{path route='blog/view' alias=$post.alias}">{$post.title}</a>
```

在内部，`path()` 函数调用的是Yii的 `Url::to()` 方法。

＃＃＃附加变量

在Smarty模板中，您还可以使用这些变量：

- `$app`，相当于 `\Yii::$app`
- `$this`，相当于当前的 `View` 对象