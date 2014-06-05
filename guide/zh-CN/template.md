使用模板引擎
======================

Yii 默认使用PHP作为模板语言，但可以通过配置Yii来支持其他模版引擎，如[Twig](http://twig.sensiolabs.org/) 或 [Smarty](http://www.smarty.net/)。

`view` 组件负责渲染视图。您可以通过重新配置该组件的行为 `behavior` 来添加自定义的模板引擎：

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

以上代码配置了Smarty和Twig模板引擎，现在它们都是可用的。但是，为了让你的项目自动获取这些扩展文件，你还需要在你的`composer.json`文件的 `require` 部分添加如下代码：

```
"yiisoft/yii2-smarty": "*",
"yiisoft/yii2-twig": "*",
```
修改并保存文件后，您就可以通过运行命令 `composer update --preder-dist` 来安装扩展了。

Twig
----

要使用Twig，你需要创建一个以 `.twig` 为扩展名的模版文件（要使用其他扩展名，请在配置组件做相应的修改）。
不同于标准的视图文件，使用 Twig 需要在控制器调用方法`$this->render()` 或 `$this->renderPartial()` 包含(`include` )扩展：

```php
echo $this->render('renderer.twig', ['username' => 'Alex']);
```
。

### 新增方法

Yii在标准的Twig语法中增加了以下的构造：

```php
<a href="{{ path('blog/view', {'alias' : post.alias}) }}">{{ post.title }}</a>
```

`path()` 函数内部调用了Yii的 `Url::to()` 方法。

### 新增变量

Twig模板可以使用这些变量：

- `app`，相当于 `\Yii::$app`
- `this`, 相当于当前的 `View` 对象

### 全局

您可以设置应用配置文件的 `globals` 变量来添加全局辅助方法或全局变量值，也可以同时添加：


```php
'globals' => [
	'html' => '\yii\helpers\Html',
	'name' => 'Carsten',
],
```

配置完成后，就可以在模板中这样使用全局变量：

```
Hello, {{name}}! {{ html.a('Please login', 'site/login') | raw }}.
```

### 新增过滤器

配置应用配置文件的 `filters` 选项来添加额外的过滤器：

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

要使用Smarty，你需要创建以 `.tpl` 为扩展名的模版文件（要使用其他扩展名，请在配置组件中做相应修改）。不同于标准的视图文件，使用Smarty的时候你必须在控制器调用方法`$this->render()` 或 `$this->renderPartial()` `include`(包含) 扩展：

```php
echo $this->render('renderer.tpl', ['username' => 'Alex']);
```

### 新增方法

Yii添加了以下的构造到标准的Smarty语法中：

```php
<a href="{path route='blog/view' alias=$post.alias}">{$post.title}</a>
```

`path()` 函数内部调用了Yii的 `Url::to()` 方法。

### 新增变量

在Smarty模板中，您还可以使用这些变量：

- `$app`，相当于 `\Yii::$app`
- `$this`，相当于当前的 `View` 对象