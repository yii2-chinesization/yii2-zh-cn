表单使用
==================

> 注意：该章节还在开发中。

Yii 使用表单的主要方式是通过[[yii\widgets\ActiveForm]]。模型表单更推荐用这个方法。另外，在[[yii\helpers\Html]]有一些有用的方法，通常用于添加按钮、帮助表单的文本填充。

建立模型表单的第一步是定义模型，模型可以基于活动记录（Active Record）类或更普通的模型类。对这个登录例子来说，使用的是普通模型类：

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

以上代码中，[[yii\widgets\ActiveForm::begin()|ActiveForm::begin()]]不只建立了一个表单实例，还标记表单的开始位置。位于[[yii\widgets\ActiveForm::begin()|ActiveForm::begin()]]和[[yii\widgets\ActiveForm::end()|ActiveForm::end()]]之间的所有内容将用 `<form>` 标签包裹。像其他小部件可以指定一些选项一样，ActiveForm允许传递数组到 `begin` 方法来配置此小部件。该例中，一个外部的 CSS 类和可识别的 ID 被传递和使用到 `<form>` 闭合标签。

要建立表单元素和元素标记及任何 JS 验证，活动表单小部件的[[yii\widgets\ActiveForm::field()|ActiveForm::field()]]方法将被调用。直接 echo 该方法的结果是合格的（文本）输入。要自定义输出，可以链接其他方法到此调用：

```php
<?= $form->field($model, 'password')->passwordInput() ?>

// 或

<?= $form->field($model, 'username')->textInput()->hint('Please enter your name')->label('Name') ?>
```

以上代码将根据表单字段定义的模板创建所有 `<label>`, `<input>` 和其他标签。
要自己填写这些标签可以使用 `Html` 助手类。

如果想使用 HTML5 字段，可以直接定义输入类型如下：

```php
<?= $form->field($model, 'email')->input('email') ?>
```

> **提示**: 为给必填项的样式添加星号，可以使用以下 CSS ：
>
```css
div.required label:after {
    content: " *";
    color: red;
}
```

处理多个模型结合到一个表单
-------------------------------------------

有时必须处理单个表单的多个同类模型。例如，多个布置，其中每个布置以键值对存储并用 `Setting` 模型表示。以下代码展示 Yii 如何实现它：

以控制器动作开始：

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

以上代码从数据库检索模型时使用 `indexBy` 让数组以模型 ID 为索引。这些功能稍后将用于识别表单字段。 `loadMultiple` 用 POST 过来的表单数据填充多个模型，而 `validateMultiple` 一次验证所有模型。保存数据时要跳过验证通过传递`false` 参数给 `save` 。

现在是在 `update` 视图的表单：

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

这里为每个布置以值渲染名字和每个输入。添加适当的索引给输入名是重要的，因为 `loadMultiple` 决定哪些值填充哪些模型。
