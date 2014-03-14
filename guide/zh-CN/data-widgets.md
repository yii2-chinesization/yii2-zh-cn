数据小部件
============

列表视图(ListView)
--------
TBD


详情视图(DetailView)
----------

DetailView用于显示单个 [[yii\widgets\DetailView::$model|model]] (模型)数据的详细信息。

它最适合用于显示一个模型的常规格式（如在一个表格中将模型的每个属性显示为一行）。
该模型可以是继承自 [[\yii\base\Model]] 或关联数组的任一个实例。

DetailView使用 [[yii\widgets\DetailView::$attributes]] 属性来决定应该显示模型的哪些属性，以及它们要如何格式化。

DetailView的一个典型用法如下：

```php
echo DetailView::widget([
	'model' => $model,
	'attributes' => [
		'title',             // title attribute (in plain text)
		'description:html',  // description attribute in HTML
		[                    // the owner name of the model
			'label' => 'Owner',
			'value' => $model->owner->name,
		],
	],
]);
```