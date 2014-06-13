Yii2 视图代码风格
====================

以下代码风格用于 Yii 2.x 核心和官方扩展中的视图文件。我们不强制你在自己的应用中使用此风格。自由决断，选你喜欢的。

```php
<?php
// 每个模板文件必须有 PHP 开始标签。开始标签后需要空一行。

// 这里描述通过控制器传过来的变量
/**
 * @var yii\base\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\Post[] $posts
 * @var app\models\ContactMessage $contactMessage
 */
// 描述完也要空一行

// 命名空间的类声明
use yii\helpers\Html;
use yii\widgets\ActiveForm;
// 下面也要空一行

// 设置上下文属性，调用 setter， 和做其它事。
$this->title = 'Posts';
?>
<!-- foreach，for，if等最好用单独的代码段 -->
<?php foreach ($posts as $post): ?>
    <!-- 注意缩进 -->
    <h2><?= Html::encode($post['title']) ?></h2>
    <p><?= Html::encode($post['shortDescription']) ?></p>
<!-- 如果使用了多个代码段， 应该用 `endforeach;`， `endfor;`， `endif;` 等代替 `}` -->
<?php endforeach; ?>

<!-- 小物件的调用可能在多个逻辑控制语句中 -->
<?php $form = ActiveForm::begin([
    'options' => ['id' => 'contact-message-form'],
    'fieldConfig' => ['inputOptions' => ['class' => 'common-input']],
]); ?>
    <!-- 注意缩进 -->
    <?= $form->field($contactMessage, 'name')->textInput() ?>
    <?= $form->field($contactMessage, 'email')->textInput() ?>
    <?= $form->field($contactMessage, 'subject')->textInput() ?>
    <?= $form->field($contactMessage, 'body')->textArea(['rows' => 6]) ?>

    <div class="form-actions">
        <?= Html::submitButton('Submit', ['class' => 'common-button']) ?>
    </div>
<!-- 结束小物件调用应该用单独一组 PHP 标签 -->
<?php ActiveForm::end(); ?>
<!-- 结尾必须多出一个换行符 -->

```
