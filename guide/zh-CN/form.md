表单使用
==================

Yii 使用表单的主要方式是通过[[yii\widgets\ActiveForm]]。这个方法对模型表单是更好选择。另外，在[[yii\helpers\Html]]有一些有用的方法，通常用于添加按钮、帮助表单的文本填充。

When creating model-based forms, the first step is to define the model itself. The model can be either based upon the
Active Record class, or the more generic Model class. For this login example, a generic model will be used:
建立模型表单的第一步是定义模型，模型可以基于活动记录类或更普通的模型类。对这个登录例子来说，使用的是普通模型类：

```php
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;

    /**
     * @return array 返回验证规则数组
     */
    public function rules()
    {
        return [
            // 用户名和密码都是必填项
            [['username', 'password'], 'required'],
            // 密码将被 validatePassword() 方法验证
            ['password', 'validatePassword'],
        ];
    }

    /**
     * 验证密码
     * 该方法是密码验证的内置方法。
     */
    public function validatePassword()
    {
        $user = User::findByUsername($this->username);
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError('password', '错误的用户名或密码。');
        }
    }

    /**
     * 给定用户名和密码的用户登录
     * @return boolean 返回该用户是否登录成功的布尔值。
     */
    public function login()
    {
        if ($this->validate()) {
            $user = User::findByUsername($this->username);
            return true;
        } else {
            return false;
        }
    }
}
```

控制器传递模型实例到视图，其中的活动表单（ Active Form ）小部件将被应用：

```php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['class' => 'form-horizontal'],
]) ?>
    <?= $form->field($model, 'username') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
<?php ActiveForm::end() ?>
```

以上代码中，[[yii\widgets\ActiveForm::begin()|ActiveForm::begin()]]不只是建立一个表单实例，还标记表单的开始位置。位于[[yii\widgets\ActiveForm::begin()|ActiveForm::begin()]]和 [[yii\widgets\ActiveForm::end()|ActiveForm::end()]]之间的所有内容将用 `<form>` 标签包裹。像其他小部件可以指定一些选项一样，ActiveForm允许传递数组到 `begin` 方法来配置此小部件。该例中，一个外部的 CSS 类和可识别的 ID 被传递和使用到 `<form>` 闭合标签。

In order to create a form element in the form, along with the element's label, and any application JavaScript validation,
the [[yii\widgets\ActiveForm::field()|ActiveForm::field()]] method of the Active Form widget is called.
When the invocation of this method is echoed directly, the result is a regular (text) input.
To customize the output, you can chain additional methods to this call:
要建立表单元素，使用元素

```php
<?= $form->field($model, 'password')->passwordInput() ?>

// or

<?= $form->field($model, 'username')->textInput()->hint('Please enter your name')->label('Name') ?>
```

This will create all the `<label>`, `<input>` and other tags according to the template defined by the form field.
To add these tags yourself you can use the `Html` helper class. The following is equivalent to the code above:

```php
<?= Html::activeLabel($model, 'password') ?>
<?= Html::activePasswordInput($model, 'password') ?>
<?= Html::error($model, 'password') ?>

or

<?= Html::activeLabel($model, 'username', ['label' => 'name']) ?>
<?= Html::activeTextInput($model, 'username') ?>
<div class="hint-block">Please enter your name</div>
<?= Html::error($model, 'username') ?>
```

If you want to use one of HTML5 fields you may specify input type directly like the following:

```php
<?= $form->field($model, 'email')->input('email') ?>
```

> **Tip**: in order to style required fields with asterisk you can use the following CSS:
>
```css
div.required label:after {
    content: " *";
    color: red;
}
```

Handling multiple models with a single form
-------------------------------------------

Sometimes you need to handle multiple models of the same kind in a single form. For example, multiple settings where
each setting is stored as name-value and is represented by `Setting` model. The
following shows how to implement it with Yii.

Let's start with controller action:

```php
namespace app\controllers;

use Yii;
use yii\base\Model;
use yii\web\Controller;
use app\models\Setting;

class SettingsController extends Controller
{
    // ...

    public function actionUpdate()
    {
        $settings = Setting::find()->indexBy('id')->all();

        if (Model::loadMultiple($settings, Yii::$app->request->post()) && Model::validateMultiple($settings)) {
            foreach ($settings as $setting) {
                $setting->save(false);
            }

            return $this->redirect('index');
        }

        return $this->render('update', ['settings' => $settings]);
    }
}
```

In the code above we're using `indexBy` when retrieving models from database to make array indexed by model ids. These
will be later used to identify form fields. `loadMultiple` fills multiple modelds with the form data coming from POST
and `validateMultiple` validates all models at once. In order to skip validation when saving we're passing `false` as
a parameter to `save`.

Now the form that's in `update` view:

```php
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();

foreach ($settings as $index => $setting) {
    echo Html::encode($setting->name) . ': ' . $form->field($setting, "[$index]value");
}

ActiveForm::end();
```

Here for each setting we are rendering name and an input with a value. It is important to add a proper index
to input name since that is how `loadMultiple` determines which model to fill with which values.
